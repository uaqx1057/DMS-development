<?php
namespace App\Livewire\Vehicle;

use App\Models\Vehicle;
use App\Traits\DataTableTrait;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Driver;
use App\Models\AssignDriver;

#[Title('Vehicle List')]
class VehicleList extends Component
{
    use DataTableTrait;
    use WithPagination;
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

    public $registration_number, $make, $model, $year, $vin, $current_location, $status, $fuel_type, $mileage, $notes;

    private string $main_menu  = 'Vehicle';
    private string $menu  = 'Vehicle List';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        if (session()->has('page')) {
            $this->page = session('page');
        }
    }

    public function boot()
    {
        if (session()->has('page')) {
            $this->page = session('page');
        }
    }

    public function replace($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->vehicle_id = $vehicle->id;
        $this->vehicle_name = $vehicle->make . ' / ' . $vehicle->model;
        $this->reset(['reason', 'replacement_period', 'notes']);
        $this->showReplacementModal = true;

        $this->dispatch('show-replacement-modal');
    }

    public function saveReplacement()
    {
        $this->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'reason' => 'required|string',
            'replacement_period' => 'required|string',
            'notes' => 'required|string|max:1000',
        ]);

        \App\Models\VehicleReplacement::create([
            'vehicle_id' => $this->vehicle_id,
            'reason' => $this->reason,
            'replacement_period' => $this->replacement_period,
            'notes' => $this->notes,
            'status' => 'pending',
        ]);

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

        $this->drivers = Driver::select('id', 'name')->get();
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
            'vin' => 'required|string|max:255',
            'current_location' => 'required|string|max:255',
            'status' => 'string|in:available,assigned,maintenance,out_of_service',
            'fuel_type' => 'string|in:petrol,diesel,electric,hybrid',
            'mileage' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function render()
{
    $main_menu = $this->main_menu;
    $menu = $this->menu;

    $add_permission = CheckPermission(config('const.ADD'), config('const.VEHICLES'));
    $edit_permission = CheckPermission(config('const.EDIT'), config('const.VEHICLES'));

    $columns = [
        ['label' => 'Registration No.', 'column' => 'registration_number', 'isData' => true, 'hasRelation' => false],
        ['label' => 'Make/Model', 'column' => 'vehicle_info', 'isData' => false, 'hasRelation' => false],
        ['label' => 'Status', 'column' => 'status', 'isData' => false, 'hasRelation' => false],
        ['label' => 'Location', 'column' => 'current_location', 'isData' => true, 'hasRelation' => false],
        ['label' => 'Driver', 'column' => 'driver_name', 'isData' => false, 'hasRelation' => false],
        ['label' => 'Action', 'column' => 'action', 'isData' => false, 'hasRelation' => false],
    ];

    $vehicles = Vehicle::query()
        ->leftJoin('assign_drivers', 'vehicles.id', '=', 'assign_drivers.vehicle_id')
        ->leftJoin('drivers', 'assign_drivers.driver_id', '=', 'drivers.id')
        ->select(
            'vehicles.*',
            'drivers.name as driver_name',
            'drivers.id as driver_id'
        )
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('vehicles.registration_number', 'like', '%' . $this->search . '%')
                    ->orWhere('vehicles.make', 'like', '%' . $this->search . '%')
                    ->orWhere('vehicles.model', 'like', '%' . $this->search . '%')
                    ->orWhere('drivers.name', 'like', '%' . $this->search . '%');
            });
        })
        ->orderBy($this->sortColumn, $this->sortDirection)
        ->paginate(10);

    $maintenanceRequests = \App\Models\VehicleMaintenance::with('vehicle')->latest()->paginate(10);
    $replacementRequests = \App\Models\VehicleReplacement::with('vehicle')->latest()->paginate(10);

    return view('livewire.vehicle.vehicle-list', compact(
        'vehicles',
        'maintenanceRequests',
        'replacementRequests',
        'main_menu',
        'menu',
        'add_permission',
        'edit_permission',
        'columns'
    ));
}


    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->dispatch('show-add-edit-modal');
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
        $this->status = $vehicle->status;
        $this->fuel_type = $vehicle->fuel_type;
        $this->mileage = $vehicle->mileage;
        $this->notes = $vehicle->notes;

        $this->isEdit = true;
        $this->showModal = true;
        $this->dispatch('show-add-edit-modal');
    }

    public function save()
    {
        $this->status = blank($this->status) ? 'available' : $this->status;
        $this->fuel_type = blank($this->fuel_type) ? 'petrol' : $this->fuel_type;

        $this->validate();

        Vehicle::updateOrCreate(
            ['id' => $this->vehicle_id],
            [
                'registration_number' => $this->registration_number,
                'make' => $this->make,
                'model' => $this->model,
                'year' => $this->year,
                'vin' => $this->vin,
                'current_location' => $this->current_location,
                'status' => $this->status,
                'fuel_type' => $this->fuel_type,
                'mileage' => $this->mileage,
                'notes' => $this->notes,
            ]
        );

        session()->flash('success', $this->isEdit ? 'Vehicle updated successfully.' : 'Vehicle added successfully.');
        $this->isEdit ?  : $this->resetForm();
        
        $this->dispatch('hide-add-edit-modal');
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
        $assign->delete();

        // Set vehicle status to 'available'
        \App\Models\Vehicle::where('id', $this->vehicleToUnassign)->update([
            'status' => 'available',
        ]);

        session()->flash('success', 'Driver unassigned successfully.');
    } else {
        session()->flash('error', 'Assignment not found.');
    }

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

    \App\Models\VehicleMaintenance::create([
        'vehicle_id' => $this->vehicle_id,
        'maintenance_type' => $this->maintenance_type,
        'description' => $this->description,
        'urgency' => $this->urgency,
        'status' => 'pending',
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
        'approval_notes' => 'required|string|max:1000',
    ]);

     $maintenance = \App\Models\VehicleMaintenance::findOrFail($this->selectedMaintenanceId);

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

    VehicleMaintenance::findOrFail($this->selectedMaintenanceId)->update([
        'resolution_details' => $this->resolution_details,
        'cost_incurred' => $this->cost_incurred,
        'next_maintenance_date' => $this->next_maintenance_date,
        'status' => 'completed',
    ]);

    session()->flash('success', 'Maintenance marked as completed.');
    $this->completeModal = false;

    $this->dispatch('hide-complete-modal');
}






}
