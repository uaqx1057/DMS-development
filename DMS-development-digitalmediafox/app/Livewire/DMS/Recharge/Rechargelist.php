<?php

namespace App\Livewire\DMS\Recharge;

use App\Models\RequestRecharge;
use App\Traits\DataTableTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class Rechargelist extends Component
{
    use DataTableTrait;
    private string $main_menu = 'Recharge';
    private string $menu = 'Recharge List';

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
            ['label' => 'Report', 'column' => 'report', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Amount', 'column' => 'recharge', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'amount'],
            ['label' => 'View', 'column' => 'recharge', 'isData' => false, 'hasRelation' => true, 'columnRelation' => 'image'],
            ['label' => 'Action', 'column' => 'driverRecharge', 'isData' => false, 'hasRelation' => false],
        ];


        $query = RequestRecharge::with(['recharge', 'user', 'driver', 'approved'])->where('status', 'accepted');

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
            // Driver 
            $recharge->driver_with_iqama = $recharge->driver->name . '(' . $recharge->driver->iqaama_number . ')';
            $recharge->driver_branch = $recharge->driver->branch->name ?? '';

            // Status 
            $status = $recharge->status == 'accepted' ? 'Approved By' : 'Rejected By';
            // Requested By 
            $recharge->requested_by = isset($recharge->user->name) ? 'Requested By:' . $recharge->user->name . ' at '. $recharge->created_at->format('d M Y H:i:s' ) . '<br>' : '';
            // Approved/ Rejected By 
            $recharge->approved_by = isset($recharge->approved->name) ? '' . $status . ':' . $recharge->approved->name . ' at '. $recharge->updated_at->format('d M Y H:i:s' )  . '<br>' : '';
            // Recharged By
            $recharge->recharged_by = isset($recharge->recharge) ? 'Recharged By:' .$recharge->recharge->user->name . ' at '. $recharge->recharge->created_at->format('d M Y H:i:s' ) . '<br>' : '';
            // Reason
            $recharge->reason = isset($recharge->reason) ? 'Reject Reason:' .$recharge->reason : '';

            // Report 
            $recharge->report = $recharge->requested_by . $recharge->approved_by . $recharge->recharged_by . $recharge->reason;
            return $recharge;
        });

        return view('livewire.dms.recharge.rechargelist', compact(
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
            ['label' => 'Report', 'column' => 'report'],
            ['label' => 'Amount', 'column' => 'recharge'],
            // ['label' => 'View', 'column' => 'recharge'],
            ['label' => 'Status', 'column' => 'driverRecharge'],
        ];

        $query = RequestRecharge::with(['recharge', 'user', 'driver', 'approved'])->where('status', 'accepted');

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
            // Driver 
            $recharge->driver_with_iqama = $recharge->driver->name . '(' . $recharge->driver->iqaama_number . ')';
            $recharge->driver_branch = $recharge->driver->branch->name ?? '';

            // Status 
            $status = $recharge->status == 'accepted' ? 'Approved By' : 'Rejected By';
            // Requested By 
            $recharge->requested_by = isset($recharge->user->name) ? 'Requested By:' . $recharge->user->name . ' at '. $recharge->created_at->format('d M Y H:i:s' ) . '<br>' : '';
            // Approved/ Rejected By 
            $recharge->approved_by = isset($recharge->approved->name) ? '' . $status . ':' . $recharge->approved->name . ' at '. $recharge->updated_at->format('d M Y H:i:s' )  . '<br>' : '';
            // Recharged By
            $recharge->recharged_by = isset($recharge->recharge) ? 'Recharged By:' .$recharge->recharge->user->name . ' at '. $recharge->recharge->created_at->format('d M Y H:i:s' ) . '<br>' : '';
            // Reason
            $recharge->reason = isset($recharge->reason) ? 'Reject Reason:' .$recharge->reason : '';

            // Report 
            $recharge->report = $recharge->requested_by . $recharge->approved_by . $recharge->recharged_by . $recharge->reason;
            return $recharge;
        });

        // Generate PDF from a Blade view
        $pdf = Pdf::loadView('exports.recharge', [
            'requestRecharges' => $requestRecharges,
            'columns' => $columns,
        ]);

        $filename = 'recharge-list-' . now()->format('Y-m-d') . '.pdf';

        return response()->streamDownload(
            fn() => print ($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function exportCsv()
{
    $fileName = 'recharge-list-' . now()->format('Y-m-d') . '.csv';
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, post-check=0",
        "Expires"             => "0"
    ];
    
    // CSV column headers
    $columns = [
        'Driver',
        'Branch',
        'Phone No.',
        'Opearator',
        'Report',
        'Amount',
        'Status'
    ];
    
    $callback = function () use ($columns) {
        $file = fopen('php://output', 'w');
        
        // Write column header row
        fputcsv($file, $columns);
        
        // Query
        $query = RequestRecharge::with(['recharge', 'user', 'driver', 'approved'])->where('status', 'accepted');
        
        // Apply Search
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
        
        $requestRecharges = $query->orderBy('id', 'desc')->get();
        
        // Transform data
        $requestRecharges->transform(function ($recharge) {
            // Driver
            $recharge->driver_with_iqama = $recharge->driver->name . '(' . $recharge->driver->iqaama_number . ')';
            $recharge->driver_branch = $recharge->driver->branch->name ?? '';
            
            // Status
            $status = $recharge->status == 'accepted' ? 'Approved By' : 'Rejected By';
            
            // Requested
            $recharge->requested_by = isset($recharge->user->name)
                ? "Requested By: {$recharge->user->name} at " . $recharge->created_at->format('d M Y H:i:s')
                : '';
            
            // Approved/Rejected
            $recharge->approved_by = isset($recharge->approved->name)
                ? "{$status}: {$recharge->approved->name} at " . $recharge->updated_at->format('d M Y H:i:s')
                : '';
            
            // Recharged
            $recharge->recharged_by = isset($recharge->recharge)
                ? "Recharged By: {$recharge->recharge->user->name} at " . $recharge->recharge->created_at->format('d M Y H:i:s')
                : '';
            
            // Reject Reason
            $recharge->reason = $recharge->reason
                ? "Reject Reason: {$recharge->reason}"
                : '';
            
            // Combine report (WITH line breaks for CSV)
            $report = $recharge->requested_by . "\n" .
                      $recharge->approved_by . "\n" .
                      $recharge->recharged_by . "\n" .
                      $recharge->reason;
            
            // Clean up while preserving line breaks
            $lines = explode("\n", $report);
            $cleanLines = array_map(function($line) {
                return preg_replace('/\s+/', ' ', trim($line));
            }, $lines);
            $recharge->report = implode("\n", array_filter($cleanLines));
            
            return $recharge;
        });
        
        // Write CSV rows
        foreach ($requestRecharges as $row) {
            fputcsv($file, [
                $row->driver_with_iqama,
                $row->driver_branch,
                $row->mobile,
                $row->opearator,
                $row->report,
                $row->recharge->amount ?? '-',
                $row->recharge ? 'Recharged' : 'Pending',
            ]);
        }
        
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
}


}
