<?php

namespace App\Livewire\DMS\BusinessesId;

use App\Models\Business;
use App\Models\BusinessId;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Edit Business ID')]
class EditBusinessId extends Component
{
    public string $main_menu = 'Business';
    public string $menu = 'Edit Business ID';
    
    public $businessId;
    public $business_id;
    public $value;
    public $is_active = true;

    public function mount($id)
    {
        $this->businessId = BusinessId::findOrFail($id);
        $this->business_id = $this->businessId->business_id;
        $this->value = $this->businessId->value;
        $this->is_active = $this->businessId->is_active;
    }

    protected function rules()
    {
        return [
            'business_id' => 'required|exists:businesses,id',
            'value' => [
                'required',
                'string',
                'max:255',
                'unique:business_ids,value,' . $this->businessId->id . ',id,business_id,' . $this->business_id
            ],
            'is_active' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'business_id.required' => 'Please select a business.',
            'value.required' => 'Please enter Business ID value.',
            'value.unique' => 'This Business ID value already exists for the selected business.',
        ];
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['business_id', 'value'])) {
            $this->validateOnly($propertyName);
        }
    }

    public function update()
    {
        $this->validate();

        $this->businessId->update([
            'business_id' => $this->business_id,
            'value' => $this->value,
            'is_active' => $this->is_active
        ]);

        session()->flash('success', translate('Business ID Updated Successfully!'));
        return $this->redirectRoute('businessid.index', navigate: true);
    }

    public function render()
    {
        $businesses = Business::all();
        $main_menu = $this->main_menu;
        $menu = $this->menu;

        return view('livewire.dms.business-ids.edit-business-id', compact('businesses', 'main_menu', 'menu'));
    }
}