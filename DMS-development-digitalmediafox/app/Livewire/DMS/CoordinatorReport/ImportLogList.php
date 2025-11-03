<?php

namespace App\Livewire\DMS\CoordinatorReport;

use App\Models\ImportLog;
use App\Traits\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class ImportLogList extends Component
{
    use WithPagination, DataTableTrait;

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
        $main_menu = 'Logs';
        $menu = 'Import Logs';
        $add_permission = false; // no add button for logs
        $edit_permission = false;

        $columns = [
            ['label' => 'User', 'column' => 'user', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'File Name', 'column' => 'original_name', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Report Date', 'column' => 'report_date', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Rows Imported', 'column' => 'rows_imported', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Imported At', 'column' => 'created_at', 'isData' => true, 'hasRelation' => false],
        ];

        $query = ImportLog::with('user');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('original_name', 'like', "%{$this->search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            });
        }

        $logs = $query
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage, ['*'], 'page');

        return view('livewire.dms.coordinator-report.import-log-list', compact(
            'logs',
            'columns',
            'main_menu',
            'menu',
            'add_permission',
            'edit_permission'
        ));
    }
}
