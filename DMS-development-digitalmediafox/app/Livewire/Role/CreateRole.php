<?php

namespace App\Livewire\Role;

use App\Traits\Employee\RoleTrait;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Title('Create Role')]

class CreateRole extends Component
{
    use RoleTrait;
    public string $main_menu = 'Roles';
    public string $menu = 'Create Role';

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        return view('livewire.role.create-role', compact('main_menu', 'menu'));
    }

    public function create(){
        $validated = $this->validations();

        $role = $this->roleService->create($validated);

        session()->flash('success', translate('Role Created Successfully!'));
        return $this->redirectRoute('role.permission', $role->id, navigate:true);
    }
}

