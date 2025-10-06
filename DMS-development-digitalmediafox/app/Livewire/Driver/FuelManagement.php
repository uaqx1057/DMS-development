<?php

namespace App\Livewire\Driver;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Fuel;
use Carbon\Carbon;
use App\Models\AssignDriver;
use App\Models\Vehicle;
use Livewire\WithFileUploads;

class FuelManagement extends Component
{
    use WithFileUploads;

    public $activeTab = 'requests';
    public $showRequestForm = false;
    public $showFilter=false;
    public $number_of_order_deliver;
    public $scrrenShorts;
    public $filterStatus;
    public $filterDate;
    

    public $fuelRequests = [];

    #[Layout('components.layouts.plain')]
    public function mount()
    {
        $this->loadFuelRequests();
    }

    /**
     * Refresh requests list
     */
    public function refreshRequests()
    {
        $this->loadFuelRequests();
    }
    
    public function showFilterModal()
    {
        $this->showFilter = true;
        $this->filterStatus = '';
        $this->filterDate='';
    }
    
    public function resetFilters()
    {
        $this->filterStatus = '';
        $this->filterDate='';
    }
    
    
public function applyFilters()
{
    $driver = Auth::guard('driver')->user();

    $query = Fuel::with('approval')
        ->where('requested_by', $driver->id);

    // Status filter
    if (!empty($this->filterStatus)) {
        $query->where('status', $this->filterStatus);
    }

  // ateTime filter (match by hour)
if (!empty($this->filterDate)) {
    $dateTime = Carbon::parse($this->filterDate);

    $query->whereBetween('created_at', [
        $dateTime->copy()->startOfHour(),
        $dateTime->copy()->endOfHour(),
    ]);
}



    $allRequests = $query->orderByDesc('created_at')->get();

    $this->fuelRequests = $allRequests->map(function ($fuel) use ($driver) {
        return [
            'id' => $fuel->id,
            'type' => $fuel->fuel_type,
            'amount' => $fuel->request_amount,
            'status' => $fuel->status,
            'date' => $fuel->created_at->format('d/m/Y, H:i:s'),
            'date_only' => $fuel->created_at->format('Y-m-d'),
            'reason' => ucwords(str_replace('_', ' ', $fuel->reason_for_request)),
            'reject_by'     => $fuel->rejection && $fuel->rejection->rejectedByUser
            ? $fuel->rejection->rejectedByUser->name
            : null,
            'reject_date'   => $fuel->rejection && $fuel->rejection->created_at
            ? $fuel->rejection->created_at->format('d/m/Y, H:i:s')
            : null,
            'reject_notes'  => $fuel->rejection->notes ?? null,
            'orders' => $fuel->number_of_order_deliver,
            'approved_amount' => $fuel->approval->approved_amount ?? null,
            'approved_by' => $fuel->approval && $fuel->approval->approvedByUser
                ? $fuel->approval->approvedByUser->name
                : null,
            'approval_date' => $fuel->approval && $fuel->approval->created_at
                ? $fuel->approval->created_at->format('d/m/Y, H:i:s')
                : null,
            'notes' => $fuel->approval->notes ?? null,
            'estimated_cost' => $fuel->approval->estimated_cost ?? null,
            'scheduled_date' => $fuel->approval && $fuel->approval->scheduled_date
                ? Carbon::parse($fuel->approval->scheduled_date)->format('d/m/Y')
                : null,
            'iqaama_number' => $driver->iqaama_number,
        ];
    })->toArray();

    // lose modal but keep filters applied
    $this->showFilter = false;
}


    /**
     * Load all requests for the driver
     */
    public function loadFuelRequests()
    {
        $driver = Auth::guard('driver')->user();

        $allRequests = Fuel::with('approval')
            ->where('requested_by', $driver->id)
            ->orderByDesc('created_at')
            ->get();

        $today = Carbon::today();

        $this->fuelRequests = $allRequests->map(function ($fuel) use ($today, $driver) {
        return [
        'id' => $fuel->id,
        'type' => $fuel->fuel_type,
        'amount' => $fuel->request_amount,
        'status' => $fuel->status,
        'date' => $fuel->created_at->format('d/m/Y, H:i:s'),
        'date_only' => $fuel->created_at->format('Y-m-d'),
        
        // ejection details
        'reject_notes'  => $fuel->rejection->notes ?? null,
        'reject_by'     => $fuel->rejection && $fuel->rejection->rejectedByUser
                            ? $fuel->rejection->rejectedByUser->name
                            : null,
        'reject_date'   => $fuel->rejection && $fuel->rejection->created_at
                            ? $fuel->rejection->created_at->format('d/m/Y, H:i:s')
                            : null,
        
        'orders' => $fuel->number_of_order_deliver,
        'approved_amount' => $fuel->approval->approved_amount ?? null,
        'approved_by' => $fuel->approval && $fuel->approval->approvedByUser
        ? $fuel->approval->approvedByUser->name
        : null,
        'approval_date' => $fuel->approval && $fuel->approval->created_at
        ? $fuel->approval->created_at->format('d/m/Y, H:i:s')
        : null,
        'notes' => $fuel->approval->notes ?? null,
        'estimated_cost' => $fuel->approval->estimated_cost ?? null,
        'scheduled_date' => $fuel->approval && $fuel->approval->scheduled_date
        ? Carbon::parse($fuel->approval->scheduled_date)->format('d/m/Y')
        : null,
        'iqaama_number' => $driver->iqaama_number,
        ];
        })->toArray();

    }

    /**
     * Switch between tabs
     */
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * Render the view with current driver and vehicle
     */
    public function render()
    {
        $driver = Auth::guard('driver')->user();

        // Find the active vehicle assigned to this driver
        $assignedVehicle = AssignDriver::where('driver_id', $driver->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        $currentVehicle = $assignedVehicle
            ? Vehicle::find($assignedVehicle->vehicle_id)
            : null;

        return view('livewire.driver.fuel-management', [
            'driver' => $driver,
            'currentVehicle' => $currentVehicle,
        ]);
    }

    /**
     * Show / hide fuel request form
     */
    public function toggleRequestForm()
    {
        $this->showRequestForm = !$this->showRequestForm;
    }

    /**
     * Submit a new fuel request
     */
    public function submit()
    {
        $driver = Auth::guard('driver')->user();

        // Get assigned vehicle
        $assignment = AssignDriver::where('driver_id', $driver->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$assignment) {
            session()->flash('error', 'No vehicle assigned to this driver.');
            return;
        }

        $vehicle = Vehicle::find($assignment->vehicle_id);

        // Validate inputs
        $validated = $this->validate([
            'number_of_order_deliver' => 'required|numeric|min:1',
            'scrrenShorts' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // File upload logic
        $filePath = null;
        if ($this->scrrenShorts) {
            $filePath = $this->scrrenShorts->store('order_screenshots', 'public');
        }

        // Save Fuel request
        Fuel::create([
            'vehicle_id' => $assignment->vehicle_id,
            'status' => 'pending',
            'fuel_type' => $vehicle->fuel_type,
            'number_of_order_deliver' => $this->number_of_order_deliver,
            'upload_order_screenshort' => $filePath,
            'requested_by' => $driver->id,
        ]);

        // Reload requests
        $this->loadFuelRequests();
        $this->showRequestForm = false;

        // Reset form fields
        $this->reset(['number_of_order_deliver', 'scrrenShorts']);

        session()->flash('success', 'Fuel request submitted successfully.');
    }
}
