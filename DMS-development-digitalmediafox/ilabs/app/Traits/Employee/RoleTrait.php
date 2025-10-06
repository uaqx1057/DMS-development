<?php

namespace App\Traits\Employee;

use App\Services\{
    RoleService,
};
use Illuminate\Validation\Rule;

trait RoleTrait
{
    // Services
    protected RoleService $roleService;

    protected $role;
    public ?string $roleId = null;

    // Properties
    public ?string $name = null;

    public function mount($id = null){
        if($id)
        {
            $this->role = $this->roleService->find($id);
            $this->roleId = $id;
            $this->name = $this->role->name;
        }
    }

    public function boot(
        RoleService $roleService,
    ) {
        $this->roleService = $roleService;
    }


    public function validations()
    {
        if($this->roleId == null){
            $validations['name'] = ['required', Rule::unique('roles')];
        }else{
            $validations['name'] = ['required', Rule::unique('roles')->ignore($this->roleId)];
        }
        return $this->validate($validations);
    }

}
