<?php

namespace App\Traits\DMS;

use App\Enums\FieldsTypes;
use App\Enums\Range;
use App\Services\{
    FieldService,
};
use Illuminate\Validation\Rule;

trait FieldTrait
{
    // Services
    protected FieldService $fieldService;

    protected $field;

    // Properties
    public ?string $name = null;
    public mixed $type = FieldsTypes::TEXT;
    public int  $required = 0;

    public function boot(
        FieldService $fieldService,
    ) {
        $this->fieldService = $fieldService;
    }


    public function validations()
    {
        $validations['name'] = ['required', Rule::unique('fields')];
        return $this->validate($validations);
    }

}
