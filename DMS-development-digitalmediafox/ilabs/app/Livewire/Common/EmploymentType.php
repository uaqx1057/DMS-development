<?php

namespace App\Livewire\Common;

use App\Services\EmploymentTypeService;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class EmploymentType extends Component
{
    #[Modelable]
    public $employment_type_id = '';

    protected EmploymentTypeService $employmentTypeService;

    public function mount(EmploymentTypeService $employmentTypeService): void
    {
        $this->employmentTypeService = $employmentTypeService;
    }

    public function render()
    {
        $employment_types = cache()->rememberForever('employment_types', function () {
            return $this->employmentTypeService->all();
        });

        return view('livewire.common.employment-type', compact('employment_types'));
    }
}
