<?php

namespace App\Livewire\DMS\Recharge;

use App\Mail\RechargeMail;
use App\Models\Driver;
use App\Models\Recharge;
use App\Models\RequestRecharge;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\WithFileUploads;
use Livewire\Component;

class CreateRecharge extends Component
{
    use WithFileUploads, WithFileUploads;
    public $request_recharge_id;
    public $amount;
    public $date;
    public $image;

    public string $main_menu = 'Recharge';
    public string $menu = 'Create Recharge';
    public function mount($id)
    {
        $this->request_recharge_id = $id;
        logger($this->request_recharge_id);
        if (session()->has('page')) {
            $this->page = session('page');
        }
    }
    
    public function render()
    {
        $requestRecharge = RequestRecharge::find($this->request_recharge_id);
        if ($requestRecharge->status !== 'accepted') {
            abort(404);
        }

        $existRecharge = Recharge::where('request_recharge_id', $this->request_recharge_id)->first();
        if ($existRecharge) {
            abort(404);
        }

        return view('livewire.dms.recharge.create-recharge', [
            'main_menu'   => $this->main_menu,
            'menu'        => $this->menu,
        ]);
    }

    public function save()
    {
        $this->validate([
            'amount' => 'required',
            'image' => 'required|mimetypes:image/*,application/pdf|max:2048',
            'date' => 'required|date|before_or_equal:today',
        ]);

        $requestRecharge = RequestRecharge::find($this->request_recharge_id);

        $driverRecharge = Driver::where('id',$requestRecharge->driver_id)->first();

        if ($this->image) {
            $filename = now()->format('Ymd_His') . '-' . $driverRecharge->name . '-' . $driverRecharge->iqaama_number . '.' . $this->image->getClientOriginalExtension();
            $path = $this->image->storeAs('recharge', $filename, 'public');
        } else {
            $path = null;
        }

        $recharge =  Recharge::create([
            'request_recharge_id' => $this->request_recharge_id,
            'user_id' => auth()->id(),
            'amount' => $this->amount,
            'date' => $this->date,
            'image' => $path,
        ]);

        $driverData = Driver::with('branch')->where('id', $requestRecharge->driver_id)->first();
        $rechargeBy = User::with('branch')->with('role')->where('id', auth()->user()->id)->first();

        $users = User::whereIn('role_id', [1, 8, 12, 11])->where('status', 'Active')->pluck('email');

        foreach ($users  as $email) {
            Mail::to($email)->queue(new RechargeMail($driverData, $rechargeBy,  $recharge, $requestRecharge));
        }

        session()->flash('success', __('Your Recharged request has been processed successfully, and the recharge has been completed.'));
        return redirect()->route('recharge.index');
    }
}
