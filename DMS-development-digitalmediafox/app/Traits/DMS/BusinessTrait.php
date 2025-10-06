<?php

namespace App\Traits\DMS;

use App\Enums\FieldsTypes;
use App\Enums\Range;
use App\Services\{
    BusinessService,
    FieldService,
};
use Illuminate\Validation\Rule;

trait BusinessTrait
{
    // Services
    protected BusinessService $businessService;
    protected FieldService $fieldService;

    protected $business;

    // Properties
    public ?string $businessId = null;
    public ?string $name = null;
    public $branch_id;
    public ?string $add_field_name = null;
    public mixed $add_field_type = FieldsTypes::TEXT;
    public int  $add_required = 0;
    public string $add_calculation_type = Range::RANGE->value;
    public ?float $add_from = null;
    public ?float $add_to = null;
    public ?float $add_amount = null;
    public $image;
    public array $fields = [];
    public array $driver_calculations = [];
    public array $field_ids = ["1","2","3","4", "5"];
    public function mount($id = null){
        if($id)
        {
            $this->business = $this->businessService->find($id);
            $this->businessId = $id;
            $this->name = $this->business->name;
            $this->branch_id = $this->business->branch_id;
            $this->driver_calculations  = $this->business->driver_calculations->toArray();
            $this->field_ids = $this->business->fields()->pluck('id')->toArray();

        }
        $this->fields  = $this->fieldService->all()->toArray();

    }

    public function boot(
        BusinessService $businessService,
        FieldService $fieldService,
    ) {
        $this->businessService = $businessService;
        $this->fieldService = $fieldService;
    }

    public function updateDriverRange(){
        $this->add_calculation_type = $this->add_calculation_type;

    }



    public function addDriverCalculation(){
        $this->validate([
            'add_from' => $this->add_calculation_type == 'RANGE' ? 'required|numeric' : 'sometimes',
            'add_to' =>  $this->add_calculation_type == 'RANGE' ? 'required|numeric|gt:add_from' : 'sometimes',
            'add_amount' => 'required|numeric|gt:0',
            'add_calculation_type' => 'required|string',
        ]);

        // Add the field to the business fields array
        $this->driver_calculations[] = [
            'calculation_type' => $this->add_calculation_type,
            'from' => $this->add_from,
            'to' => $this->add_to,
            'amount' => $this->add_amount,
        ];

        // Reset the input fields after adding
        $this->reset('add_calculation_type', 'add_from', 'add_to', 'add_amount');
    }

    public function deleteDriverCalculation($index){
        unset($this->driver_calculations[$index]);
        $this->driver_calculations = array_values($this->driver_calculations);
    }


    public function validations()
    {

        $validations =  [
            // 'driver_calculations.*.calculation_type' => 'sometimes|min:2|max:255',
            // 'driver_calculations.*.from' => 'sometimes',
            // 'driver_calculations.*.to' => 'sometimes',
            // 'driver_calculations.*.amount' => 'sometimes',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',

        ];

        if($this->businessId != null){
            $validations['name'] = ['required', Rule::unique('businesses')->whereNull('deleted_at')->ignore($this->businessId)];
             $validations['branch_id'] = ['required'];
        }else{
            $validations['name'] = ['required', Rule::unique('businesses')->whereNull('deleted_at')];
            $validations['branch_id'] = ['required'];
        }

        return $this->validate($validations);
    }

}
