<?php

namespace App\Livewire\DMS\RequestRecharge;

use App\Mail\AcceptRechargeMail;
use App\Mail\RejectRechargeMail;
use App\Models\Driver;
use App\Models\RequestRecharge;
use App\Models\User;
use App\Traits\DataTableTrait;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class RequestRechargeList extends Component
{
    use DataTableTrait;

    private string $main_menu = 'Recharge';
    private string $menu = 'Request Recharge List';

    public $selectedId;
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
    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $add_permission = CheckPermission(config('const.ADD'), config('const.REQUESTRECHARGE'));
        $edit_permission = false;

        $columns = [
            ['label' => 'Driver Name', 'column' => 'driver', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Phone No.', 'column' => 'mobile', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Opearator', 'column' => 'opearator', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Created By', 'column' => 'user', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
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
            RequestRecharge::where('id', $id)->update(['status' => 'accept', 'approved_by' => auth()->user()->id]);
        }

        $driverData = Driver::with('branch')->where('id', $requestRecharge->driver_id)->first();
        $opManager = User::with('branch')->with('role')->where('id', auth()->user()->id)->first();

        $financeAdmin = User::whereIn('role_id', [1, 12])->where('status', 'Active')->pluck('email');

        foreach ($financeAdmin as $email) {
            Mail::to($email)->queue(new AcceptRechargeMail($driverData, $opManager, $requestRecharge));
        }

        session()->flash('success', __('Recharge request has been reviewed and Accept.'));
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
            RequestRecharge::where('id', $this->selectedId)->update(['status' => 'reject', 'approved_by' => auth()->user()->id]);
        }

        $driverData = Driver::with('branch')->where('id', $requestRecharge->driver_id)->first();
        $opManager = User::with('branch')->with('role')->where('id', auth()->user()->id)->first();

        $opSuperviser = User::where('id', $requestRecharge->user_id)->first();
        $rejectReason = $this->reject_reason;
        logger($rejectReason);

        Mail::to($opSuperviser->email)->send(new RejectRechargeMail($driverData, $opManager, $rejectReason, $requestRecharge));

        $this->showRejectModal = false;
        $this->reject_reason = '';

        session()->flash('success', __('Recharge request has been reviewed and has been rejected'));
    }
}
