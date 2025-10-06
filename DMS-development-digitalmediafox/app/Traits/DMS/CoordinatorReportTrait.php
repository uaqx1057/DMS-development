<?php

namespace App\Traits\DMS;

use App\Enums\CoordinatorReportStatus;
use App\Services\{
    BusinessService,
    CoordinatorReportService,
    DriverService,
    FieldService,
};

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

    public function mount($id = null){
        if($id)
        {
            $this->coordinatorReport = $this->coordinatorReportService->find($id);
            // dd($this->coordinatorReport->report_fields);
            $this->driver_id = $this->coordinatorReport->driver_id;
            $this->report_date = $this->coordinatorReport->report_date;
            $this->business_ids = $this->coordinatorReport->businesses()->pluck('businesses.id')->toArray();
            $this->business_fields = $this->coordinatorReport->report_fields()->get();
            $this->coordinatorReportId = $this->coordinatorReport->id;
            // dd($this->business_fields[0]);
            $this->handleBusinessSelectionUpdated();
        }
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
        $businesses_with_fields = [];
        $formData = [];

        // Loop through selected businesses
        foreach ($this->business_ids as $businessId) {
            $business = $this->businessService->find($businessId); // Fetch business with its fields

            // Add business to the array of businesses with fields
            $businesses_with_fields[] = $business;

            // Initialize formData for each business field
            foreach ($business->fields as $field) {
                $formData[$businessId][$field->short_name] = ''; // Initialize empty value for each field
            }
        }

        // Set the dynamically populated businesses and formData
        $this->businesses_with_fields = $businesses_with_fields;
        $this->formData = $formData;
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


    public function validations()
    {
        // Define the static validation rules first
        $validations = [
            "driver_id" => 'required|exists:drivers,id',
            "business_ids" => 'required|array',
            "report_date" => 'required|date',
        ];
        // dd($this->files);
        // Loop through each business and dynamically apply validation
        foreach ($this->formData as $businessId => $fields) {
            foreach ($fields as $fieldKey => $value) {
                // Find the field object for the current field key to check its 'required' property
                $field = $this->findFieldObject($businessId, $fieldKey);

                if ($field) {
                    // Apply 'required' if the field is required, otherwise use 'sometimes'
                    $validations["formData.{$businessId}.{$fieldKey}"] = $field->required ? 'required' : 'sometimes';
                }
            }

            if (isset($this->files[$businessId])) {
                foreach ($this->files[$businessId] as $fileField => $fileArray) {
                    // Since `upload_driver_documents` is an array of files, loop through each file and validate
                    foreach ($fileArray as $index => $file) {
                        $validations["files.{$businessId}.{$fileField}.{$index}"] = 'sometimes|nullable|file|mimes:jpeg,png,jpg';
                    }
                }
            }

        }
        // dd($validations);
        // Perform the validation in a single call
        return $this->validate($validations);

    }

    // A helper method to find the field object by its short_name (fieldKey) in a business
    public function findFieldObject($businessId, $fieldKey)
    {
        foreach ($this->businesses_with_fields as $business) {
            if ($business->id == $businessId) {
                foreach ($business->fields as $field) {
                    if ($field->short_name == $fieldKey) {
                        return $field;
                    }
                }
            }
        }

        return null; // Return null if the field object is not found
    }

}
