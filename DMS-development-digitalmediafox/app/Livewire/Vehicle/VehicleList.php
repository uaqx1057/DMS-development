<?php
namespace App\Livewire\Vehicle;

use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Models\VehicleMaintenancesReport;
use App\Models\AssignDriverReport;
use App\Traits\DataTableTrait;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Driver;
use App\Models\AssignDriver;
use App\Models\ReplacementRejection;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Models\VehicleDocument;
use Illuminate\Support\Facades\Storage;
use App\Models\Branch;


#[Title('Vehicle List')]
class VehicleList extends Component
{
    use WithFileUploads;
    use DataTableTrait;
    use WithPagination;
    protected $paginationTheme = 'bootstrap'; 
    public $showReplacementModal = false;
    public $reason, $replacement_period;
    public  $maintenance_type, $description, $urgency;
    public $showMaintenanceModal = false;
    public $vehicle_id, $vehicle_name, $driver_id, $assign_date, $showAssignModal = false;
    public $drivers = [];
    public $search = '';
    public $isEdit = false;
    public bool $showModal = false;
    public $confirmingDeleteId = null;
    public string $activeTab = 'vehicles'; // default tab

    public $approvalModal = false;
    public $selectedMaintenanceId='';
    public $estimated_cost;
    public $scheduled_date;
    public $approval_notes;
    public $sortColumn = 'created_at';
    public $dateFrom = null;
    public $dateTo = null;
    public $dateFrom3 = null;
    public $dateTo3 = null;
    public $dateFrom2 = null;
    public $dateTo2 = null;
    
    protected $listeners = [
        'setDateRange' => 'setDateRange',
        'setDateRange2' => 'setDateRange2',
    'setDateRange3' => 'setDateRange3',
];





    public $registration_number, $make, $model, $year, $vin, $current_location, $status, $fuel_type, $mileage, $notes, $branch_id,$user_branch,$color, $rental_company, $company_sticker,$gps;
    public $Avehicles=[];
    public $documents = []; public $uploadIteration = 0;

    private string $main_menu  = 'Vehicle';
    private string $menu  = 'Vehicle List';

    public function updatingSearch()
    {
        
        $this->dispatch('bind-search-events');
    }

    public function mount()
    {
        if(auth()->user()->role_id==1)
        {
            $this->user_branch='';
        }else{
            $this->user_branch=auth()->user()->branch_id;
        }
        
        
        
        if (session()->has('page')) {
            $this->page = session('page');
        }
        $this->dispatch('bind-search-events');
    }

    public function boot()
    {
        if (session()->has('page')) {
            $this->page = session('page');
        }
        $this->dispatch('bind-search-events');
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;

        // Reset pagination based on tab
        match ($tab) {
            'vehicles' => $this->resetPage('vehiclesPage'),
            'maintenance' => $this->resetPage('maintenancePage'),
            'replacement' => $this->resetPage('replacementPage'),
            default => $this->resetPage(),
        };
        
        $this->resetFilters();
        $this->dispatch('bind-search-events');
    }


public $Mvehicles = [];
public $vid;
public function make_replace($id)
{
    // Get all vehicles in maintenance
    $this->Mvehicles = Vehicle::where('status', 'maintenance')->get();
    $this->vehicle_id=$id;
    $this->vid=$id;
    // Reset form fields
    $this->reset(['reason', 'replacement_period', 'notes', 'vehicle_id', 'vehicle_name','Mvehicle_id']);
    $this->showReplacementModal = true;

    $this->dispatch('show-make-replacement-modal');
}


  
    public function replace($id)
    {
        $this->Avehicles = Vehicle::where('status', 'available')->get();
        $vehicle = Vehicle::findOrFail($id);
        $this->vehicle_id = $vehicle->id;
        $this->vehicle_name = $vehicle->make . ' / ' . $vehicle->model;
        $this->reset(['reason', 'replacement_period', 'notes']);
        $this->showReplacementModal = true;

        $this->dispatch('show-replacement-modal');
    }
    
    public $Mvehicle_id;
    public function saveMakeReplacement()
    {
        $this->validate([
            'Mvehicle_id' => 'required',
            'reason' => 'required|string',
            'replacement_period' => 'required|string',
        ]);

        \App\Models\VehicleReplacement::create([
            'vehicle_id' =>$this->Mvehicle_id,
            'reason' => $this->reason,
            'replacement_period' => $this->replacement_period,
            'notes' => $this->notes,
            'status' => 'pending',
            'replacement_vehicle'=>$this->vid,
        ]);
        
        //\App\Models\VehicleMaintenance::where('vehicle_id', $this->vehicle_id)->delete();

        session()->flash('success', 'Vehicle replacement request submitted successfully.');
        $this->showReplacementModal = false;

        $this->dispatch('hide-make-replacement-modal');
    }

    
    
    
    
    
    
    

    public $Avehicle_id;
    public function saveReplacement()
    {
        $this->validate([
            'Avehicle_id' => 'required',
            'reason' => 'required|string',
            'replacement_period' => 'required|string',
        ]);

        \App\Models\VehicleReplacement::create([
            'vehicle_id' => $this->vehicle_id,
            'reason' => $this->reason,
            'replacement_period' => $this->replacement_period,
            'notes' => $this->notes,
            'status' => 'pending',
            'replacement_vehicle'=>$this->Avehicle_id,
        ]);
        
        \App\Models\VehicleMaintenance::where('vehicle_id', $this->vehicle_id)->delete();

        session()->flash('success', 'Vehicle replacement request submitted successfully.');
        $this->showReplacementModal = false;

        $this->dispatch('hide-replacement-modal');
    }


    public function assign($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $this->vehicle_id = $vehicle->id;
        $this->vehicle_name = $vehicle->make . ' / ' . $vehicle->model . ' (' . $vehicle->year . ')';
        $this->assign_date = now()->format('Y-m-d');
        $this->driver_id = '';

        if($this->user_branch!=''){
            $this->drivers = Driver::select('id', 'name')->where('branch_id',$this->user_branch)->get();
        }else{
            $this->drivers = Driver::select('id', 'name')->get();
        }
        
        $this->showAssignModal = true;

        $this->dispatch('open-assign-modal');
    }

    public function saveAssignment()
    {
            $this->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            ], [
            'vehicle_id.required' => 'Please select a vehicle.',
            'vehicle_id.exists' => 'The selected vehicle does not exist.',
            'driver_id.required' => 'Please select driver.',
            'driver_id.exists' => 'The selected driver does not exist.',
            ]);


            // Create assignment
            AssignDriver::create([
                'vehicle_id' => $this->vehicle_id,
                'driver_id' => $this->driver_id,
                'assign_date' => now(),
                'status' => 'active',
            ]);
            
             // Create assignment report
            AssignDriverReport::create([
                'vehicle_id' => $this->vehicle_id,
                'driver_id' => $this->driver_id,
                'add_date' => now(),
                'status' => 'assign',
            ]);

            // Update vehicle status to 'assigned'
            \App\Models\Vehicle::where('id', $this->vehicle_id)->update([
                'status' => 'assigned',
            ]);

            session()->flash('success', 'Vehicle assigned to driver successfully.');
            $this->showAssignModal = false;

            $this->dispatch('close-modal'); // triggers JS to close modal


    }



    public function rules()
    {
        return [
            'registration_number' => 'required|string|max:255|unique:vehicles,registration_number,' . $this->vehicle_id,
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|digits:4|integer|min:1900|max:2100',
            // 'vin' => 'required|string|max:255',
            // 'current_location' => 'required|string|max:255',
            'status' => 'string|in:available,assigned,maintenance,out_of_service',
            'fuel_type' => 'string|in:petrol,diesel,electric,hybrid',
            'mileage' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'branch_id' => 'required|integer',
            'color' => 'required',
            'rental_company' => 'required',
            'company_sticker' => 'required',
            'gps' => 'required',
        ];
    }


    public function messages()
    {
        return [
            'registration_number.required' => 'The registration number is required.',
            'registration_number.unique'   => 'This registration number is already registered.',
            'registration_number.max'      => 'The registration number may not be greater than 255 characters.',
    
            'make.required' => 'Please enter the vehicle make.',
            'model.required' => 'Please enter the vehicle model.',
    
            'year.required' => 'The vehicle year is required.',
            'year.digits'   => 'The year must be exactly 4 digits.',
            'year.integer'  => 'The year must be a valid integer.',
            'year.min'      => 'The year must be 1900 or later.',
            'year.max'      => 'The year may not be greater than 2100.',
    
            'vin.required' => 'The VIN is required.',
    
            'current_location.required' => 'The current location is required.',
    
            'status.in' => 'Status must be one of: available, assigned, maintenance, or out of service.',
    
            'fuel_type.in' => 'Fuel type must be one of: petrol, diesel, electric, or hybrid.',
    
            'mileage.required' => 'Mileage is required.',
            'mileage.numeric'  => 'Mileage must be a number.',
            'mileage.min'      => 'Mileage must be at least 0.',
    
            'notes.max' => 'Notes cannot exceed 1000 characters.',
    
            'branch_id.required' => 'Please select a branch.',
            'branch_id.integer'  => 'Branch must be a valid selection.',
        ];
    }



    public $VehicleStatus='';
    public function filterByVehicle($status)
    {
        $this->VehicleStatus=$status;
    }


    public $mrFilterStatus='';
    public function mrFilter($status)
    {
        $this->mrFilterStatus=$status;
    }
    
    public $repFilterStatus='';
    public function repFilter($status)
    {
        $this->repFilterStatus=$status;
    }
    
  public function resetFilters()
    {
        $this->VehicleStatus='';
        $this->mrFilterStatus='';
        $this->repFilterStatus='';
        $this->dateFrom ='';
        $this->dateTo ='';
        $this->dispatch('reset-filter');
    }
   
    public function resetFilters2()
    {
      
        $this->mrFilterStatus='';
         $this->dateFrom2 ='';
        $this->dateTo2 ='';
        $this->render();
        $this->dispatch('reset-filter2');
    }
    
     public function resetFilters3()
    {
      
        $this->repFilterStatus='';
        $this->dateFrom3 ='';
        $this->dateTo3 ='';
        $this->render();
        $this->dispatch('reset-filter3');
    }
   

    protected $updatesQueryString = ['search'];
    

    

public function setDateRange(string $from, string $to)
{

    $this->dateFrom = $from;
    $this->dateTo = $to;
}

public function setDateRange2(string $from, string $to)
{

    $this->dateFrom2 = $from;
    $this->dateTo2 = $to;
    
   
}

public function setDateRange3(string $from, string $to)
{
    $this->dateFrom3 = $from;
    $this->dateTo3 = $to;

}


   public function render()
{
    $main_menu = $this->main_menu;
    $menu = $this->menu;

    $add_permission = CheckPermission(config('const.ADD'), config('const.VEHICLES'));
    $edit_permission = CheckPermission(config('const.EDIT'), config('const.VEHICLES'));

    $columns = [
        ['label' => 'Date', 'column' => 'created_at', 'isData' => true, 'hasRelation' => false,'format' => 'd-m-Y'],
        ['label' => 'Registration No.', 'column' => 'registration_number', 'isData' => true, 'hasRelation' => false],
        ['label' => 'Make/Model', 'column' => 'vehicle_info', 'isData' => false, 'hasRelation' => false],
        ['label' => 'Status', 'column' => 'status', 'isData' => false, 'hasRelation' => false],
        // ['label' => 'Location', 'column' => 'current_location', 'isData' => true, 'hasRelation' => false],
        ['label' => 'Driver', 'column' => 'driver_name', 'isData' => false, 'hasRelation' => false],
        ['label' => 'Action', 'column' => 'action', 'isData' => false, 'hasRelation' => false],
    ];


    

    


    // ✅ Build the base vehicle query
    $vehicleQuery = Vehicle::query()
        ->leftJoin('assign_drivers', 'vehicles.id', '=', 'assign_drivers.vehicle_id')
        ->leftJoin('drivers', 'assign_drivers.driver_id', '=', 'drivers.id')
        ->select(
            'vehicles.*',
            'drivers.name as driver_name',
            'drivers.id as driver_id'
        );
        
        
        
        

    // 🔍 Apply search filter
    if ($this->search) {
        $vehicleQuery->where(function ($q) {
            $q->where('vehicles.registration_number', 'like', '%' . $this->search . '%')
                ->orWhere('vehicles.make', 'like', '%' . $this->search . '%')
                ->orWhere('vehicles.model', 'like', '%' . $this->search . '%')
                ->orWhere('drivers.name', 'like', '%' . $this->search . '%');
        });
    }
    
    if ($this->dateFrom && $this->dateTo) {
    $from = $this->dateFrom . ' 00:00:00';
    $to = $this->dateTo . ' 23:59:59';
    
    
    $vehicleQuery->whereBetween('vehicles.created_at', [$from, $to]);
    }


    if ($this->VehicleStatus !== '') {
        $vehicleQuery->where('vehicles.status', $this->VehicleStatus);
    }
    
    
    
    
    if($this->user_branch=='')
    {
        $branch=Branch::get();   
        
    }else
    {
         $branch=Branch::where('id',$this->user_branch)->get();   
         $vehicleQuery->where('vehicles.branch_id',$this->user_branch);
    }
    
    
    
    
    

    $vehicles = $vehicleQuery
        ->orderBy('vehicles.id','asc')
        ->paginate(10, ['*'], 'vehiclesPage');

    // 🛠 Maintenance & replacement pagination
    $maintenanceQuery = VehicleMaintenance::with('vehicle')
    ->latest();
    
    if ($this->user_branch) {
        $maintenanceQuery->whereHas('vehicle', function ($q) {
            $q->where('branch_id', $this->user_branch);
        });
    }
    
    if ($this->mrFilterStatus !== '') {
    $maintenanceQuery->where('status', $this->mrFilterStatus);
    }
    
    
    if ($this->dateFrom2 && $this->dateTo2) {
    $from2 = $this->dateFrom2 . ' 00:00:00';
    $to2 = $this->dateTo2 . ' 23:59:59';
    
    $maintenanceQuery->whereBetween('created_at', [$from2, $to2]);
   
    }
    
    $maintenanceRequests = $maintenanceQuery->paginate(10, ['*'], 'maintenancePage');
    
    

     $replacementQuery = \App\Models\VehicleReplacement::with('vehicle')
        ->latest();
        
         if ($this->user_branch) {
        $replacementQuery->whereHas('vehicle', function ($q) {
            $q->where('branch_id', $this->user_branch);
        });
    }
        
       if ($this->repFilterStatus !== '') {
    $replacementQuery->where('status', $this->repFilterStatus);
    } 
    
    
    if ($this->dateFrom3 && $this->dateTo3) {
    $from3 = $this->dateFrom3 . ' 00:00:00';
    $to3 = $this->dateTo3 . ' 23:59:59';
    
    $replacementQuery->whereBetween('created_at', [$from3, $to3]);
   
    }
        
    $replacementRequests=$replacementQuery->paginate(10, ['*'], 'replacementPage');    


    return view('livewire.vehicle.vehicle-list', compact(
        'vehicles',
        'maintenanceRequests',
        'replacementRequests',
        'main_menu',
        'menu',
        'add_permission',
        'edit_permission',
        'columns',
        'branch',
    ));
}


    public function create()
    {
        $this->resetForm();
         $this->uploadIteration++;
        $this->showModal = true;
        $this->dispatch('show-add-edit-modal');
        $this->resetValidation();
        $this->documents=[];
    }

    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $this->vehicle_id = $vehicle->id;
        $this->registration_number = $vehicle->registration_number;
        $this->make = $vehicle->make;
        $this->model = $vehicle->model;
        $this->year = $vehicle->year;
        $this->vin = $vehicle->vin;
        $this->current_location = $vehicle->current_location;
        $this->fuel_type = $vehicle->fuel_type;
        $this->mileage = $vehicle->mileage;
        $this->notes = $vehicle->notes;
        $this->branch_id = $vehicle->branch_id;
        $this->color = $vehicle->color;
        $this->rental_company = $vehicle->rental_company;
        $this->company_sticker = $vehicle->company_sticker;
        $this->gps = $vehicle->gps;

        $this->isEdit = true;
        $this->showModal = true;
        $this->documents='';
        $this->dispatch('show-add-edit-modal');
    }
    
    public function removeDocument($index)
    {
        if (isset($this->documents[$index])) {
            unset($this->documents[$index]);
            $this->documents = array_values($this->documents); 
            
        }
    }


    public function save()
    {
        $this->status = blank($this->status) ? 'available' : $this->status;
        $this->fuel_type = blank($this->fuel_type) ? 'petrol' : $this->fuel_type;
    
        $this->validate();
    
        $vehicle = Vehicle::updateOrCreate(
            ['id' => $this->vehicle_id],
            [
                'registration_number' => $this->registration_number,
                'make' => $this->make,
                'model' => $this->model,
                'year' => $this->year,
                'vin' => $this->vin,
                'current_location' => $this->current_location,
                'fuel_type' => $this->fuel_type,
                'mileage' => $this->mileage,
                'notes' => $this->notes,
                'branch_id' => $this->branch_id,
                'color' => $this->color,
                'rental_company' => $this->rental_company,
                'company_sticker' => $this->company_sticker,
                'gps' => $this->gps,
            ]
        );
        
       
    
        // === Document handling ===
        if (!empty($this->documents)) {
            // If updating, delete old docs first
            if (!$vehicle->wasRecentlyCreated) {
                $oldDocs = VehicleDocument::where('vehicle_id', $vehicle->id)->get();
                foreach ($oldDocs as $doc) {
                    Storage::disk('public')->delete($doc->file_path); // remove from storage
                    $doc->delete();
                }
            }
    
            // Save new docs
            foreach ($this->documents as $file) {
                $path = $file->store('vehicle_documents', 'public');
                VehicleDocument::create([
                    'vehicle_id' => $vehicle->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }
    
        session()->flash('success', $this->isEdit ? 'Vehicle updated successfully.' : 'Vehicle added successfully.');
    
      
        $this->resetForm();
        
    
        $this->dispatch('hide-add-edit-modal');
    }



    public function openDelete($id)
    {
        $this->confirmingDeleteId=$id;
        $this->dispatch('open-delete-modal');
    }

    public function confirmDelete()
    {
        if ($this->confirmingDeleteId) {
            Vehicle::findOrFail($this->confirmingDeleteId)->delete();
            $this->confirmingDeleteId = null;
            session()->flash('success', 'Vehicle deleted successfully.');
            $this->dispatch('close-delete-modal');
        }
    }

    public function resetForm()
    {
        $this->reset([
            'vehicle_id',
            'registration_number',
            'make',
            'model',
            'year',
            'vin',
            'current_location',
            'status',
            'fuel_type',
            'mileage',
            'notes',
            'branch_id',
            'isEdit',
        ]);
    }



public $vehicleToUnassign;

public function confirmUnassign($vehicleId)
{
    $this->vehicleToUnassign = $vehicleId;
    $this->dispatch('show-unassign-modal');
}

public function unassign()
{
    $assign = \App\Models\AssignDriver::where('vehicle_id', $this->vehicleToUnassign)->latest()->first();

    if ($assign) {
        
        
         // Create assignment report
            AssignDriverReport::create([
                'vehicle_id' => $assign->vehicle_id,
                'driver_id' => $assign->driver_id,
                'add_date' => now(),
                'status' => 'unassign',
            ]);
        
        
        $assign->delete();

        // Set vehicle status to 'available'
        \App\Models\Vehicle::where('id', $this->vehicleToUnassign)->update([
            'status' => 'available',
        ]);

       
    } else {
        session()->flash('error', 'Assignment not found.');
    }

    $this->resetPage('vehiclesPage');
     session()->flash('success', 'Driver unassigned successfully.');
    $this->dispatch('hide-unassign-modal');
    
}

public function maintenance($id)
{
    $vehicle = Vehicle::findOrFail($id);
    $this->vehicle_id = $vehicle->id;
    $this->vehicle_name = $vehicle->make . ' / ' . $vehicle->model;
    $this->reset(['maintenance_type', 'description', 'urgency']);
    $this->dispatch('show-maintenance-modal');
}
public function saveMaintenance()
{
    $this->validate([
        'vehicle_id' => 'required|exists:vehicles,id',
        'maintenance_type' => 'required|string',
        'description' => 'required|string|max:1000',
        'urgency' => 'required|in:low,normal,high,critical',
    ]);

    VehicleMaintenance::create([
        'vehicle_id' => $this->vehicle_id,
        'maintenance_type' => $this->maintenance_type,
        'description' => $this->description,
        'urgency' => $this->urgency,
        'status' => 'pending',
        'request_by' => auth()->user()->name ?? 'admin', // or use user_id
    ]);

    

    session()->flash('success', 'Maintenance request submitted.');
    $this->dispatch('hide-maintenance-modal');

}

public function approve($id)
{
    $this->openApproveModal($id); // This will set everything
}

public function openApproveModal($id)
{
    $this->activeTab = 'maintenance'; // <-- semicolon added
    $this->selectedMaintenanceId = $id;
    $this->reset(['estimated_cost', 'scheduled_date', 'approval_notes']);
    $this->approvalModal = true;

    $this->dispatch('show-approval-modal');
}

public function saveApproval()
{
    $this->activeTab = 'maintenance'; // or 'replacement' etc.

    $this->validate([
        'estimated_cost' => 'required|numeric|min:0',
        'scheduled_date' => 'required|date',
      
    ]);

     $maintenance = VehicleMaintenance::findOrFail($this->selectedMaintenanceId);

    $maintenance->update([
        'status' => 'approved',
    ]);

      $maintenance->vehicle()->update([
        'status' => 'maintenance'
    ]);

    \App\Models\MaintenanceApproval::create([
        'maintenance_id' => $this->selectedMaintenanceId,
        'estimated_cost' => $this->estimated_cost,
        'scheduled_date' => $this->scheduled_date,
        'approval_notes' => $this->approval_notes,
        'approved_by' => auth()->user()->name ?? 'admin',
    ]);

    session()->flash('success', 'Maintenance request approved successfully.');
    $this->approvalModal = false;

    $this->dispatch('hide-approval-modal');
}


public $completeModal = false;
public  $resolution_details, $cost_incurred, $next_maintenance_date;

public function complete($id)
{
    $this->selectedMaintenanceId = $id;
    $this->reset(['resolution_details', 'cost_incurred', 'next_maintenance_date']);
    $this->completeModal = true;

    $this->dispatch('show-complete-modal');
}

public function saveCompletion()
{
    $this->validate([
        'resolution_details' => 'required|string|max:1000',
        'cost_incurred' => 'required|numeric|min:0',
        'next_maintenance_date' => 'nullable|date',
    ]);

    $maintenance = VehicleMaintenance::findOrFail($this->selectedMaintenanceId);

    // Update maintenance record
    $maintenance->update([
        'resolution_details' => $this->resolution_details,
        'cost_incurred' => $this->cost_incurred,
        'next_maintenance_date' => $this->next_maintenance_date,
        'status' => 'completed',
    ]);

    // Also update the vehicle status to 'available'
    if ($maintenance->vehicle_id) {
        \App\Models\Vehicle::where('id', $maintenance->vehicle_id)
            ->update(['status' => 'available']);
    }

    session()->flash('success', 'Maintenance marked as completed.');
    $this->completeModal = false;

    $this->dispatch('hide-complete-modal');
}

public $rejectModal = false;
public $rejection_notes;

public function reject($id)
{
    $this->selectedMaintenanceId = $id;
    $this->rejection_notes = '';
    $this->activeTab = 'maintenance'; // optional
    $this->rejectModal = true;

    $this->dispatch('show-reject-modal');
}

public function saveRejection()
{
    $maintenance = VehicleMaintenance::findOrFail($this->selectedMaintenanceId);

    // Update status
    $maintenance->update([
        'status' => 'rejected',
    ]);

    // Save rejection note (create a model if needed)
    \App\Models\MaintenanceRejection::create([
        'maintenance_id' => $this->selectedMaintenanceId,
        'rejection_note' => $this->rejection_notes,
        'rejected_by' => auth()->user()->name ?? 'admin',
    ]);

    $this->rejectModal = false;
    session()->flash('success', 'Maintenance request rejected.');
    $this->dispatch('hide-reject-modal');
}

public $replacementApprovalModal = false;
public $selectedReplacementRequestId;
public $replacement_vehicle_id;
public $expected_return_date;
public $replacement_approval_notes;


public $availableVehicles = [];

public function approveReplacement($id)
{

    $this->selectedReplacementRequestId = $id;
    $this->replacementApprovalModal = true;
    $this->reset([
        'replacement_period',
        'expected_return_date',
        'replacement_approval_notes'
    ]);

    $this->availableVehicles = \App\Models\Vehicle::where('status', 'available')->get();

    $this->dispatch('show-replacement-approval-modal');
}
public function saveReplacementApproval()
{
    $rules = [
        'replacement_period' => 'required|in:temporary,permanent',
        'approval_notes' => 'nullable|max:1000',
    ];

    if ($this->replacement_period === 'temporary') {
        $rules['expected_return_date'] = 'required|date|after_or_equal:today';
    } else {
        $rules['expected_return_date'] = 'nullable|date';
    }

    $this->validate($rules);

    // Fetch the replacement request
    $replacement = \App\Models\VehicleReplacement::findOrFail($this->selectedReplacementRequestId);

    // Save approval entry
    \App\Models\VehicleReplacementApproval::create([
        'replacement_id' => $replacement->id,
        'replacement_period' => $this->replacement_period,
        'expected_return_date' => $this->expected_return_date,
        'approval_notes' => $this->replacement_approval_notes,
        'approved_by' => auth()->user()->name ?? 'admin',
    ]);

    // Update replacement request
    $replacement->update([
        'status' => 'approved',
    ]);

    if ($replacement->replacementVehicle) {
    $replacement->replacementVehicle->update([
        'status' => 'in-replacement', // or 'in_use'
    ]);
}

    // Update original vehicle status (also to out_of_service or replaced)
    \App\Models\Vehicle::find($replacement->vehicle_id)?->update([
        'status' => 'out_of_service', // or 'out_of_service', as per your logic
    ]);

    session()->flash('success', 'Replacement request approved successfully.');
    $this->replacementApprovalModal = false;
    $this->dispatch('hide-replacement-approval-modal');
}


public function returnOriginal($id)
{
    // Fetch the replacement record
    $replacement = \App\Models\VehicleReplacement::with('replacementVehicle')->findOrFail($id);

    // Update replacement status to 'completed'
    $replacement->update([
        'status' => 'completed',
    ]);

    // Update the original vehicle's status to 'available'
    if ($replacement->vehicle_id) {
        \App\Models\Vehicle::where('id', $replacement->vehicle_id)->update([
            'status' => 'available',
        ]);
    }

    if ($replacement->replacementVehicle) {
        $replacement->replacementVehicle->update([
            'status' => 'available',
        ]);
    }

    $this->dispatch('hide-alert');
    session()->flash('success', 'Original vehicle returned and both vehicles updated successfully.');
}




public $replacement_rejection_note   = '';


public function saveReplacementRejection()
{
    // (optional) validate the note
    $this->validate([
        'replacement_rejection_note' => 'nullable|string|max:1000',
    ]);

    DB::transaction(function () {
        // 1) update the original replacement request’s status
        \App\Models\VehicleReplacement::where('id', $this->selectedReplacementRequestId)
            ->update(['status' => 'rejected']);

        // 2) create the rejection record
        ReplacementRejection::create([
            'replacement_id' => $this->selectedReplacementRequestId,
            'rejection_note' => $this->replacement_rejection_note,
            'rejected_by'    => auth()->user()->name ?? 'admin',
        ]);
    });

    session()->flash('success', 'Replacement request rejected.');
    $this->reset(['replacement_rejection_note', 'selectedReplacementRequestId']);
    $this->dispatch('hide-replacement-rejection-modal');
}

public $replacementrejectModal = false;
public $replacement_rejection_notes;

public function replacement_reject($id)
{
    $this->selectedReplacementRequestId = $id;
    $this->replacement_rejection_notes = '';
    $this->activeTab = 'replacement'; // optional
    $this->replacementrejectModal = true;

    $this->dispatch('show-replacement-rejection-modal');
}



   public $selectedVehicle = '';

    public function showVehicleDetails($vehicleId)
    {
        $this->selectedVehicle = Vehicle::with('documents')->findOrFail($vehicleId);
       $this->dispatch('show-vDetail-modal');
    }



public function exportPdf()
{
    $vehicleQuery =  Vehicle::with(['assignedDriver.driver']);
    
    if ($this->VehicleStatus !== '') {
        $vehicleQuery->where('status', $this->VehicleStatus);
    }
    
    if ($this->dateFrom && $this->dateTo) {
    $from = $this->dateFrom . ' 00:00:00';
    $to = $this->dateTo . ' 23:59:59';
    
    $vehicleQuery->whereBetween('created_at', [$from, $to]);
    }

    $vehicles = $vehicleQuery->get();

    $pdf = Pdf::loadView('exports.vehicles', compact('vehicles'));

    $filename = 'vehicles-list-' . \Carbon\Carbon::now()->format('Y-m-d') . '.pdf';

    return response()->streamDownload(
        fn () => print($pdf->stream()),
        $filename
    );
}



public function exportPdf2()
{
    $mainQuery = VehicleMaintenance::with('vehicle');

    if ($this->mrFilterStatus !== '') {
        $mainQuery->where('status', $this->mrFilterStatus);
    }
    
      if ($this->dateFrom2 && $this->dateTo2) {
    $from2 = $this->dateFrom2 . ' 00:00:00';
    $to2 = $this->dateTo2 . ' 23:59:59';
    
    $mainQuery->whereBetween('created_at', [$from2, $to2]);
    }

    $maintenance = $mainQuery->get();

    $pdf = Pdf::loadView('exports.maintenance', compact('maintenance'));

    $filename = 'maintenance-list-' . \Carbon\Carbon::now()->format('Y-m-d') . '.pdf';

    return response()->streamDownload(
        fn () => print($pdf->stream()),
        $filename
    );
}



public function exportPdf3()
{
  
    $repQuery = \App\Models\VehicleReplacement::with('vehicle');
    
    if ($this->repFilterStatus !== '') {
        $repQuery->where('status', $this->repFilterStatus);
    }
    
     if ($this->dateFrom3 && $this->dateTo3) {
    $from3 = $this->dateFrom3 . ' 00:00:00';
    $to3 = $this->dateTo3 . ' 23:59:59';
    
    $repQuery->whereBetween('created_at', [$from3, $to3]);
    }
        

    $replacements=$repQuery->get();
    $pdf = Pdf::loadView('exports.replacements', compact('replacements'));

    $filename = 'replacement-list-' . Carbon::now()->format('Y-m-d') . '.pdf';

    return response()->streamDownload(
        fn () => print($pdf->stream()),
        $filename
    );
    
    
}



public function exportVehicleHistory($vehicleId)
{
    $vehicle = Vehicle::findOrFail($vehicleId);

    $maintenances = VehicleMaintenancesReport::where('vehicle_id', $vehicleId)
        ->orderBy('created_at')
        ->get();

    $replacements = \App\Models\VehicleReplacement::where('vehicle_id', $vehicleId)
        ->orderBy('created_at')
        ->get();

    $driverAssignments = AssignDriverReport::with('driver')
        ->where('vehicle_id', $vehicle->id)
        ->orderBy('add_date')
        ->get();

    foreach ($replacements as $replacement) {
        $lastMaintenance = VehicleMaintenancesReport::where('vehicle_id', $vehicleId)
            ->where('created_at', '<=', $replacement->created_at)
            ->orderByDesc('created_at')
            ->first();

        $replacement->last_maintenance_date = $lastMaintenance?->created_at; 
        $replacement->last_maintenance_type = $lastMaintenance?->maintenance_type; // optional if you want type too
    }

    $pdf = Pdf::loadView(
        'exports.vehicle-timeline-pdf', 
        compact('vehicle', 'maintenances', 'replacements', 'driverAssignments')
    );

    $filename = $vehicle->registration_number.'-history-' . Carbon::now()->format('Y-m-d') . '.pdf';

    return response()->streamDownload(
        fn () => print($pdf->stream()),
        $filename
    );
}




}
