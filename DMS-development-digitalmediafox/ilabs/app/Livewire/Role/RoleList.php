<?php

namespace App\Livewire\Role;

use App\Models\Role;
use App\Services\RoleService;
use App\Traits\DataTableTrait;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

#[Title('Role List')]
class RoleList extends Component
{
    use WithFileUploads, WithPagination, DataTableTrait;

    protected RoleService $roleService;
    private string $main_menu  = 'Role';
    private string $menu  = 'Role List';

    public function mount()
    {
        $perPage = $this->perPage;

        if (session()->has('page')) {
            $this->page = session('page');
        }
    }

     public function boot()
     {
         if (session()->has('page')) {
             $this->page = session('page');
         }
     }


    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;

        $add_permission = CheckPermission(config('const.ADD'), config('const.ROLE'));
        $edit_permission = CheckPermission(config('const.EDIT'), config('const.ROLE'));

        $columns = [
            ['label' => 'Name', 'column' => 'name', 'isData' => true,'hasRelation'=> false],

            ['label' => 'Action', 'column' => 'action', 'isData' => false,'hasRelation'=> false],
        ];

        $customers = Role::search($this->search)
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage, ['*'], 'page');


        return view('livewire.role.role-list', compact('main_menu', 'menu', 'columns', 'customers', 'add_permission', 'edit_permission'));
    }

}

