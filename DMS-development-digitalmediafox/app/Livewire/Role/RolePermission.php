<?php

namespace App\Livewire\Role;

use App\Models\Privilege;
use App\Models\Role;
use App\Models\Module;
use Livewire\Component;

class RolePermission extends Component
{
    public $roleId;
    public $role;
    public $modules = [];

    public $viewPermissions = [];
    public $addPermissions = [];
    public $editPermissions = [];
    public $deletePermissions = [];

    public $selectAllView = false;
    public $selectAllAdd = false;
    public $selectAllEdit = false;
    public $selectAllDelete = false;

    public function mount($id)
    {
        $this->roleId = $id;
        $this->role = Role::find($id);
        $this->loadPermissions();
    }

    public function getModules()
    {
        return Module::with(['operations' => function ($query) {
                $query->orderBy('index', 'asc');
            }])
            ->whereNull('parent_id')
            ->orderBy('index', 'asc')
            ->get();
    }

    private function loadPermissions()
    {
        $this->modules = $this->getModules();

        $this->viewPermissions = [];
        $this->addPermissions = [];
        $this->editPermissions = [];
        $this->deletePermissions = [];

        foreach ($this->modules as $parent) {
            foreach ($parent->operations as $subModule) {
                $privilege = Privilege::where('module_id', $subModule->id)
                    ->where('role_id', $this->roleId)
                    ->first();

                $this->viewPermissions[$subModule->id] = $privilege ? (bool) $privilege->is_view : false;
                $this->addPermissions[$subModule->id] = $privilege ? (bool) $privilege->is_add : false;
                $this->editPermissions[$subModule->id] = $privilege ? (bool) $privilege->is_edit : false;
                $this->deletePermissions[$subModule->id] = $privilege ? (bool) $privilege->is_delete : false;
            }
        }



    }

    public function updatedSelectAllView($value)
    {
        foreach ($this->viewPermissions as $moduleId => $v) {
            $this->viewPermissions[$moduleId] = $value;
        }
    }

    public function updatedSelectAllAdd($value)
    {
        foreach ($this->addPermissions as $moduleId => $v) {
            $this->addPermissions[$moduleId] = $value;
        }
    }

    public function updatedSelectAllEdit($value)
    {
        foreach ($this->editPermissions as $moduleId => $v) {
            $this->editPermissions[$moduleId] = $value;
        }
    }

    public function updatedSelectAllDelete($value)
    {
        foreach ($this->deletePermissions as $moduleId => $v) {
            $this->deletePermissions[$moduleId] = $value;
        }
    }

    public function updatePermissions()
    {
        foreach ($this->viewPermissions as $moduleId => $view) {
            Privilege::updateOrCreate(
                ['module_id' => $moduleId, 'role_id' => $this->roleId],
                [
                    'is_view' => $this->viewPermissions[$moduleId] ? 1 : 0,
                    'is_add' => $this->addPermissions[$moduleId] ? 1 : 0,
                    'is_edit' => $this->editPermissions[$moduleId] ? 1 : 0,
                    'is_delete' => $this->deletePermissions[$moduleId] ? 1 : 0,
                ]
            );
        }

        session()->flash('success', translate('Role Permission Updated Successfully!'));
        return $this->redirectRoute('role.index', navigate:true);
    }

    public function render()
    {
        $main_menu = 'Roles';
        $menu = 'Edit Role';
        return view('livewire.role.role-permission', [
            'role' => $this->role,
            'modules' => $this->modules,
            'main_menu' => $main_menu,
            'menu' => $menu,
        ]);
    }
}
