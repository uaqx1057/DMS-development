<?php
namespace App\Livewire\Employee;

use App\Models\User;
use App\Services\EmployeeService;
use App\Traits\DataTableTrait;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Employee List')]
class EmployeeList extends Component
{
    use DataTableTrait;

    private string $main_menu  = 'Employees';
    private string $menu  = 'Employee List';


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
        $add_permission = CheckPermission(config('const.ADD'), config('const.EMPLOYEES'));
        $edit_permission = CheckPermission(config('const.EDIT'), config('const.EMPLOYEES'));

        $columns = [
            ['label' => 'Employee ID', 'column' => 'user_id', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Name', 'column' => 'name', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Email', 'column' => 'email', 'isData' => true,'hasRelation'=> false],
            ['label' => 'UserRole', 'column' => 'role', 'isData' => true,'hasRelation'=> true, 'columnRelation' => 'name'],
            ['label' => 'Status', 'column' => 'status', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Action', 'column' => 'action', 'isData' => false,'hasRelation'=> false],
        ];

        $employees = User::with('role')->search($this->search)
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage, ['*'], 'page');

        return view('livewire.employee.employee-list', compact('employees', 'main_menu', 'menu', 'add_permission', 'edit_permission', 'columns'));
    }


}
