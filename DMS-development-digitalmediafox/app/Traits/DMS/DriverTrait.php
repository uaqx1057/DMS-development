<?php

namespace App\Traits\DMS;
use Illuminate\Support\Str;

use App\Services\{
    BranchService,
    BusinessService,
    DriverService,
    DriverTypeService,
};
use Illuminate\Validation\Rule;

trait DriverTrait
{
    // Services
    protected DriverService $driverService;
    protected BusinessService $businessService;

    protected $driver;

    // Properties
    public ?string $driverId = null;
    public ?string $name = null;
    public ?string $iqaama_number = null;
    public ?string $iqaama_expiry = null;
    public ?string $absher_number = null;
    public ?string $insurance_policy_number = null;
    public ?string $sponsorship = null;
    public ?string $sponsorship_id = null;
    public ?int $vehicle_monthly_cost = 1247;
    public ?int $fuel = 100;
    public ?int $gprs = 100;
    public ?int $government_levy_fee = 880;
    public ?int $accommodation = 200;
    
    public ?string $nationality = null;
    public ?string $language = null;
    public ?string $created_at = null; 

    
    
    
    public ?int $mobile_data = 100;
    public ?string $email = null;
    public ?string $mobile = null;
    public ?string $dob = null;
    public ?string $license_expiry = null;
    public ?string $insurance_expiry = null;
    public $image;
    public ?string $driver_type_id = null;
    public ?string $branch_id = null;
    public ?string $remarks = null;
    public array $business_ids = [];

    public function mount($id = null){
        if($id)
        {
            $this->driver = $this->driverService->find($id);
            $this->driverId = $id;
            $this->iqaama_number = $this->driver->iqaama_number;
            $this->iqaama_expiry = $this->driver->iqaama_expiry;
            $this->absher_number = $this->driver->absher_number;
            $this->insurance_policy_number = $this->driver->insurance_policy_number;
            $this->sponsorship_id = $this->driver->sponsorship_id;
            $this->vehicle_monthly_cost = $this->driver->vehicle_monthly_cost;
            $this->mobile_data = $this->driver->mobile_data;
            $this->sponsorship = $this->driver->sponsorship;
            $this->name = $this->driver->name;
            $this->email = $this->driver->email;
            $this->mobile = $this->driver->mobile;
            $this->dob = $this->driver->dob;
            $this->license_expiry = $this->driver->license_expiry;
            $this->insurance_expiry = $this->driver->insurance_expiry;
            $this->fuel = $this->driver->fuel;
            $this->gprs = $this->driver->gprs;
            $this->government_levy_fee = $this->driver->government_levy_fee;
            $this->accommodation = $this->driver->accommodation;
            
             $this->nationality = $this->driver->nationality;
             $this->language = $this->driver->language;
             $this->created_at = $this->driver->created_at;
            
            $this->driver_type_id = $this->driver->driver_type_id;
            $this->branch_id = $this->driver->branch_id;
            $this->remarks = $this->driver->remarks;
            $this->business_ids = $this->driver->businesses()->pluck('businesses.id')->toArray();
        }else{
            $this->branch_id = app(BranchService::class)->first()->id;
            $this->driver_type_id = app(DriverTypeService::class)->find(1)->id;
        }

        $this->handleBusinessSelection();
    }

    public function boot(
        DriverService $driverService,
        BusinessService $businessService,
    ) {
        $this->driverService = $driverService;
        $this->businessService = $businessService;
    }

    public function validations()
    {

        $validations = [
            'name' => 'required|string|min:3',
            'dob' => 'required|date',
            'iqaama_expiry' => 'required|date',
            'sponsorship' => 'required|string|max:255',
            'sponsorship_id' => 'required|string|max:255',
            'license_expiry' => 'required|date',
            'insurance_expiry' => 'required|date',
            'mobile_data' => 'sometimes|numeric',
            'vehicle_monthly_cost' => 'sometimes|numeric',
            'fuel' => 'sometimes|numeric',
            'gprs' => 'sometimes|numeric',
            'government_levy_fee' => 'sometimes|numeric',
            'accommodation' => 'sometimes|numeric',
            'nationality' => 'required|string',
            'language' => 'required|string',
            'created_at' => 'required|date',
            'mobile' => 'required|string|max:255',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
            'driver_type_id' => 'required|exists:driver_types,id',
            'branch_id' => 'required|exists:branches,id',
            "business_ids" => 'required|array',
            'remarks'=>'nullable|string'
        ];

        if($this->driverId != null){
            $validations['email'] = ['required', 'email', Rule::unique('drivers')->whereNull('deleted_at')->ignore($this->driverId)];
            $validations['iqaama_number'] = ['required', 'size:10', Rule::unique('drivers')->whereNull('deleted_at')->ignore($this->driverId)];
            $validations['absher_number'] = ['required', Rule::unique('drivers')->whereNull('deleted_at')->ignore($this->driverId)];
            $validations['insurance_policy_number'] = ['required', Rule::unique('drivers')->whereNull('deleted_at')->ignore($this->driverId)];
        }else{
            $validations['email'] = ['required', 'email', Rule::unique('drivers')->whereNull('deleted_at')];
            $validations['iqaama_number'] = ['required', 'size:10', Rule::unique('drivers')->whereNull('deleted_at')];
            $validations['absher_number'] = ['required', Rule::unique('drivers')->whereNull('deleted_at')];
            $validations['insurance_policy_number'] = ['required', Rule::unique('drivers')->whereNull('deleted_at')];
        }

        return $this->validate($validations);
    }

    public function handleBusinessSelection()
    {
        $businesses_with_fields = [];
        $formData = [];
        if($this->driverId){
            $this->driver = $this->driverService->find($this->driverId);
        }
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
}
