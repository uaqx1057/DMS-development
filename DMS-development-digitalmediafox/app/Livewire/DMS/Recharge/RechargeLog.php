<?php

namespace App\Livewire\DMS\Recharge;

use App\Models\RequestRecharge;
use App\Traits\DataTableTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class RechargeLog extends Component
{
    use DataTableTrait;
    private string $main_menu = 'Log';
    private string $menu = 'Recharge Log';

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
            ['label' => 'Driver', 'column' => 'driver_with_iqama', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Branch', 'column' => 'driver_branch', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Phone No.', 'column' => 'mobile', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Opearator', 'column' => 'opearator', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Status', 'column' => 'status', 'isData' => false, 'hasRelation' => false],
            ['label' => 'Requested By', 'column' => 'user', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Approved By', 'column' => 'approved', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Date', 'column' => 'recharge', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'date'],
            ['label' => 'Amount', 'column' => 'recharge', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'amount'],
            ['label' => 'View', 'column' => 'recharge', 'isData' => false, 'hasRelation' => true, 'columnRelation' => 'image'],
            ['label' => 'Action', 'column' => 'driverRecharge', 'isData' => false, 'hasRelation' => false],
        ];


        $query = RequestRecharge::with(['recharge', 'user', 'driver', 'approved']);

        // Apply search if not empty
        if ($this->search) {
            $search = $this->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('driver', function ($d) use ($search) {
                    $d->where('name', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('mobile', 'LIKE', "%{$search}%")
                    ->orWhere('opearator', 'LIKE', "%{$search}%");
            });
        }

        // Final result
        $requestRecharges = $query
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);

        $requestRecharges->getCollection()->transform(function ($recharge) {
            $recharge->driver_with_iqama = $recharge->driver->name . '(' . $recharge->driver->iqaama_number . ')';
            $recharge->driver_branch = $recharge->driver->branch->name ?? '';
            return $recharge;
        });
        return view('livewire.dms.recharge.recharge-log', compact(
            'requestRecharges',
            'main_menu',
            'menu',
            'add_permission',
            'edit_permission',
            'columns'
        ));
    }

    public function exportPdf()
    {

        $columns = [
            ['label' => 'Driver', 'column' => 'driver_with_iqama'],
            ['label' => 'Branch', 'column' => 'driver_branch'],
            ['label' => 'Phone No.', 'column' => 'mobile'],
            ['label' => 'Opearator', 'column' => 'opearator'],
            ['label' => 'Status', 'column' => 'status'],
            ['label' => 'Requested By', 'column' => 'user'],
            ['label' => 'Approved By', 'column' => 'approved'],
            ['label' => 'Date', 'column' => 'recharge'],
            ['label' => 'Amount', 'column' => 'recharge'],
            // ['label' => 'View', 'column' => 'recharge'],
            ['label' => 'Process', 'column' => 'driverRecharge'],
        ];

        $query = RequestRecharge::with(['recharge', 'user', 'driver', 'approved']);

        // Apply search if not empty
        if ($this->search) {
            $search = $this->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('driver', function ($d) use ($search) {
                    $d->where('name', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('mobile', 'LIKE', "%{$search}%")
                    ->orWhere('opearator', 'LIKE', "%{$search}%");
            });
        }

        // Final result
        $requestRecharges = $query->get();

        $requestRecharges->transform(function ($recharge) {
            $recharge->driver_with_iqama = $recharge->driver->name . '(' . $recharge->driver->iqaama_number . ')';
            $recharge->driver_branch = $recharge->driver->branch->name ?? '';
            return $recharge;
        });

        // Generate PDF from a Blade view
        $pdf = Pdf::loadView('exports.recharge-log', [
            'requestRecharges' => $requestRecharges,
            'columns' => $columns,
        ]);

        $filename = 'recharge-log-' . now()->format('Y-m-d') . '.pdf';

        return response()->streamDownload(
            fn() => print ($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function exportCsv()
    {
        // CSV Headers
        $headers = [
            'Driver',
            'Branch',
            'Phone No.',
            'Opearator',
            'Status',
            'Requested By',
            'Approved By',
            'Date',
            'Amount',
            'Process',
        ];

        $query = RequestRecharge::with(['driver', 'user', 'recharge', 'approved']);

        // Branch filter
        // $query = $query->when(auth()->user()->role_id != 1, function ($query) {
        //     $query->whereHas('driver', function ($q) {
        //         $q->where('branch_id', auth()->user()->branch_id);
        //     });
        // });

        // Search filter
        if ($this->search) {
            $search = $this->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('driver', function ($d) use ($search) {
                    $d->where('name', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('mobile', 'LIKE', "%{$search}%")
                    ->orWhere('opearator', 'LIKE', "%{$search}%");
            });
        }

        // Final result transformation
        $requestRecharges = $query->get()->transform(function ($recharge) {
            $recharge->driver_with_iqama = $recharge->driver->name . '(' . $recharge->driver->iqaama_number . ')';
            $recharge->driver_branch = $recharge->driver->branch->name ?? '';
            return $recharge;
        });

        // Start CSV content
        $csvData = implode(',', $headers) . "\n";

        foreach ($requestRecharges as $row) {

            $formattedDate = optional($row->recharge)->date
                ? \Carbon\Carbon::parse($row->recharge->date)->format('d-m-Y')
                : '-';

            $csvData .= implode(',', [
                '"' . $row->driver_with_iqama . '"',
                '"' . $row->driver_branch . '"',
                '"' . ($row->mobile ?? '-') . '"',
                '"' . ($row->opearator ?? '-') . '"',
                '"' . ($row->status ?? '-') . '"',

                // user name
                '"' . ($row->user->name ?? '-') . '"',

                // approved by name
                '"' . ($row->approved->name ?? '-') . '"',

                // recharge date
                '"' . $formattedDate . '"',

                // recharge amount
                '"' . (optional($row->recharge)->amount ?? '-') . '"',

                // final status
                '"' . ($row->recharge ? 'Recharged' : (str_contains($row->status, 'reject') ? 'Rejected' : 'Pending')) . '"',
            ]) . "\n";
        }


        $filename = 'recharge-list-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(
            fn() => print ($csvData),
            $filename,
            ['Content-Type' => 'text/csv']
        );
    }
}
