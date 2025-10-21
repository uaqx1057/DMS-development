<?php

namespace App\Livewire\DMS\Drivers;

use App\Traits\DMS\DriverTrait;
use App\Services\BusinessService;
use App\Services\DriverService;
use App\Models\BusinessId;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;

#[Title('Edit Driver')]
class EditDriver extends Component
{
    use WithFileUploads, DriverTrait;

    public string $main_menu = 'Drivers';
    public string $menu = 'Edit Driver';
    
    // Assigned IDs (checkboxes)
    public $assignedBusinessIds = [];
    
    // New IDs to add (select boxes)
    public $newBusinessIds = [];
    public $availableBusinessIds = [];
    
    // Show add section
    public $showAddSection = false;

    // Add this property to track if driver exists
    public $driverExists = true;

    protected DriverService $driverService;
    protected BusinessService $businessService;

    public function boot(DriverService $driverService, BusinessService $businessService)
    {
        $this->driverService = $driverService;
        $this->businessService = $businessService;
    }

    public function mount($id)
    {
        $this->driverId = $id;
        $this->driver = $this->driverService->find($id);
        
        // Check if driver exists
        if (!$this->driver) {
            $this->driverExists = false;
            session()->flash('error', translate('Driver not found!'));
            return;
        }
        
        // Load current driver data
        $this->name = $this->driver->name;
        $this->branch_id = $this->driver->branch_id;
        $this->iqaama_number = $this->driver->iqaama_number;
        $this->iqaama_expiry = $this->driver->iqaama_expiry;
        $this->driver_type_id = $this->driver->driver_type_id;
        $this->dob = $this->driver->dob;
        $this->absher_number = $this->driver->absher_number;
        $this->sponsorship = $this->driver->sponsorship;
        $this->sponsorship_id = $this->driver->sponsorship_id;
        $this->license_expiry = $this->driver->license_expiry;
        $this->insurance_policy_number = $this->driver->insurance_policy_number;
        $this->insurance_expiry = $this->driver->insurance_expiry;
        $this->vehicle_monthly_cost = $this->driver->vehicle_monthly_cost;
        $this->mobile_data = $this->driver->mobile_data;
        $this->fuel = $this->driver->fuel;
        $this->gprs = $this->driver->gprs;
        $this->government_levy_fee = $this->driver->government_levy_fee;
        $this->accommodation = $this->driver->accommodation;
        $this->nationality = $this->driver->nationality;
        $this->language = $this->driver->language;
        $this->created_at = $this->driver->created_at;
        $this->remarks = $this->driver->remarks;
        $this->email = $this->driver->email;
        $this->mobile = $this->driver->mobile;
        $this->business_ids = $this->driver->businesses->pluck('id')->toArray();
        
        // Load currently assigned business IDs
        $this->assignedBusinessIds = $this->driver->businessIds()
            ->wherePivot('transferred_at', null)
            ->pluck('business_ids.id')
            ->toArray();
    }

    // Toggle add section
    public function toggleAddSection()
    {
        if (!$this->driverExists) {
            return;
        }
        
        $this->showAddSection = !$this->showAddSection;
        if ($this->showAddSection) {
            $this->loadAvailableBusinessIds();
        }
    }

    // Handle business selection for new IDs
    public function updatedBusinessIds()
    {
        if (!$this->driverExists) {
            return;
        }
        
        $this->loadAvailableBusinessIds();
    }

    // Load available (unassigned) business IDs
    private function loadAvailableBusinessIds()
    {
        $this->availableBusinessIds = [];
        
        if (!empty($this->business_ids)) {
            foreach ($this->business_ids as $businessId) {
                // Get only unassigned business IDs
                $availableIds = BusinessId::where('business_id', $businessId)
                    ->where('is_active', true)
                    ->whereDoesntHave('drivers', function($query) {
                        $query->whereNull('transferred_at');
                    })
                    ->get();
                
                $this->availableBusinessIds[$businessId] = $availableIds;
            }
        }
    }

    // Update selected new business IDs
    public function updateNewBusinessIds($businessId, $selectedIds)
    {
        $this->newBusinessIds[$businessId] = $selectedIds;
    }

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $driver = $this->driver;
        $businesses = $this->businessService->all();
        
        // Check if driver exists
        if (!$this->driverExists) {
            return view('livewire.dms.drivers.edit-driver', compact(
                'main_menu', 
                'menu'
            ));
        }
        
        // Group assigned IDs by business
        $assignedIdsByBusiness = [];
        if (!empty($this->assignedBusinessIds)) {
            $assignedIds = BusinessId::whereIn('id', $this->assignedBusinessIds)
                ->with('business')
                ->get();
            
            foreach ($assignedIds as $id) {
                $businessId = $id->business_id;
                if (!isset($assignedIdsByBusiness[$businessId])) {
                    $assignedIdsByBusiness[$businessId] = [];
                }
                $assignedIdsByBusiness[$businessId][] = $id;
            }
        }
        
        return view('livewire.dms.drivers.edit-driver', compact(
            'main_menu', 
            'menu', 
            'driver', 
            'businesses',
            'assignedIdsByBusiness'
        ));
    }

    public function update()
    {
        // Check if driver exists
        if (!$this->driverExists) {
            session()->flash('error', translate('Driver not found!'));
            return $this->redirectRoute('drivers.index', navigate: true);
        }

        // Applying Validations
        $validated = $this->validations();

        // Storing Image
        if($this->image){
            $validated['image'] = $this->image->store('drivers', 'public');
        }

        // Updating Driver
        $this->driverService->update($validated, $this->driverId);

        // Update business ID assignments
        $this->updateBusinessIdAssignments();

        session()->flash('success', translate('Driver Updated Successfully!'));
        return $this->redirectRoute('drivers.index', navigate:true);
    }

    // Update business ID assignments
    private function updateBusinessIdAssignments()
    {
        $driver = $this->driverService->find($this->driverId);
        
        if (!$driver) {
            return;
        }
        
        // Get current active assignments
        $currentAssignments = $driver->businessIds()
            ->wherePivot('transferred_at', null)
            ->pluck('business_ids.id')
            ->toArray();
        
        // Determine which IDs were unchecked (to close them)
        $idsToRemove = array_diff($currentAssignments, $this->assignedBusinessIds);
        
        // Determine new IDs to add
        $newIdsToAdd = [];
        foreach ($this->newBusinessIds as $businessId => $ids) {
            $newIdsToAdd = array_merge($newIdsToAdd, $ids);
        }
        
        // ✅ Mark unchecked business IDs as transferred (close active assignments)
        foreach ($idsToRemove as $businessIdId) {
            \DB::table('driver_business_ids')
                ->where('driver_id', $driver->id)
                ->where('business_id_id', $businessIdId)
                ->where('transferred_at', null)
                ->update(['transferred_at' => now()]);
        }

        // ✅ Always create new assignment records
        foreach ($newIdsToAdd as $businessIdId) {
            // Check if the ID is currently assigned to another driver
            $existingAssignment = \DB::table('driver_business_ids')
                ->where('business_id_id', $businessIdId)
                ->where('transferred_at', null)
                ->first();

            $previousDriverId = null;

            // If assigned to another driver, mark that assignment as transferred
            if ($existingAssignment && $existingAssignment->driver_id != $driver->id) {
                \DB::table('driver_business_ids')
                    ->where('business_id_id', $businessIdId)
                    ->where('transferred_at', null)
                    ->update(['transferred_at' => now()]);

                $previousDriverId = $existingAssignment->driver_id;
            }

            // ❌ OLD (reactivate old assignment)
            // -> removed completely

            // ✅ NEW (always create a fresh record)
            $driver->businessIds()->attach($businessIdId, [
                'assigned_at' => now(),
                'previous_driver_id' => $previousDriverId,
            ]);
        }
    }

}