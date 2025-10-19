<?php

namespace App\Livewire\DMS\BusinessesId;

use App\Models\Business;
use App\Models\BusinessId;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Create Platform')]
class CreateBusinessId extends Component
{
    public string $main_menu = 'Business';
    public string $menu = 'Create Platform';
    
    public $business_id;
    public $value;
    public $is_active = true;

    protected function rules()
    {
        $rules = [
            'business_id' => 'required|exists:businesses,id',
            'value' => 'required|string|max:255',
            'is_active' => 'boolean'
        ];

        // Add custom unique validation
        if ($this->business_id && $this->value) {
            $rules['value'] .= '|unique:business_ids,value,NULL,id,business_id,' . $this->business_id;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'business_id.required' => 'Please select a business.',
            'value.required' => 'Please enter Platform value.',
            'value.unique' => 'This Platform value already exists for the selected business.',
        ];
    }

    public function updated($propertyName)
    {
        // Revalidate when business_id or value changes
        if (in_array($propertyName, ['business_id', 'value'])) {
            $this->validateOnly($propertyName);
        }
    }

    public function save()
    {
        $this->validate();

        BusinessId::create([
            'business_id' => $this->business_id,
            'value' => $this->value,
            'is_active' => $this->is_active
        ]);

        session()->flash('success', translate('Platform Created Successfully!'));
        return $this->redirectRoute('businessid.index', navigate: true);
    }

    public function render()
    {
        $businesses = Business::all();
        $main_menu = $this->main_menu;
        $menu = $this->menu;

        return view('livewire.dms.business-ids.create-business-id', compact('businesses', 'main_menu', 'menu'));
    }
}