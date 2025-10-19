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
    public array $formData = [];
    public array $files = [];
    public $business_fields = [];
    public array $selectedBusinessIds = [];
    public array $availableBusinessIds = [];
    // *** NEW PROPERTY ***
    public $availableBusinesses = []; // Only businesses that have IDs for the selected driver

    public function mount($id = null){
        if($id)
        {
            $this->coordinatorReport = $this->coordinatorReportService->find($id);
            $this->driver_id = $this->coordinatorReport->driver_id;
            $this->report_date = $this->coordinatorReport->report_date;
            $this->status = $this->coordinatorReport->status;
            
            $this->business_ids = $this->coordinatorReport->businesses()->pluck('businesses.id')->toArray();
            
            $this->selectedBusinessIds = $this->coordinatorReport->report_fields()
                ->pluck('business_id_value')
                ->unique()
                ->toArray();
                
            $this->coordinatorReportId = $this->coordinatorReport->id;
            
            // Load available businesses for edit mode
            $this->loadAvailableBusinessesForEdit();
            
            $this->handleBusinessSelectionForEdit();
        }
    }

    // *** NEW METHOD: Load available businesses in edit mode ***
    private function loadAvailableBusinessesForEdit()
    {
        if (!empty($this->driver_id)) {
            $this->availableBusinesses = $this->businessService->all()->filter(function($business) {
                $hasIds = \App\Models\BusinessId::where('business_id', $business->id)
                    ->whereHas('drivers', function($query) {
                        $query->where('drivers.id', $this->driver_id)
                            ->whereNull('driver_business_ids.transferred_at');
                    })
                    ->where('is_active', true)
                    ->exists();
                
                return $hasIds;
            });
        } else {
            $this->availableBusinesses = collect([]);
        }
    }

    private function handleBusinessSelectionForEdit()
    {
        $this->availableBusinessIds = [];
        $businesses_with_fields = [];
        $formData = [];

        foreach ($this->business_ids as $businessId) {
            $business = $this->businessService->find($businessId);
            $businesses_with_fields[$businessId] = $business;

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

    // *** NEW METHOD TO FILTER BUSINESSES ***
    public function onDriverChange()
    {
        $this->resetBusinessData();
        
        if (!empty($this->driver_id)) {
            // Get only businesses that have active IDs for this driver
            $this->availableBusinesses = $this->businessService->all()->filter(function($business) {
                $hasIds = BusinessId::where('business_id', $business->id)
                    ->whereHas('drivers', function($query) {
                        $query->where('drivers.id', $this->driver_id)
                            ->whereNull('driver_business_ids.transferred_at');
                    })
                    ->where('is_active', true)
                    ->exists();
                
                return $hasIds;
            });
        } else {
            $this->availableBusinesses = collect([]);
        }
    }

    public function handleBusinessSelection()
{
    $this->availableBusinessIds = [];
    $businesses_with_fields = [];

    if (empty($this->driver_id)) {
        $this->addError('business_ids', 'Please select a driver first');
        $this->business_ids = [];
        $this->selectedBusinessIds = [];
        return;
    }

    $this->resetErrorBag('business_ids');

    // Filter selectedBusinessIds to only include IDs from selected businesses
    $filteredSelectedIds = [];
    foreach ($this->selectedBusinessIds as $selectedId) {
        $businessId = BusinessId::find($selectedId);
        if ($businessId && in_array($businessId->business_id, $this->business_ids)) {
            $filteredSelectedIds[] = $selectedId;
        }
    }
    $this->selectedBusinessIds = $filteredSelectedIds;

    // Load available IDs for selected businesses
    foreach ($this->business_ids as $businessId) {
        $business = $this->businessService->find($businessId);

        $availableIds = BusinessId::where('business_id', $businessId)
            ->whereHas('drivers', function($query) {
                $query->where('drivers.id', $this->driver_id)
                    ->whereNull('driver_business_ids.transferred_at');
            })
            ->where('is_active', true)
            ->get();

        $this->availableBusinessIds[$businessId] = $availableIds;
        $businesses_with_fields[$businessId] = $business;
    }

    $this->businesses_with_fields = $businesses_with_fields;
    
    $this->initializeFormData();
}

  

    private function initializeFormData()
{
    $formData = $this->formData;
    $files = $this->files;

    // Add new business IDs to form data
    foreach ($this->selectedBusinessIds as $businessIdValue) {
        $businessId = BusinessId::find($businessIdValue);
        if ($businessId && isset($this->businesses_with_fields[$businessId->business_id])) {
            $business = $this->businesses_with_fields[$businessId->business_id];
            
            if (!isset($formData[$businessIdValue])) {
                $formData[$businessIdValue] = [];
            }
            
            foreach ($business->fields as $field) {
                if (!isset($formData[$businessIdValue][$field->short_name])) {
                    $formData[$businessIdValue][$field->short_name] = '';
                }
            }
        }
    }

    // Remove unselected business IDs
    foreach ($formData as $businessIdValue => $fields) {
        if (!in_array($businessIdValue, $this->selectedBusinessIds)) {
            unset($formData[$businessIdValue]);
            if (isset($files[$businessIdValue])) {
                unset($files[$businessIdValue]);
            }
        }
    }

    $this->formData = $formData;
    $this->files = $files;
    
    \Log::info("Form data initialized", [
        'selected_business_ids' => $this->selectedBusinessIds,
        'form_data_keys' => array_keys($formData)
    ]);
}

    public function validations()
    {
        $validations = [
            "driver_id" => 'required|exists:drivers,id',
            "selectedBusinessIds" => 'required|array|min:1',
            "report_date" => 'required|date',
        ];

        foreach ($this->formData as $businessIdValue => $fields) {
            $businessId = BusinessId::find($businessIdValue);
            
            if ($businessId && isset($this->businesses_with_fields[$businessId->business_id])) {
                $business = $this->businesses_with_fields[$businessId->business_id];
                
                foreach ($fields as $fieldKey => $value) {
                    $field = $this->findFieldObject($businessId->business_id, $fieldKey);

                    if ($field) {
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
        $this->business_ids = [];
        $this->selectedBusinessIds = [];
        $this->availableBusinessIds = [];
        $this->businesses_with_fields = [];
        $this->formData = [];
        $this->files = [];
        
        $this->resetErrorBag(['business_ids', 'selectedBusinessIds']);
    }

    public function handleBusinessSelectionUpdated()
    {
        $businesses_with_fields = [];
        $formData = [];

         foreach ($this->business_ids as $businessId) {
            $businesses_with_fields[] = $this->businessService->find($businessId);
            foreach ($this->business_fields as $field) {
                    $formData[$field->business_id][$field->field->short_name] = $field->value;
            }
        }

        $this->businesses_with_fields = $businesses_with_fields;
        $this->formData = $formData;
    }

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