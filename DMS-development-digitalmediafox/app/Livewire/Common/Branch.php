<?php

namespace App\Livewire\Common;

use Livewire\Attributes\Modelable;
use App\Models\Branch as bb;
use Livewire\Component;


class Branch extends Component
{
    #[Modelable]
    public $branch_id;
    public $branches = [];

    public function mount(): void
    {
        // Set branch_id for non-admins
        if (auth()->user()->role_id != 1) {
            $this->branch_id = auth()->user()->branch_id;
            $this->branches = bb::where('id', $this->branch_id)->get();
        } else {
            $this->branches = bb::all();
        }
    }

    public function render()
    {
        return view('livewire.common.branch');
    }
}
