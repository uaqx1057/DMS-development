<?php

namespace App\Livewire\Fuel;
use App\Models\FuelRequestReject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Livewire\Component;
use App\Models\Fuel;
use App\Models\Vehicle;
use App\Traits\DataTableTrait;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Driver;
use App\Models\AssignDriver;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\FuelRequestApproval;
use App\Models\FuelExpense;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;


#[Title('Fuel List')]
class Index extends Component
{
     public string $activeTab = 'fuel_request'; // default tab
     use WithFileUploads;

     public $fuel_station, $liters, $amount_paid,
       $odometer_reading, $distance_since_last_refuel, $receipt_image, $notes;

    public $vehicle_id, $fuel_type, $request_amount, $reason_for_request;
    public $number_of_order_deliver, $upload_order_screenshort, $additional_notes;

     public function resetForm()
        {
            $this->reset([
                'vehicle_id',
                'fuel_type',
                'request_amount',
                'number_of_order_deliver',
                'upload_order_screenshort',
                'additional_notes',
            ]);
        }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->dispatch('show-add-edit-modal');
        $this->resetValidation();
    }
    public function saveFuelRequest()
    {
        $validated = $this->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'fuel_type' => 'required|in:petrol,diesel,cng,electric',
            'request_amount' => 'required|numeric|min:1',
            'reason_for_request' => 'required',
            'number_of_order_deliver' => 'required|numeric|min:0',
            'upload_order_screenshort' => 'nullable|file|max:2048',
            'additional_notes' => 'nullable|string|max:500',
        ]);

$validated['requested_by'] = auth()->id();
        
if ($this->upload_order_screenshort) {
    $file = $this->upload_order_screenshort;

    // Generate safe filename
    $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

    // Store temporarily in Laravel's storage
    $tempPath = $file->storeAs('fuel_screenshots_tmp', $filename, 'public');

    // Copy it manually to public folder
    $source = storage_path('app/public/fuel_screenshots_tmp/' . $filename);
    $destination = public_path('fuel_screenshots/' . $filename);

    // Ensure destination folder exists
    if (!file_exists(public_path('fuel_screenshots'))) {
        mkdir(public_path('fuel_screenshots'), 0755, true);
    }

    copy($source, $destination);

    // Save relative path
    $validated['upload_order_screenshort'] = 'fuel_screenshots/' . $filename;
}

        Fuel::create($validated);

        session()->flash('success', 'Fuel request saved successfully.');
        $this->reset();
        $this->dispatch('close-fuel-modal');
    }




     public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;

        // Reset pagination based on tab
        // match ($tab) {
        //     'fuel_request' => $this->resetPage('requestPage'),
        //     'fuel_expenses' => $this->resetPage('expensesPage'),
        //     'fuel_reports' => $this->resetPage('reportsPage'),
        //     default => $this->resetPage(),
        // };

        $this->dispatch('bind-search-events');
    }

    public $statusFilter = '';
    public $fuelTypeFilter = '';

    public function filterByStatus($value)
    {
        $this->statusFilter = $value;
        $this->render();
    }

    public function filterByFuelType($value)
    {
        $this->fuelTypeFilter = $value;
        $this->render();
    }

    public function resetFilters()
    {
        $this->fuelTypeFilter = '';
        $this->statusFilter = '';
        $this->render();
        $this->dispatch('reset-filter');
    }

    public function render()
    {
        $query = Fuel::with('vehicle')->latest();

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }


        if (!empty($this->fuelTypeFilter)) {
            $query->where('fuel_type', $this->fuelTypeFilter);
        }

        $fuelRequests = $query->paginate(10);
        $vehicles = Vehicle::latest()->get();
        $fuelExpenses = \App\Models\FuelExpense::with('vehicle')->latest()->paginate(10); 
        $drivers= Driver::all();

        return view('livewire.fuel.index', compact('fuelRequests', 'vehicles','fuelExpenses','drivers'));
    }

    public $showFuelModal = false;
    public $selectedFuel;

    public $showExpenseModal = false;
    public $selectedExpense;

    public function showFuelDetails($fuelId)
    {
        $this->selectedFuel = Fuel::findOrFail($fuelId);
        $this->showFuelModal = true;
        $this->dispatch('show-fuel-details-modal');
    }

     public function showExpenseDetails($fuelId)
    {
        $this->selectedExpense = FuelExpense::findOrFail($fuelId);
        $this->showExpenseModal = true;
        $this->dispatch('show-expense-details-modal');
    }



public $rejectionNotes = '';
public $fuelToReject;
public $fuelToApprove;

public function confirmReject($fuelId)
{
    
    $this->fuelToReject = Fuel::with('driver')->findOrFail($fuelId);
    $this->dispatch('show-reject-modal');
}



public function rejectFuelRequest()
{
    
    FuelRequestReject::create([
        'fuel_id' => $this->fuelToReject->id,
        'rejected_by' => Auth::id(), // optional
        'notes' => $this->rejectionNotes,
    ]);

    $this->fuelToReject->status = 'rejected';
    $this->fuelToReject->save();

    $this->reset(['fuelToReject', 'rejectionNotes']);
    $this->dispatch('hide-reject-modal');
    session()->flash('success', 'Fuel request rejected.');
}



public function exportCsv()
{
    $this->dispatch('download-csv');
     $this->activeTab='fuel_request';
}
public function exportExpensesCsv()
{
    $this->dispatch('download-expense-csv');
    $this->activeTab='fuel_expenses';
}

public function openApproveModal($fuelId)
{
    // load fuel with driver
    $fuel = Fuel::with('driver')->findOrFail($fuelId);

    $this->fuel_id = $fuel->id;
    $this->fuelToApprove = $fuel; // now includes driver
    $this->approved_amount = $fuel->request_amount;
    $this->approved_fuel_type = $fuel->fuel_type; 
    $this->estimated_cost = null;
    $this->scheduled_date = null;
    $this->notes = null;

    $this->dispatch('show-approve-modal');
}




public $fuel_id, $approved_amount, $approved_fuel_type, $estimated_cost, $scheduled_date;

public function approveFuelRequest()
{
    
     $fuel = Fuel::with(['driver', 'vehicle'])->findOrFail($this->fuel_id);
     
    $this->validate([
        'fuel_id' => 'required|exists:fuels,id',
     
    ]);

    FuelRequestApproval::create([
    'fuel_id'             => $this->fuel_id,
    'approved_by'         => auth()->id(),
    'approved_fuel_type'  => $fuel->fuel_type,
   
]);
    
    
//     $this->validate([
//         'fuel_id' => 'required|exists:fuels,id',
//         'approved_amount' => 'required|numeric|min:1',
//         'approved_fuel_type' => 'required|in:petrol,diesel,cng,electric',
//         'estimated_cost' => 'nullable|numeric|min:0',
//         'scheduled_date' => 'nullable|date',
//         'notes' => 'nullable|string|max:500',
//     ]);

//     FuelRequestApproval::create([
//     'fuel_id'             => $this->fuel_id,
//     'approved_by'         => auth()->id(),
//     'approved_amount'     => $this->approved_amount,
//     'approved_fuel_type'  => $this->approved_fuel_type,
//     'estimated_cost'      => $this->estimated_cost ?: null,
//     'scheduled_date'      => $this->scheduled_date ?: null,
//     'notes'               => $this->notes,
// ]);


    Fuel::where('id', $this->fuel_id)->update(['status' => 'approved']);

    session()->flash('success', 'Fuel request approved successfully.');
    $this->dispatch('close-approve-modal');
}


public function createExpense()
{
    $this->resetForm();
    $this->showModal = true;
    $this->resetValidation();
    $this->dispatch('show-add-expense-modal');
 
}

public function saveFuelExpense()
{
    $this->validate([
        'vehicle_id' => 'required|exists:vehicles,id',
        'fuel_type' => 'required|in:petrol,diesel,cng,electric',
        'fuel_station' => 'required|string|max:255',
        'liters' => 'required|numeric|min:0',
        'amount_paid' => 'required|numeric|min:0',
        'odometer_reading' => 'required|integer|min:0',
        'distance_since_last_refuel' => 'required|integer|min:0',
        'receipt_image' => 'required|image|max:2048',
        'notes' => 'nullable|string|max:1000',
    ]);

    $data = $this->only([
        'vehicle_id', 'fuel_type', 'fuel_station', 'liters', 'amount_paid',
        'odometer_reading', 'distance_since_last_refuel', 'notes'
    ]);


    $data['recorded_by'] = auth()->id(); 

    if ($this->receipt_image) {
    $file = $this->receipt_image;

    // Generate safe filename
    $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

    // Store temporarily in Laravel's storage
    $tempPath = $file->storeAs('receipt_image_tmp', $filename, 'public');

    // Copy it manually to public folder
    $source = storage_path('app/public/receipt_image_tmp/' . $filename);
    $destination = public_path('receipt_image/' . $filename);

    // Ensure destination folder exists
    if (!file_exists(public_path('receipt_image'))) {
        mkdir(public_path('receipt_image'), 0755, true);
    }

    copy($source, $destination);

    // Save relative path
    $data['receipt_image'] = 'receipt_image/' . $filename;
}

    FuelExpense::create($data);

    session()->flash('success', 'Fuel expense saved successfully.');
    $this->reset();
    $this->dispatch('close-expense-modal');
     $this->activeTab='fuel_expenses';
}


public function callGenerateModal()
{
     $this->activeTab='fuel_reports';
    $this->dispatch('open-generate-modal');
}



    public $startDate;
    public $endDate;
    public $vehicleId = '';
    public $driverId = '';

    public $byVehicle = [];
    public $byDriver = [];

  public function generate()
{
    $this->validate([
        'startDate' => 'required|date',
        'endDate'   => 'required|date|after_or_equal:startDate',
    ]);

   $query = FuelExpense::query()
    ->when($this->vehicleId, fn($q) => $q->where('vehicle_id', $this->vehicleId))
    ->whereBetween('created_at', [
        Carbon::parse($this->startDate)->startOfDay(),
        Carbon::parse($this->endDate)->endOfDay()
    ]);


    // By Vehicle only
    $this->byVehicle = $query->clone()
        ->select(
            'vehicle_id',
            DB::raw('SUM(liters) as total_fuel'),
            DB::raw('SUM(amount_paid) as total_cost'),
            DB::raw('SUM(distance_since_last_refuel) as total_distance'),
            DB::raw('COUNT(*) as refuels'),
            DB::raw('ROUND(SUM(distance_since_last_refuel)/SUM(liters), 2) as avg_mileage'),
            DB::raw('ROUND(SUM(amount_paid)/SUM(liters), 2) as avg_cost_per_liter')
        )
        ->groupBy('vehicle_id')
        ->with('vehicle')
        ->get();

        $this->activeTab='fuel_reports';
        $this->dispatch('close-generate-modal');
}

public function ExportTReportCSV()
{
    
    $this->dispatch('ExportReportCSV');
}


public function exportReport()
{
    $filename = 'fuel_expense_report_' . now()->format('Ymd_His') . '.csv';

    $query = FuelExpense::query()
        ->when($this->vehicleId, fn($q) => $q->where('vehicle_id', $this->vehicleId))
        ->whereBetween('created_at', [
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay(),
        ]);

    $data = $query
        ->select(
            'vehicle_id',
            DB::raw('SUM(liters) as total_fuel'),
            DB::raw('SUM(amount_paid) as total_cost'),
            DB::raw('SUM(distance_since_last_refuel) as total_distance'),
            DB::raw('COUNT(*) as refuels'),
            DB::raw('ROUND(SUM(distance_since_last_refuel)/SUM(liters), 2) as avg_mileage'),
            DB::raw('ROUND(SUM(amount_paid)/SUM(liters), 2) as avg_cost_per_liter')
        )
        ->groupBy('vehicle_id')
        ->with('vehicle')
        ->get();

    return response()->streamDownload(function () use ($data) {
        $file = fopen('php://output', 'w');

        fputcsv($file, [
            'Vehicle',
            'Total Fuel (L)',
            'Total Cost (SAR)',
            'Total Distance (km)',
            'Refuels',
            'Avg Mileage (km/L)',
            'Avg Cost/Liter (SAR)',
        ]);

        foreach ($data as $v) {
            fputcsv($file, [
                $v->vehicle->registration_number ?? 'Unknown',
                number_format($v->total_fuel, 2),
                number_format($v->total_cost, 2),
                number_format($v->total_distance, 2),
                $v->refuels,
                $v->avg_mileage,
                $v->avg_cost_per_liter,
            ]);
        }

        fclose($file);
    }, $filename);
}



}
