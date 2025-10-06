<?php

namespace App\Livewire\DMS\Drivers;

use App\Traits\DMS\DriverTrait;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
#[Title('Create Driver')]
class CreateDriver extends Component
{

    use WithFileUploads, DriverTrait;

    public string $main_menu = 'Drivers';
    public string $menu = 'Create Driver';

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $businesses = $this->businessService->all();
        return view('livewire.dms.drivers.create-driver', compact('main_menu', 'menu', 'businesses'));
    }

    public function create(){
            // * Applying Validations
            $validated = $this->validations();
            // * Storing Image
            if($this->image){
                $validated['image'] = $this->image->store('drivers', 'public');
            }

            // * Creating Driver
            $this->driverService->create($validated);

            session()->flash('success', translate('Driver Created Successfully!'));
            return $this->redirectRoute('drivers.index', navigate:true);
    }




}
