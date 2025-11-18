<?php

namespace App\Livewire\DMS\OperationSuperviser;

use App\Models\OperationSuperviserDifference;
use App\Traits\DataTableTrait;
use Livewire\Component;

class OperationSuperviserDifferenceLog extends Component
{
    use DataTableTrait;

    private string $main_menu = 'Difference';
    private string $menu = 'Supervisor Difference List';

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
            ['label' => 'Supervisor', 'column' => 'superviser', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Created By', 'column' => 'creator', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Total Difference', 'column' => 'total_receipt', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Total Paid', 'column' => 'total_paid', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Total Remaining', 'column' => 'total_remaining', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Receipt Image', 'column' => 'receipt_image', 'isData' => false, 'hasRelation' => false],
            ['label' => 'Created At', 'column' => 'created_at', 'isData' => true, 'hasRelation' => false],
        ];

        $superviserDifference = OperationSuperviserDifference::with(['superviser', 'creator'])
            ->when(auth()->user()->role_id != 1, function ($query) {
                $query->where('created_by', auth()->user()->id);
            })
            ->when($this->search, function ($query) use ($columns) {
                $search = $this->search;

                $query->where(function ($q) use ($columns, $search) {
                    foreach ($columns as $col) {
                        // Only search in columns marked as isData = true
                        if ($col['isData']) {
                            if ($col['hasRelation']) {
                                // Search in related table
                                $relation = $col['column'];
                                $relationColumn = $col['columnRelation'];
                                $q->orWhereHas($relation, function ($q2) use ($relationColumn, $search) {
                                    $q2->where($relationColumn, 'like', "%$search%");
                                });
                            } else {
                                // Search in main table
                                $q->orWhere($col['column'], 'like', "%$search%");
                            }
                        }
                    }
                });
            })
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.dms.operation-superviser.operation-superviser-difference-log', compact(
            'superviserDifference',
            'main_menu',
            'menu',
            'add_permission',
            'edit_permission',
            'columns'
        ));
    }
}
