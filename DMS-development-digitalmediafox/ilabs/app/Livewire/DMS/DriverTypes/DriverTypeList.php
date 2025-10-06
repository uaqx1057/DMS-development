<?php

namespace App\Livewire\DMS\DriverTypes;

use App\Models\DriverType;
use App\Services\DriverTypeService;
use App\Traits\DataTableTrait;
use Livewire\Component;

class DriverTypeList extends Component
{
    use DataTableTrait;

    private string $main_menu  = 'Driver Types';
    private string $menu  = 'Driver Type List';

     /**
     * Initialize component with stored page or default values.
     *
     * @return void
     */

     public function mount()
     {
         if (session()->has('page')) {
             $this->page = session('page');
         }
     }

     /**
      * Initialize component with stored page or default values.
      *
      * @return void
      */

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
        $add_permission = CheckPermission(config('const.ADD'), config('const.DRIVERTYPES'));
        $edit_permission = CheckPermission(config('const.EDIT'), config('const.DRIVERTYPES'));

        $columns = [
            ['label' => 'Name', 'column' => 'name', 'isData' => true,'hasRelation'=> false],

            ['label' => 'Action', 'column' => 'action', 'isData' => false,'hasRelation'=> false],
        ];

        $driverTypes = DriverType::search($this->search)
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage, ['*'], 'page');

        return view('livewire.dms.driver-types.driver-type-list', compact('driverTypes', 'main_menu', 'menu', 'add_permission', 'edit_permission', 'columns'));
    }
}
