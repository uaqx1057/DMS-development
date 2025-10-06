<?php

namespace App\Livewire\DMS\Drivers;

use App\Models\Driver;
use App\Traits\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class DriverList extends Component
{
    use WithPagination, DataTableTrait;

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

        $main_menu = 'Drivers';
        $menu = 'Driver List';
        $add_permission = CheckPermission(config('const.ADD'), config('const.DRIVERS'));
        $edit_permission = CheckPermission(config('const.EDIT'), config('const.DRIVERS'));

        $columns = [
            ['label' => 'Driver ID', 'column' => 'driver_id', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Name', 'column' => 'name', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Iqaama Number', 'column' => 'iqaama_number', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Driver Type', 'column' => 'driver_type', 'isData' => true,'hasRelation'=> true, 'columnRelation' => 'name'],
            ['label' => 'Branch', 'column' => 'branch', 'isData' => true,'hasRelation'=> true, 'columnRelation' => 'name'],
            ['label' => 'Action', 'column' => 'action', 'isData' => false,'hasRelation'=> false],
        ];

        $query = Driver::with(['driver_type', 'branch']);
        
        if (auth()->user()->role_id!=1) {
        $query->where('branch_id', auth()->user()->branch_id);
        }
        
        $drivers = $query
        ->search($this->search)
        ->orderBy($this->sortColumn, $this->sortDirection)
        ->paginate($this->perPage, ['*'], 'page');


        return view('livewire.dms.drivers.driver-list', compact('drivers', 'columns', 'main_menu', 'menu', 'add_permission', 'edit_permission'));
    }
}
