<?php

namespace App\Livewire\DMS\Businesses;

use App\Enums\FieldsTypes;
use App\Traits\DMS\BusinessTrait;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
#[Title('Create Business')]

class CreateBusiness extends Component
{

    use WithFileUploads, BusinessTrait;

    public string $main_menu = 'Businesss';
    public string $menu = 'Create Business';

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $fields = $this->fields;
        $types = getFieldTypes();
        $ranges = getRanges();
        return view('livewire.dms.businesses.create-business', compact('main_menu', 'menu', 'fields', 'types', 'ranges'));
    }

    public function create()
    {
        // * Applying Validations
        $validated = $this->validations();

        $validated['fields'] = $this->field_ids;
         // * Storing Image
         if($this->image){
            $validated['image'] = $this->image->store('Businesses', 'public');
        }
        $validated['driver_calculations'] = $this->driver_calculations;
        // * Creating Business
        $this->businessService->create($validated);

        // * Driver Calculations
        session()->flash('success', translate('Business Created Successfully!'));
        return $this->redirectRoute('business.index', navigate:true);
    }




}


