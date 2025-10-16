<?php

namespace App\Traits\DMS;

use App\Enums\CoordinatorReportStatus;
use App\Services\{
    BusinessService,
    CoordinatorReportService,
    DriverService,
    FieldService,
};
use App\Models\BusinessId;

trait CoordinatorReportTrait
{
    // Services
    protected BusinessService $businessService;
    protected DriverService $driverService;
    protected FieldService $fieldService;
    protected CoordinatorReportService $coordinatorReportService;

    // Properties
    public ?string $coordinatorReportId = null;
    public $coordinatorReport = null;
    public array $fields = [];
    public string $report_date;
    public string $driver_id;
    public string $status = CoordinatorReportStatus::Pending->value;
    public array $business_ids = [];
    public array $businesses_with_fields = [];
    public array $formData = []; // Store form data business-wise
    public array $files = [];
    public $business_fields = [];
    public array $selectedBusinessIds = []; // For multi-select business IDs
    public array $availableBusinessIds = [];// Available IDs for selected businesses

    public function mount($id = null){
        if($id)
        {
            $this->coordinatorReport = $this->coordinatorReportService->find($id);
            $this->driver_id = $this->coordinatorReport->driver_id;
            $this->report_date = $this->coordinatorReport->report_date;
            $this->status = $this->coordinatorReport->status;
            
            // Get business type IDs from the relationship
            $this->business_ids = $this->coordinatorReport->businesses()->pluck('businesses.id')->toArray();
            
            // Get selected business IDs from report_fields
            $this->selectedBusinessIds = $this->coordinatorReport->report_fields()
                ->pluck('business_id_value')
                ->unique()
                ->toArray();
                
            $this->coordinatorReportId = $this->coordinatorReport->id;
            
            // Initialize available business IDs and form data
            $this->handleBusinessSelectionForEdit();
        }
    }

    private function handleBusinessSelectionForEdit()
    {
        $this->availableBusinessIds = [];
        $businesses_with_fields = [];
        $formData = [];

        // Initialize businesses_with_fields for selected business types
        foreach ($this->business_ids as $businessId) {
            $business = $this->businessService->find($businessId);
            $businesses_with_fields[$businessId] = $business;

            // Get available business IDs for the selected driver
            $availableIds = BusinessId::where('business_id', $businessId)
                ->whereHas('drivers', function($query) {
                    $query->where('drivers.id', $this->driver_id)
                        ->whereNull('driver_business_ids.transferred_at');
                })
                ->where('is_active', true)
                ->get();

            $this->availableBusinessIds[$businessId] = $availableIds;
        }

        $this->businesses_with_fields = $businesses_with_fields;

        // Initialize form data from existing report fields
        foreach ($this->coordinatorReport->report_fields as $field) {
            $businessIdValue = $field->business_id_value;
            $fieldName = $field->field->short_name;
            $value = $field->value;
            
            $formData[$businessIdValue][$fieldName] = $value;
        }

        $this->formData = $formData;
        
        \Log::info("Edit form initialized", [
            'business_ids' => $this->business_ids,
            'selected_business_ids' => $this->selectedBusinessIds,
            'form_data_keys' => array_keys($formData)
        ]);
    }

    public function boot(
        BusinessService $businessService,
        DriverService $driverService,
        CoordinatorReportService $coordinatorReportService
    ) {
        $this->businessService = $businessService;
        $this->driverService = $driverService;
        $this->coordinatorReportService = $coordinatorReportService;
    }

    public function handleBusinessSelection()
    {
        $this->availableBusinessIds = [];
        $businesses_with_fields = [];

        // Check if driver is selected first
        if (empty($this->driver_id)) {
            $this->addError('business_ids', 'Please select a driver first');
            $this->business_ids = []; // Reset business selections
            $this->selectedBusinessIds = []; // Also reset selected business IDs
            return;
        }

        // Clear any previous errors
        $this->resetErrorBag('business_ids');

        // For both create and edit, filter selectedBusinessIds to only include IDs that belong to currently selected business types
        $filteredSelectedIds = [];
        foreach ($this->selectedBusinessIds as $selectedId) {
            $businessId = BusinessId::find($selectedId);
            if ($businessId && in_array($businessId->business_id, $this->business_ids)) {
                $filteredSelectedIds[] = $selectedId;
            }
        }
        $this->selectedBusinessIds = $filteredSelectedIds;

        // Loop through selected businesses
        foreach ($this->business_ids as $businessId) {
            $business = $this->businessService->find($businessId);

            // Get available business IDs for the selected driver
            $availableIds = BusinessId::where('business_id', $businessId)
                ->whereHas('drivers', function($query) {
                    $query->where('drivers.id', $this->driver_id)
                        ->whereNull('driver_business_ids.transferred_at');
                })
                ->where('is_active', true)
                ->get();

            $this->availableBusinessIds[$businessId] = $availableIds;

            // Add business to the array of businesses with fields
            $businesses_with_fields[$businessId] = $business;
        }

        // Set the dynamically populated businesses
        $this->businesses_with_fields = $businesses_with_fields;
        
        // Initialize formData for selected business IDs
        $this->initializeFormData();
    }

    public function updatedSelectedBusinessIds()
    {
        // When business IDs selection changes, reinitialize form data
        $this->initializeFormData();
    }

    private function initializeFormData()
    {
        $formData = $this->formData; // Start with existing form data
        $files = $this->files;

        foreach ($this->selectedBusinessIds as $businessIdValue) {
            $businessId = BusinessId::find($businessIdValue);
            if ($businessId && isset($this->businesses_with_fields[$businessId->business_id])) {
                $business = $this->businesses_with_fields[$businessId->business_id];
                
                // Only initialize if not already set (preserve existing data)
                if (!isset($formData[$businessIdValue])) {
                    $formData[$businessIdValue] = [];
                }
                
                foreach ($business->fields as $field) {
                    if (!isset($formData[$businessIdValue][$field->short_name])) {
                        $formData[$businessIdValue][$field->short_name] = ''; // Initialize empty if not exists
                    }
                }
            }
        }

        // Remove form data for business IDs that are no longer selected
        foreach ($formData as $businessIdValue => $fields) {
            if (!in_array($businessIdValue, $this->selectedBusinessIds)) {
                unset($formData[$businessIdValue]);
                // Also remove any files for this business ID
                if (isset($files[$businessIdValue])) {
                    unset($files[$businessIdValue]);
                }
            }
        }

        $this->formData = $formData;
        $this->files = $files;
    }

    public function validations()
    {
        // Define the static validation rules first
        $validations = [
            "driver_id" => 'required|exists:drivers,id',
            "selectedBusinessIds" => 'required|array|min:1',
            "report_date" => 'required|date',
        ];

        // Loop through each business ID and dynamically apply validation
        foreach ($this->formData as $businessIdValue => $fields) {
            $businessId = BusinessId::find($businessIdValue);
            
            if ($businessId && isset($this->businesses_with_fields[$businessId->business_id])) {
                $business = $this->businesses_with_fields[$businessId->business_id];
                
                foreach ($fields as $fieldKey => $value) {
                    // Find the field object for the current field key to check its 'required' property
                    $field = $this->findFieldObject($businessId->business_id, $fieldKey);

                    if ($field) {
                        // Apply 'required' if the field is required, otherwise use 'sometimes'
                        $validations["formData.{$businessIdValue}.{$fieldKey}"] = $field->required ? 'required' : 'sometimes';
                    }
                }

                if (isset($this->files[$businessIdValue])) {
                    foreach ($this->files[$businessIdValue] as $fileField => $fileArray) {
                        foreach ($fileArray as $index => $file) {
                            $validations["files.{$businessIdValue}.{$fileField}.{$index}"] = 'sometimes|nullable|file|mimes:jpeg,png,jpg';
                        }
                    }
                }
            }
        }

        \Log::info("Validation rules prepared", [
            'total_validation_rules' => count($validations),
            'selected_business_ids' => $this->selectedBusinessIds
        ]);

        return $this->validate($validations);
    }

    public function resetBusinessData()
    {
        // Reset all business-related data
        $this->business_ids = [];
        $this->selectedBusinessIds = [];
        $this->availableBusinessIds = [];
        $this->businesses_with_fields = [];
        $this->formData = [];
        $this->files = [];
        
        // Clear any business-related errors
        $this->resetErrorBag(['business_ids', 'selectedBusinessIds']);
    }

    public function handleBusinessSelectionUpdated()
    {
        $businesses_with_fields = [];
        $formData = [];

         // Loop through selected businesses
         foreach ($this->business_ids as $businessId) {
            $businesses_with_fields[] = $this->businessService->find($businessId);
            foreach ($this->business_fields as $field) {
                    $formData[$field->business_id][$field->field->short_name] = $field->value;
            }
        }

        $this->businesses_with_fields = $businesses_with_fields;
        $this->formData = $formData;
    }

    // Helper method to find field object
    public function findFieldObject($businessTypeId, $fieldKey)
    {
        if (isset($this->businesses_with_fields[$businessTypeId])) {
            $business = $this->businesses_with_fields[$businessTypeId];
            foreach ($business->fields as $field) {
                if ($field->short_name == $fieldKey) {
                    return $field;
                }
            }
        }
        return null;
    }
}