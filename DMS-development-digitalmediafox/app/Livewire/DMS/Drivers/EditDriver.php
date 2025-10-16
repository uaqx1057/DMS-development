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
    
    // ADD THESE PROPERTIES
    public $selectedBusinessIds = [];
    public $availableBusinessIds = [];

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
        $this->selectedBusinessIds = $this->driver->businessIds->pluck('id')->toArray();
        
        // Load available business IDs
        $this->loadAvailableBusinessIds();
    }

    // Handle business selection change
    public function updatedBusinessIds()
    {
        $this->loadAvailableBusinessIds();
    }

    // Load available business IDs
    private function loadAvailableBusinessIds()
    {
        $this->availableBusinessIds = [];
        
        if (!empty($this->business_ids)) {
            foreach ($this->business_ids as $businessId) {
                // Remove the complex where condition to show ALL business IDs
                $availableIds = BusinessId::where('business_id', $businessId)
                    ->where('is_active', true)
                    ->get(); // Show ALL active business IDs
                
                $this->availableBusinessIds[$businessId] = $availableIds;
            }
        }
    }

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $driver = $this->driver;
        $businesses = $this->businessService->all();
        return view('livewire.dms.drivers.edit-driver', compact('main_menu', 'menu', 'driver', 'businesses'));
    }

    public function update(){
        // * Applying Validations
        $validated = $this->validations();

        // * Storing Image
        if($this->image){
            $validated['image'] = $this->image->store('drivers', 'public');
        }

        // * Updating Driver
        $this->driverService->update($validated, $this->driverId);

        // * Update business ID assignments
        $this->updateBusinessIdAssignments();

        session()->flash('success', translate('Driver Updated Successfully!'));
        return $this->redirectRoute('drivers.index', navigate:true);
    }

    // Update business ID assignments
    private function updateBusinessIdAssignments()
    {
        $driver = $this->driverService->find($this->driverId);
        
        // Get current assignments
        $currentAssignments = $driver->businessIds->pluck('id')->toArray();
        
        // IDs to remove (were assigned but now unselected)
        $idsToRemove = array_diff($currentAssignments, $this->selectedBusinessIds);
        
        // IDs to add (newly selected)
        $idsToAdd = array_diff($this->selectedBusinessIds, $currentAssignments);
        
        // Remove unselected business IDs
        foreach ($idsToRemove as $businessIdId) {
            \DB::table('driver_business_ids')
                ->where('driver_id', $driver->id)
                ->where('business_id_id', $businessIdId)
                ->where('transferred_at', null)
                ->update(['transferred_at' => now()]);
        }

        // Add new business IDs with transfer logic
        foreach ($idsToAdd as $businessIdId) {
            // Check if assigned to another driver
            $existingAssignment = \DB::table('driver_business_ids')
                ->where('business_id_id', $businessIdId)
                ->where('transferred_at', null)
                ->first();

            if ($existingAssignment) {
                // Transfer from previous driver (only if it's not already assigned to this driver)
                if ($existingAssignment->driver_id != $driver->id) {
                    \DB::table('driver_business_ids')
                        ->where('business_id_id', $businessIdId)
                        ->where('transferred_at', null)
                        ->update(['transferred_at' => now()]);
                    
                    $previousDriverId = $existingAssignment->driver_id;
                } else {
                    $previousDriverId = null;
                }
            } else {
                $previousDriverId = null;
            }

            // Assign to current driver (only if not already assigned)
            if (!$existingAssignment || $existingAssignment->driver_id != $driver->id) {
                $driver->businessIds()->attach($businessIdId, [
                    'assigned_at' => now(),
                    'previous_driver_id' => $previousDriverId
                ]);
            }
        }
    }
}