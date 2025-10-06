<?php

namespace App\Livewire\DMS\Businesses;

use App\Enums\FieldsTypes;
use App\Traits\DMS\BusinessTrait;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
#[Title('Edit Business')]

class EditBusiness extends Component
{

    use WithFileUploads, BusinessTrait;

    public string $main_menu = 'Businesss';
    public string $menu = 'Update Business';

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $fields = $this->fields;
        $types = getFieldTypes();
        $ranges = getRanges();
        $business = $this->business;
        return view('livewire.dms.businesses.edit-business', compact('main_menu', 'menu', 'fields', 'types', 'ranges', 'business'));
    }

    public function update()
    {
        // * Applying Validations
        $validated = $this->validations();
        $validated['fields'] = $this->field_ids;

        if($this->image){
            $validated['image'] = $this->image->store('Businesses', 'public');
        }

        // * Creating Business
        $this->businessService->update($validated, $this->businessId);

        session()->flash('success', translate('Business Updated Successfully!'));
        return $this->redirectRoute('business.index', navigate:true);
    }




}


