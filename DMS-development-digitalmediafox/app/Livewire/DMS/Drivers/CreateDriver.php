<?php

namespace App\Livewire\DMS\Drivers;

use App\Traits\DMS\DriverTrait;
use App\Services\BusinessService;
use App\Services\DriverService;
use App\Models\BusinessId;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;

#[Title('Create Driver')]
class CreateDriver extends Component
{
    use WithFileUploads, DriverTrait;

    public string $main_menu = 'Drivers';
    public string $menu = 'Create Driver';
    public $selectedBusinessIds = []; // For multi-select
    public $availableBusinessIds = []; // Available IDs for selected businesses

    protected DriverService $driverService;
    protected BusinessService $businessService;

    public function boot(DriverService $driverService, BusinessService $businessService)
    {
        $this->driverService = $driverService;
        $this->businessService = $businessService;
    }

    // Handle business selection change
    public function handleBusinessSelection()
    {
        $this->availableBusinessIds = [];
        
        if (!empty($this->business_ids)) {
            foreach ($this->business_ids as $businessId) {
                $availableIds = BusinessId::where('business_id', $businessId)
                    ->where('is_active', true)
                    ->get();
                
                $this->availableBusinessIds[$businessId] = $availableIds;
            }
        }
    }

    public function render()
    {
        $main_menu   = $this->main_menu;
        $menu        = $this->menu;
        $businesses  = $this->businessService->all();

        return view('livewire.dms.drivers.create-driver', compact('main_menu', 'menu', 'businesses'));
    }
    public function updatedBusinessIds()
    {
        $this->availableBusinessIds = [];
        $newSelectedBusinessIds = [];
        
        if (!empty($this->business_ids)) {
            foreach ($this->business_ids as $businessId) {
                // Get only available (unassigned) business IDs
                $availableIds = BusinessId::where('business_id', $businessId)
                    ->where('is_active', true)
                    ->whereDoesntHave('drivers', function($query) {
                        $query->whereNull('transferred_at');
                    })
                    ->get();
                
                $this->availableBusinessIds[$businessId] = $availableIds;
                
                // Keep previously selected IDs for this business
                if (isset($this->selectedBusinessIds[$businessId])) {
                    $newSelectedBusinessIds[$businessId] = $this->selectedBusinessIds[$businessId];
                }
            }
        }
        
        $this->selectedBusinessIds = $newSelectedBusinessIds;
    }
    public function updateSelectedBusinessIds($businessId, $selectedIds)
    {
        $this->selectedBusinessIds[$businessId] = $selectedIds;
    }
    public function create(){
        $validated = $this->validations();
        
        if (isset($this->image)) {
            $validated['image'] = $this->image->store('drivers', 'public');
        }

        $driver = $this->driverService->create($validated);

        // Flatten selectedBusinessIds array and assign to driver
        $allSelectedIds = [];
        foreach ($this->selectedBusinessIds as $businessId => $ids) {
            $allSelectedIds = array_merge($allSelectedIds, $ids);
        }
        
        if (!empty($allSelectedIds)) {
            foreach ($allSelectedIds as $businessIdId) {
                $businessId = BusinessId::find($businessIdId);
                $previousDriver = $businessId->currentDriver();
                
                if ($previousDriver) {
                    $businessId->drivers()->updateExistingPivot($previousDriver->id, [
                        'transferred_at' => now()
                    ]);
                }
                
                $driver->businessIds()->attach($businessIdId, [
                    'assigned_at' => now(),
                    'previous_driver_id' => $previousDriver ? $previousDriver->id : null
                ]);
            }
        }

        session()->flash('success', translate('Driver Created Successfully!'));
        return $this->redirectRoute('drivers.index', navigate:true);
    }
}