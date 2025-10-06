<?php

namespace App\Livewire\Common;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class Salutation extends Component
{
    #[Modelable]
    public ?string $salutation = null;
    public function render()
    {
        $salutations = getSalutations();
        return view('livewire.common.salutation', compact('salutations'));
    }
}
