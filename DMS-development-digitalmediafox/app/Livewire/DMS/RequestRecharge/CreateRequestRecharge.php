<?php

namespace App\Livewire\DMS\RequestRecharge;

use App\Mail\RequestRechargeMail;
use App\Models\Driver;
use App\Models\RequestRecharge;
use App\Models\User;
use App\Traits\DataTableTrait;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class CreateRequestRecharge extends Component
{
    use DataTableTrait;
    public string $main_menu = 'Recharge';
    public string $menu = 'Request Recharge';

    public $driver;
    public $phone_number;
    public $operator;
    public $selectedDriver;
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

    public function updatedDriver($id)
    {
        $reqRecharge = RequestRecharge::where('driver_id', $id)->get();
        if(count($reqRecharge) > 0){
            $this->selectedDriver = $id;
        } else{
            $this->selectedDriver = '';
        }
    }
    public function render()
    {
        $drivers = Driver::when(auth()->user()->role_id != 1, function ($query) {
                $query->where('branch_id', auth()->user()->branch_id);
            })
            ->get();
        // $drivers = Driver::orderBy('name', 'asc')->get();

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
            ['label' => 'Report', 'column' => 'report', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Amount', 'column' => 'recharge', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'amount'],
            ['label' => 'View', 'column' => 'recharge', 'isData' => false, 'hasRelation' => true, 'columnRelation' => 'image'],
        ];


        $query = RequestRecharge::with(['recharge', 'user', 'driver', 'approved']);

        if($this->selectedDriver){
            $query = $query->where('driver_id', $this->selectedDriver);
        }
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

        if(!$this->selectedDriver){
            $query = $query->limit(1);
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
            $recharge->requested_by = isset($recharge->user->name) ? 'Requested By: ' . $recharge->user->name . ' at '. $recharge->created_at->format('d M Y H:i:s' ) . '<br>' : '';
            // Approved/ Rejected By 
            $recharge->approved_by = isset($recharge->approved->name) ? '' . $status . ': ' . $recharge->approved->name . ' at '. $recharge->updated_at->format('d M Y H:i:s' )  . '<br>' : '';
            // Recharged By
            $recharge->recharged_by = isset($recharge->recharge) ? 'Recharged By: ' .$recharge->recharge->user->name . ' at '. $recharge->recharge->created_at->format('d M Y H:i:s' ) . '<br>' : '';
            // Reason
            $recharge->reason = isset($recharge->reason) ? 'Reject Reason: ' .$recharge->reason : '';

            // Report 
            $recharge->report = $recharge->requested_by . $recharge->approved_by . $recharge->recharged_by . $recharge->reason;
            return $recharge;
        });



        return view('livewire.dms.request-recharge.create-request-recharge', compact(
            'main_menu',
            'menu',
            'drivers',
            'add_permission',
            'requestRecharges',
            'edit_permission',
            'columns'
        ));
    }

    public function save()
    {
        // Validate required fields
        $this->validate([
            'driver' => 'required',
            'phone_number' => 'required',
            'operator' => 'required',
        ]);

        //Save
        $requestRecharge=  RequestRecharge::create([
            'driver_id' => $this->driver,
            'user_id' => auth()->user()->id,
            'mobile' => $this->phone_number,
            'opearator' => $this->operator,
            'status' => 'pending',
        ]);

        $driverData = Driver::with('branch')->where('id', $this->driver)->first();
        $opSupervisr = User::with('branch')->with('role')->where('id', auth()->user()->id)->first();

        $operationManagers = User::where('role_id',11)->where('status', 'Active')->pluck('email');

        foreach ($operationManagers  as $email) {
            Mail::to($email)->queue(new RequestRechargeMail($driverData, $opSupervisr, $requestRecharge));
        }

        session()->flash('success', __('Recharge request has been successfully submitted to the Operation Manager for review and further processing.'));
        return redirect()->route('request-recharge.index');
    }
}
