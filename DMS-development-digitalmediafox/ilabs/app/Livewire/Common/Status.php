<?php

namespace App\Livewire\Common;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class Status extends Component
{
    #[Modelable]
    public $status = '';

    public function render()
    {
        $statuses = getStatuses();
        return view('livewire.common.status', compact('statuses'));
    }
}
