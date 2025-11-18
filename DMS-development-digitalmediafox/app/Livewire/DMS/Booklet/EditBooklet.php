<?php

namespace App\Livewire\DMS\Booklet;

use App\Models\Booklet;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EditBooklet extends Component
{
    public string $main_menu = 'Booklet';
    public string $menu = 'Edit Booklet';

    // Form fields
    public $booklet_number;
    public $operation_superviser;

    public $booklet_id;

    public function mount($id)
    {
        $this->booklet_id = $id;
        $this->booklet = Booklet::find($id);
        
        // Check if driver exists
        if (!$this->booklet) {
            $this->bookletExists = false;
            session()->flash('error', translate('Driver not found!'));
            return;
        }
        
        // Load current driver data
        $this->booklet_number = $this->booklet->booklet_number;
        $this->operation_superviser = $this->booklet->user_id;
    }
    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;

        $operationSupervisers = User::where('role_id', 8);
        
        if(auth()->user()->role_id != 1){
            $operationSupervisers = $operationSupervisers->where('branch_id', auth()->user()->branch_id);
        }
        $operationSupervisers = $operationSupervisers->get();
        return view('livewire.dms.booklet.edit-booklet', compact(
            'main_menu',
            'menu',
            'operationSupervisers'
        ));
    }

    public function update()
    {
        $this->validate([
            'booklet_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('booklets', 'booklet_number')
                    ->ignore($this->booklet_id)  // ignore current record
                    ->whereNull('deleted_at'),   // ignore soft-deleted records
            ],
            'operation_superviser' => 'required',
        ]);

        Booklet::where('id', $this->booklet_id)->update([
            'booklet_number' => $this->booklet_number,
            'user_id' => $this->operation_superviser,
        ]);

        session()->flash('success', __('Booklet updated successfully.'));
        return redirect()->route('booklet.index');
    }
}
