<?php

namespace App\Livewire\DMS\Drivers;

use App\Traits\DMS\DriverTrait;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
#[Title('Show Driver')]
class ShowDriver extends Component
{
    use WithFileUploads, DriverTrait;

    public string $main_menu = 'Drivers';
    public string $menu = 'Show Driver';

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $driver = $this->driver;
        return view('livewire.dms.drivers.show-driver', compact('main_menu', 'menu', 'driver'));
    }

    public function update(){
            // * Applying Validations
            $validated = $this->validations();

            // * Storing Image
            if($this->image){
                $validated['image'] = $this->image->store('drivers', 'public');
            }

            // * Creating Driver
            $this->driverService->update($validated, $this->driverId);

            session()->flash('success', translate('Driver Updated Successfully!'));
            return $this->redirectRoute('drivers.index', navigate:true);
    }
}
