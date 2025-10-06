<?php

namespace App\Livewire\Common;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class Gender extends Component
{
    #[Modelable]
    public $gender = '';
    public function render()
    {
        $genders = getGenders();
        return view('livewire.common.gender', compact('genders'));
    }
}
