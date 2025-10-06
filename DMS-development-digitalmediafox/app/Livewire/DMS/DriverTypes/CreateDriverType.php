<?php

namespace App\Livewire\DMS\DriverTypes;

use App\Traits\DMS\DriverTypeTrait;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;

#[Title('Create Driver Type')]
class CreateDriverType extends Component
{

    use WithFileUploads, DriverTypeTrait;

    public string $main_menu = 'DMS';
    public string $menu = 'Create Driver Type';

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;

        return view('livewire.dms.driver-types.create-driver-type', compact('main_menu', 'menu'));
    }

    public function create(){
            // * Applying Validations
            $validated = $this->validations();

            if(count($validated['fields']) > 0){
                $validated['fields'] = implode(',', $validated['fields']);
            }
            // * Creating Driver Type
            $this->driverTypeService->create($validated);

            session()->flash('success', translate('Driver Type Created Successfully!'));
            return $this->redirectRoute('driver-types.index', navigate:true);
    }




}
