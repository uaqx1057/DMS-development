<?php

namespace App\Livewire\Common;

use App\Services\DepartmentService;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class Department extends Component
{
    #[Modelable]
    public $department_id = '';

    protected DepartmentService $departmentService;

    public function mount(DepartmentService $departmentService): void
    {
        $this->departmentService = $departmentService;
    }

    public function render()
    {
        $departments = cache()->rememberForever('departments', function () {
            return $this->departmentService->all();
        });

        return view('livewire.common.department', compact('departments'));
    }
}
