<?php

namespace App\Livewire\DMS\DriverTypes;

use App\Traits\DMS\DriverTypeTrait;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;

#[Title('Edit Driver Type')]
class EditDriverType extends Component
{

    use WithFileUploads, DriverTypeTrait;

    public string $main_menu = 'DMS';
    public string $menu = 'Edit Driver Type';

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;

        return view('livewire.dms.driver-types.edit-driver-type', compact('main_menu', 'menu'));
    }

    public function update(){
            // * Applying Validations
            $validated = $this->validations();

            if(count($validated['fields']) > 0){
                $validated['fields'] = implode(',', $validated['fields']);
            }
            // * Creating Driver Type
            $this->driverTypeService->update($validated, $this->driverTypeId);

            session()->flash('success', translate('Driver Type Updated Successfully!'));
            return $this->redirectRoute('driver-types.index', navigate:true);
    }




}
