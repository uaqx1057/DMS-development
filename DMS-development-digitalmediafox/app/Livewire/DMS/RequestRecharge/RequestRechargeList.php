<?php

namespace App\Livewire\DMS\RequestRecharge;

use App\Mail\AcceptRechargeMail;
use App\Mail\RejectRechargeMail;
use App\Models\Driver;
use App\Models\RequestRecharge;
use App\Models\User;
use App\Traits\DataTableTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\On;
use Livewire\Component;

class RequestRechargeList extends Component
{
    public $branch_id, $date_range;

    use DataTableTrait;

    private string $main_menu = 'Recharge';
    private string $menu = 'Request Recharge List';

    public $selectedId;
    public $status;
    public $start_date;
    public $end_date;
    public $reject_reason;
    public $showRejectModal = false;

    protected $rules = [
        'reject_reason' => 'required|string|min:3',
    ];

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

    public function updatedStatus($selectStatus)
    {
        $this->status = $selectStatus;
    }

    #[On('dateRangeSelected')]
    public function updatedDateRange($start, $end)
    {
        $this->start_date = $start;
        $this->end_date = $end;
    }
    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $add_permission = CheckPermission(config('const.ADD'), config('const.REQUESTRECHARGE'));
        $edit_permission = false;

        $columns = [
            ['label' => 'Driver', 'column' => 'driver_with_iqama', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Branch', 'column' => 'driver_branch', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Phone No.', 'column' => 'mobile', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Opearator', 'column' => 'opearator', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Requested By', 'column' => 'user', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Date and Time', 'column' => 'dateTime', 'isData' => true, 'hasRelation' => false],
        ];

        if (auth()->user()->role_id == 8) {
            $columns = array_merge($columns, [
                ['label' => 'Status', 'column' => 'status', 'isData' => false, 'hasRelation' => false],
            ]);
        }

        if (auth()->user()->role_id == 1 || auth()->user()->role_id == 11) {
            $columns = array_merge($columns, [

                ['label' => 'Action', 'column' => 'action', 'isData' => false, 'hasRelation' => false],
            ]);
        }

        $query = RequestRecharge::with(['driver', 'user']);

        // filter with Status 
        if($this->status){
            $query = $query->where('status', $this->status);
        }

        if($this->start_date && $this->end_date){
            $query = $query->whereBetween('created_at', [
                Carbon::parse($this->start_date)->startOfDay(),
                Carbon::parse($this->end_date)->endOfDay(),
            ]);
        }

         $query->when(auth()->user()->role_id == 8, function ($query) {
                $query->whereHas('driver', function ($q) {
                    $q->where('branch_id', auth()->user()->branch_id);
                });
            });
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
            $recharge->dateTime = $recharge->created_at->format('d M Y H:i:s' );
            return $recharge;
        });


        return view('livewire.dms.request-recharge.request-recharge-list', compact(
            'requestRecharges',
            'main_menu',
            'menu',
            'add_permission',
            'edit_permission',
            'columns'
        ));
    }

    public function accept($id)
    {
        $requestRecharge = RequestRecharge::find($id);
        if ($requestRecharge) {
            RequestRecharge::where('id', $id)->update(['status' => 'Accepted', 'approved_by' => auth()->user()->id]);
        }

        $driverData = Driver::with('branch')->where('id', $requestRecharge->driver_id)->first();
        $opManager = User::with('branch')->with('role')->where('id', auth()->user()->id)->first();

        $financeAdmin = User::whereIn('role_id', [1, 12])->where('status', 'Active')->pluck('email');

        foreach ($financeAdmin as $email) {
            Mail::to($email)->queue(new AcceptRechargeMail($driverData, $opManager, $requestRecharge));
        }

        session()->flash('success', __('Recharge request has been reviewed and Accepted.'));
    }

    public function reject($id)
    {
        $this->selectedId = $id;
        $this->reset('reject_reason');
        $this->showRejectModal = true; // open modal
    }

    public function submitReject()
    {
        $this->validate();
        $requestRecharge = RequestRecharge::find($this->selectedId);
        if ($requestRecharge) {
            RequestRecharge::where('id', $this->selectedId)->update(['status' => 'Rejected', 'reason' => $this->reject_reason, 'approved_by' => auth()->user()->id]);
        }

        $driverData = Driver::with('branch')->where('id', $requestRecharge->driver_id)->first();
        $opManager = User::with('branch')->with('role')->where('id', auth()->user()->id)->first();

        $opSuperviser = User::where('id', $requestRecharge->user_id)->first();
        $rejectReason = $this->reject_reason;
        logger($rejectReason);

        Mail::to($opSuperviser->email)->send(new RejectRechargeMail($driverData, $opManager, $rejectReason, $requestRecharge));

        $this->showRejectModal = false;
        $this->reject_reason = '';

        session()->flash('success', __('Recharge request has been reviewed and Rejected'));
    }

    public function exportPdf()
    {

        $columns = [
            ['label' => 'Driver', 'column'  => 'name'],
            ['label' => 'Branch.', 'column' => 'branch'],
            ['label' => 'Phone No.', 'column' => 'mobile'],
            ['label' => 'Opearator', 'column' => 'opearator'],
            ['label' => 'Status', 'column' => 'status'],
            ['label' => 'Requested By', 'column' => 'user'],
            ['label' => 'Date and Time', 'column' => 'opearator'],
        ];

        $query = RequestRecharge::with(['driver', 'user']);
        
        if($this->status){
            $query = $query->where('status', $this->status);
        }

        if($this->start_date && $this->end_date){
            $query = $query->whereBetween('created_at', [
                Carbon::parse($this->start_date)->startOfDay(),
                Carbon::parse($this->end_date)->endOfDay(),
            ]);
        }

        $query = $query->when(auth()->user()->role_id == 8, function ($query) {
                $query->whereHas('driver', function ($q) {
                    $q->where('branch_id', auth()->user()->branch_id);
                });
            });
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
        $requestRecharges = $query->orderBy('id', 'desc')->get()->transform(function ($recharge) {
            $recharge->driver_with_iqama = $recharge->driver->name . '(' . $recharge->driver->iqaama_number . ')';
            $recharge->driver_branch = $recharge->driver->branch->name ?? '';
            $recharge->dateTime = $recharge->created_at->format('d M Y H:i:s' );
            return $recharge;
            });

        // Generate PDF from a Blade view
        $pdf = Pdf::loadView('exports.reqeust-recharge', [
            'requestRecharges' => $requestRecharges,
            'columns' => $columns,
        ]);

        $filename = 'request-recharge-list-' . now()->format('Y-m-d') . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function exportCsv()
    {
        // Prepare CSV headers
        $headers = [
            'Driver',
            'Branch',
            'Phone No',
            'Operator',
            'Status',
            'Requested By',
            'Date and Time',
        ];

        $query = RequestRecharge::with(['driver', 'user']);

        if($this->status){
            $query = $query->where('status', $this->status);
        }

        if($this->start_date && $this->end_date){
            $query = $query->whereBetween('created_at', [
                Carbon::parse($this->start_date)->startOfDay(),
                Carbon::parse($this->end_date)->endOfDay(),
            ]);
        }

        // Branch Filter
        $query = $query->when(auth()->user()->role_id == 8, function ($query) {
            $query->whereHas('driver', function ($q) {
                $q->where('branch_id', auth()->user()->branch_id);
            });
        });

        // Search Filter
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
        $requestRecharges = $query->orderBy('id', 'desc')->get()->transform(function ($recharge) {
            $recharge->driver_with_iqama = $recharge->driver->name . '(' . $recharge->driver->iqaama_number . ')';
            $recharge->driver_branch = $recharge->driver->branch->name ?? '';
            $recharge->dateTime = $recharge->created_at->format('d M Y H:i:s');
            return $recharge;
        });

        $filename = 'request-recharge-list-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($requestRecharges, $headers) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write headers
            fputcsv($handle, $headers);
            
            // Write data rows
            foreach ($requestRecharges as $row) {
                fputcsv($handle, [
                    $row->driver_with_iqama,
                    $row->driver_branch,
                    $row->mobile,
                    $row->opearator,
                    ucfirst($row->status),
                    $row->user->name,
                    $row->dateTime,
                ]);
            }
            
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

}
