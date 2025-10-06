<?php

namespace App\Livewire\Common;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class MaritalStatus extends Component
{
    #[Modelable]
    public $marital_status = '';

    public function render()
    {
        $marital_statuses = getMaritalStatuses();
        return view('livewire.common.marital-status', compact('marital_statuses'));
    }
}
