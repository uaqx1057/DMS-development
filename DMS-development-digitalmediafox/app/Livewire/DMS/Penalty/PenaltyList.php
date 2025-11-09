<?php

namespace App\Livewire\DMS\Penalty;

use App\Models\Penalty;
use App\Traits\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class PenaltyList extends Component
{
    use WithPagination, DataTableTrait;
    public string $main_menu = 'Penalty';
    public string $menu = 'Penalty List';

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
        $add_permission = false; // no add button for logs
        $edit_permission = false;

        $columns = [
            ['label' => 'Penalty Date', 'column' => 'penalty_date', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Driver Name', 'column' => 'driver', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Platform', 'column' => 'business', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Platform Id', 'column' => 'businessValue','isData' => true, 'hasRelation' => true, 'columnRelation' => 'value'],
            ['label' => 'Penalty Value', 'column' => 'penalty_value', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Proof', 'column' => 'penalty_file', 'isData' => false, 'hasRelation' => false],
        ];

        $query = Penalty::with('driver')->with('business');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('driver_id', 'like', "%{$this->search}%")
                    ->orWhereHas('coordinator_report_id', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            });
        }

        $penalties = $query
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage, ['*'], 'page');

        // dd($penalties);exit;

        return view('livewire.dms.penalty.penalty-list',compact(
            'columns',
            'add_permission',
            'edit_permission',
            'penalties'
        ));
    }
}
