<?php

namespace App\Livewire\DMS\Drivers;

use App\Models\Driver;
use App\Traits\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;

class DriverList extends Component
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
        $main_menu = 'Drivers';
        $menu = 'Driver List';
        $add_permission = CheckPermission(config('const.ADD'), config('const.DRIVERS'));
        $edit_permission = CheckPermission(config('const.EDIT'), config('const.DRIVERS'));

        $columns = [
            ['label' => 'Driver ID', 'column' => 'driver_id', 'isData' => true, 'hasRelation'=> false],
            ['label' => 'Name', 'column' => 'name', 'isData' => true, 'hasRelation'=> false],
            ['label' => 'Iqaama Number', 'column' => 'iqaama_number', 'isData' => true, 'hasRelation'=> false],
            ['label' => 'Iqaama Expiry', 'column' => 'iqaama_expiry', 'isData' => true, 'hasRelation'=> false],
            ['label' => 'Driver Type', 'column' => 'driver_type', 'isData' => true, 'hasRelation'=> true, 'columnRelation' => 'name'],
            ['label' => 'Branch', 'column' => 'branch', 'isData' => true, 'hasRelation'=> true, 'columnRelation' => 'name'],
            ['label' => 'Platform Name(IDs)', 'column' => 'platform_ids', 'isData' => true, 'hasRelation'=> false],
            ['label' => 'Action', 'column' => 'action', 'isData' => false, 'hasRelation'=> false],
        ];

        $query = Driver::with(['driver_type', 'branch', 'businesses']);

        if (auth()->user()->role_id != 1) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        $drivers = $query
            ->search($this->search)
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage, ['*'], 'page');

        // Prepare Platform Name + IDs column
        foreach ($drivers as $driver) {
            $platforms = [];
            foreach ($driver->businesses as $business) {
                $assignedIds = $driver->businessIds()
                    ->where('business_id', $business->id)
                    ->wherePivot('transferred_at', null)
                    ->get()
                    ->pluck('value')
                    ->toArray();

                if ($assignedIds) {
                    $platforms[] = $business->name . ' (' . implode(', ', $assignedIds) . ')';
                }
            }
            $driver->platform_ids = $platforms ? implode(' | ', $platforms) : '-';
            $driver->iqaama_expiry = $driver->iqaama_expiry ? date('d-m-Y', strtotime($driver->iqaama_expiry)) : '-';
        }

        return view('livewire.dms.drivers.driver-list', compact(
            'drivers',
            'columns',
            'main_menu',
            'menu',
            'add_permission',
            'edit_permission'
        ));
    }

    /** Export Driver List as CSV */
    public function exportCsv()
    {
        $drivers = Driver::with(['driver_type', 'branch', 'businesses'])->orderby('id','desc')->get();

        $headers = [
            'Driver ID',
            'Name',
            'Iqaama Number',
            'Iqaama Expiry',
            'Driver Type',
            'Branch',
            'Platform Name(IDs)',
        ];

        $csvContent = fopen('php://temp', 'r+');
        fputcsv($csvContent, $headers);

        foreach ($drivers as $driver) {
            $platforms = [];
            foreach ($driver->businesses as $business) {
                $assignedIds = $driver->businessIds()
                    ->where('business_id', $business->id)
                    ->wherePivot('transferred_at', null)
                    ->get()
                    ->pluck('value')
                    ->toArray();

                if ($assignedIds) {
                    $platforms[] = $business->name . ' (' . implode(', ', $assignedIds) . ')';
                }
            }

            fputcsv($csvContent, [
                $driver->driver_id,
                $driver->name,
                $driver->iqaama_number,
                $driver->iqaama_expiry ? date('d-m-Y', strtotime($driver->iqaama_expiry)) : '-',
                $driver->driver_type?->name ?? '-',
                $driver->branch?->name ?? '-',
                $platforms ? implode(' | ', $platforms) : '-',
            ]);
        }

        rewind($csvContent);
        $csvData = stream_get_contents($csvContent);
        fclose($csvContent);

        $filename = 'driver-list-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(
            fn() => print($csvData),
            $filename,
            ['Content-Type' => 'text/csv']
        );
    }

    /** Export Driver List as PDF */
    public function exportPdf()
    {
        $drivers = Driver::with(['driver_type', 'branch', 'businesses'])->orderby('id','desc')->get();

        // Format Iqaama Expiry & Platforms
        foreach ($drivers as $driver) {
            $driver->iqaama_expiry = $driver->iqaama_expiry ? date('d-m-Y', strtotime($driver->iqaama_expiry)) : '-';
            $platforms = [];
            foreach ($driver->businesses as $business) {
                $assignedIds = $driver->businessIds()
                    ->where('business_id', $business->id)
                    ->wherePivot('transferred_at', null)
                    ->get()
                    ->pluck('value')
                    ->toArray();

                if ($assignedIds) {
                    $platforms[] = $business->name . ' (' . implode(', ', $assignedIds) . ')';
                }
            }
            $driver->platform_ids = $platforms ? implode(' | ', $platforms) : '-';
        }

        $pdf = Pdf::loadView('exports.driver-list', compact('drivers'));

        $filename = 'driver-list-' . now()->format('Y-m-d') . '.pdf';

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename
        );
    }
}
