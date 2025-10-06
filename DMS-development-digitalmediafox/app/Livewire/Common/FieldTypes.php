<?php

namespace App\Livewire\Common;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class FieldTypes extends Component
{
    #[Modelable]
    public $type = '';
    public function render()
    {
        $types = getFieldTypes();
        return view('livewire.common.field-types', compact('types'));
    }

}
