<?php

namespace App\Livewire\DMS\AmountTransfer;

use App\Models\AmountTransfer;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateAmountTransfer extends Component
{
    use WithFileUploads;

    public string $main_menu = 'Transfer';
    public string $menu = 'Amount Transfer';

    public $supervisor;
    public $user_id;
    public $payment_type;
    public $amount;
    public $receipt_image;
    public $receipt_date;

    public function mount()
    {
        if (auth()->user()->role_id == 8) {
            $this->supervisor = auth()->user()->id;
        }
    }

    public function boot()
    {
        if (auth()->user()->role_id == 8) {
            $this->supervisor = auth()->user()->id;
        }
    }
    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $supervisors = User::where('role_id', 8)->get();
        return view('livewire.dms.amount-transfer.create-amount-transfer', compact(
            'main_menu',
            'menu',
            'supervisors'
        ));
    }

     public function save()
    {
        // Validate required fields
        $this->validate([
            'supervisor' => 'required',
            'payment_type' => 'required',
            'amount' => 'required',
            'receipt_image' => 'required|mimetypes:image/*,application/pdf|max:2048',
            'receipt_date' => 'required|date|before_or_equal:today',
        ]);

        $supervisorData = User::where('id',$this->supervisor)->first();

        if ($this->receipt_image) {
            $filename = now()->format('Ymd_His') . '-' . $supervisorData->name . '.' . $this->receipt_image->getClientOriginalExtension();
            $path = $this->receipt_image->storeAs('receipts', $filename, 'public');
        } else {
            $path = null;
        }


        // Optional: Save difference as a separate table entry
        AmountTransfer::create([
            'supervisor_id' => $this->supervisor,
            'created_by' => auth()->user()->id,
            'payment_type' => $this->payment_type,
            'amount' => $this->amount,
            'receipt_date' => $this->receipt_date,
            'receipt_image' => $path,
            'branch_id' => $supervisorData->branch_id,
        ]);

        session()->flash('success', __('Amount added successfully.'));
        return redirect()->route('amount-transfer.index');
    }
}
