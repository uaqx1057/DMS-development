<?php

namespace App\Repositories;

use App\Interfaces\RoleInterface;
use App\Models\Module;
use App\Models\Privilege;
use App\Models\Role;

class RoleRepository implements RoleInterface
{
    public function all()
    {
        return Role::all();
    }

    public function create(array $data)
    {
        $role = Role::create($data);
        $modules = Module::orderBy('index','asc')->get();
        foreach($modules as $module)
            {
                Privilege::create([
                    'role_id' => $role->id,
                    'module_id' => $module->id,
                    'is_view' => 0,
                    'is_add' => 0,
                    'is_edit' => 0,
                    'is_delete' => 0,
                ]);
            }
        return $role;
    }

    public function update(array $data, $id)
    {
        $user = Role::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $user = Role::findOrFail($id);
        $user->delete();
    }

    public function find($id)
    {
        return Role::findOrFail($id);
    }
}
