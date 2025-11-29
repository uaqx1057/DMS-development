<?php

namespace App\Livewire\DMS\RequestRecharge;

use App\Mail\RequestRechargeMail;
use App\Models\Driver;
use App\Models\RequestRecharge;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class CreateRequestRecharge extends Component
{
    public string $main_menu = 'Recharge';
    public string $menu = 'Request Recharge';

    public $driver;
    public $phone_number;
    public $operator;
    public function render()
    {
        // $drivers = Driver::when(auth()->user()->role_id != 1, function ($query) {
        //         $query->where('branch_id', auth()->user()->branch_id);
        //     })
        //     ->get();
        $drivers = Driver::orderBy('name', 'asc')->get();

        $main_menu = $this->main_menu;
        $menu = $this->menu;

        return view('livewire.dms.request-recharge.create-request-recharge', compact(
            'main_menu',
            'menu',
            'drivers'
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
