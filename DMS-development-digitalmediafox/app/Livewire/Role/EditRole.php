<?php

namespace App\Livewire\Role;

use App\Traits\Employee\RoleTrait;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Title('Edit Role')]

class EditRole extends Component
{
    use RoleTrait;
    public string $main_menu = 'Roles';
    public string $menu = 'Edit Role';

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        return view('livewire.role.edit-role', compact('main_menu', 'menu'));
    }

    public function update(){
        $validated = $this->validations();

        $this->roleService->update($validated, $this->roleId);

        session()->flash('success', translate('Role Updated Successfully!'));
        return $this->redirectRoute('role.index', navigate:true);
    }
}

