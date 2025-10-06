<?php

namespace App\Livewire\Common;

use App\Services\DriverTypeService;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class DriverType extends Component
{
    #[Modelable]
    public $driver_type_id = '';

    protected DriverTypeService $driverTypeService;

    public function boot(DriverTypeService $driverTypeService): void
    {
        $this->driverTypeService = $driverTypeService;
    }

    public function render()
    {
        $driver_types =  $this->driverTypeService->all();

        return view('livewire.common.driver-type', compact('driver_types'));
    }
}
