<?php

namespace App\Traits;

use App\Models\{Module, Privilege, Role};
use Illuminate\Support\Facades\DB;

trait RoleTrait{
    public function storeRoles(){
        $roles = $this->getRoles();
        foreach($roles as $item){
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Role::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $role = Role::create($item);
            $modules = Module::orderBy('index','asc')->get();
            foreach($modules as $module)
            {
                Privilege::create([
                    'role_id' => $role->id,
                    'module_id' => $module->id,
                    'is_view' => $module->is_view,
                    'is_add' => $module->is_add,
                    'is_edit' => $module->is_edit,
                    'is_delete' => $module->is_delete,
                ]);
            }
        }
    }

    public function getRoles(){
        return [
            [
                'name' => 'Administrator',
                'is_default' => 1
            ],
        ];
    }
}
