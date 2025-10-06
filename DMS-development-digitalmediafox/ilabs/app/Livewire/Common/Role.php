<?php

namespace App\Livewire\Common;

use App\Services\RoleService;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class Role extends Component
{
    #[Modelable]
    public $role_id = '';

    protected RoleService $roleService;

    public function boot(RoleService $roleService): void
    {
        $this->roleService = $roleService;
    }

    public function render()
    {
        $roles = $this->roleService->all();


        return view('livewire.common.role', compact('roles'));
    }
}
