<?php

namespace App\Traits\DMS;
use Illuminate\Support\Str;

use App\Services\{
    DriverTypeService,
};
use Illuminate\Validation\Rule;

trait DriverTypeTrait
{
    // Services
    protected DriverTypeService $driverTypeService;

    protected $driverType;

    public ?string $name = null;
    public ?string $driverTypeId = null;
    public ?array $fields = [];
    public ?string $is_freelancer = '0';

    public function mount($id = null){
        if($id)
        {
            $this->driverType = $this->driverTypeService->find($id);
            $this->driverTypeId = $id;
            $this->name = $this->driverType->name;
            $this->fields = explode(',', $this->driverType->fields);
            $this->is_freelancer = $this->driverType->is_freelancer;
        }
    }

    public function boot(
        driverTypeService $driverTypeService,
    ) {
        $this->driverTypeService = $driverTypeService;
    }

    public function validations()
    {

        $validations = [];

        if($this->driverTypeId != null){
            $validations['name'] = ['required', Rule::unique('driver_types')->whereNull('deleted_at')->ignore($this->driverTypeId)];
        }else{
            $validations['name'] = ['required', Rule::unique('driver_types')->whereNull('deleted_at')];
        }

        $validations['fields'] = 'sometimes|array';
        $validations['is_freelancer'] = 'sometimes|string';

        return $this->validate($validations);
    }

}
