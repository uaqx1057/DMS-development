<?php

namespace App\Livewire\Common;

use App\Services\DesignationService;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class Designation extends Component
{
    #[Modelable]
    public $designation_id = '';

    protected DesignationService $designationService;

    public function mount(DesignationService $designationService): void
    {
        $this->designationService = $designationService;
    }

    public function render()
    {
        $designations = cache()->rememberForever('designations', function () {
            return $this->designationService->all();
        });

        return view('livewire.common.designation', compact('designations'));
    }
}
