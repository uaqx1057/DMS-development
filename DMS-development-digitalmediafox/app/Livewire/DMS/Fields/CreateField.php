<?php

namespace App\Livewire\DMS\Fields;

use App\Traits\DMS\FieldTrait;
use Livewire\Attributes\Title;
use Illuminate\Support\Str;
use Livewire\Component;
#[Title('Create Business Field')]

class CreateField extends Component
{
    use FieldTrait;
    public string $main_menu = 'Business Fields';
    public string $menu = 'Create Business Field';

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        return view('livewire.dms.fields.create-field', compact('main_menu', 'menu'));
    }

    public function create(){
        $validated = $this->validations();
        $validated['type'] = $this->type;
        $validated['short_name'] = Str::lower(str_replace(' ', '_', $validated['name']));
        $validated['required'] = $this->required;

        $this->fieldService->create($validated);

        session()->flash('success', translate('Business Field Created Successfully!'));
        return $this->redirectRoute('field.index', navigate:true);
    }
}
