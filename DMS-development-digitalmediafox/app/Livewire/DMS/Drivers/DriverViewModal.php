<?php

namespace App\Livewire\DMS\Drivers;

use App\Models\Driver;
use Livewire\Component;

class DriverViewModal extends Component
{
    public $showModal = false;
    public $driverData;

    protected $listeners = ['openDriverView' => 'show'];

    public function show($driverData)
    {
        $this->driverData = $driverData;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset('driverData');
    }

    public function render()
    {
        return view('livewire.dms.drivers.driver-view-modal');
    }
}


