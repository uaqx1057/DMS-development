<?php

namespace App\Livewire\DMS\DriverDifference;

use App\Models\DriverDifference;
use App\Traits\DataTableTrait;
use Livewire\Component;

class DriverDifferenceLog extends Component
{

    use DataTableTrait;

    private string $main_menu = 'Difference';
    private string $menu = 'Driver Difference List';

    public function mount()
    {
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
        $add_permission = false;
        $edit_permission = false;

        $columns = [
            ['label' => 'Driver Name', 'column' => 'driver', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Iqaama Number', 'column' => 'driver', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'iqaama_number'],
            ['label' => 'Created By', 'column' => 'user', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Receipt Date', 'column' => 'receipt_date', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Total Difference', 'column' => 'total_receipt', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Total Paid', 'column' => 'total_paid', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Total Remaining', 'column' => 'total_remaining', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Receipt Image', 'column' => 'receipt_image', 'isData' => false, 'hasRelation' => false],
            ['label' => 'Created At', 'column' => 'created_at', 'isData' => true, 'hasRelation' => false],
        ];

        $driverDifference = DriverDifference::with(['driver', 'user'])
            ->when(auth()->user()->role_id != 1, function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->when($this->search, function ($query) use ($columns) {
                $search = $this->search;

                $query->where(function ($q) use ($columns, $search) {
                    foreach ($columns as $col) {
                        if ($col['hasRelation']) {
                            // Search in related model
                            $relation = $col['column'];
                            $relationColumn = $col['columnRelation'];
                            $q->orWhereHas($relation, function ($q2) use ($relationColumn, $search) {
                                $q2->where($relationColumn, 'like', "%$search%");
                            });
                        } else {
                            // Search in current table
                            $q->orWhere($col['column'], 'like', "%$search%");
                        }
                    }
                });
            })
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);


        return view('livewire.dms.driver-difference.driver-difference-log', compact(
            'driverDifference',
            'main_menu',
            'menu',
            'add_permission',
            'edit_permission',
            'columns'
        ));
    }
}
