<?php

namespace App\Livewire\DMS\Booklet;

use App\Models\Booklet;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateBooklet extends Component
{

    public string $main_menu = 'Booklet';
    public string $menu = 'Add Booklet';

    // Form fields
    public $booklet_number;
    public $operation_superviser;

    public function save()
    {
        $this->validate([
            'booklet_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('booklets', 'booklet_number')
                    ->whereNull('deleted_at'),
            ],
            'operation_superviser' => 'required',
        ]);

        Booklet::create([
            'booklet_number' => $this->booklet_number,
            'user_id' => $this->operation_superviser,
        ]);

        session()->flash('success', __('Booklet created successfully.'));
        return redirect()->route('booklet.index');
    }


    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;

        $operationSupervisers = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('name', 'operation supervisor');
            })
            ->where('branch_id', auth()->user()->branch_id)->get();
        return view('livewire.dms.booklet.create-booklet', compact(
            'main_menu',
            'menu',
            'operationSupervisers'
        ));
    }
}
