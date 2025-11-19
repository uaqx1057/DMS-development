<?php

namespace App\Livewire\DMS\OperationSuperviser;

use App\Models\DriverDifference;
use App\Models\DriverReceipt;
use App\Models\OperationSuperviserDifference;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateOperationSuperviserDifference extends Component
{
    use WithFileUploads;

    public string $main_menu = 'Difference';
    public string $menu = 'Supervisor Difference';

    public $superviser;
    public $user_id;
    public $total_receipt;

    public $receipt_date;
    public $receipt_image;
    public $total_paid;
    public $total_remaining;

    public function updatedSuperviser($superviserID)
    {
        if ($superviserID) {
            $alread_paid = OperationSuperviserDifference::where('superviser_id', $superviserID)->sum('total_paid');
            
            $total_driver_receipt = DriverReceipt::where('user_id', $superviserID)->sum('amount_received');

            $total_driver_difference = DriverDifference::where('user_id', $superviserID)->sum('total_paid');

            $alread_paid = $alread_paid ?? 0;

            $total_driver_receipt = $total_driver_receipt ?? 0;

            $total_driver_difference = $total_driver_difference ?? 0;

            $this->total_receipt = $total_driver_receipt + $total_driver_difference - $alread_paid;
            $this->total_remaining = $total_driver_receipt + $total_driver_difference - $alread_paid;
        

        } else {
            $this->total_receipt = 0;
        }
    }

    public function updatedTotalPaid($value)
    {
        $value = $value === '' ? 0 : $value;
        // If no driver selected yet
        if (!$this->total_receipt) {
            $this->total_paid = 0;
            return;
        }

        // If entered amount is greater than total_receipt
        if ($value > $this->total_receipt) {

            $this->addError('total_paid', 'Paid amount cannot be greater than total receipt.');

            // Reset values
            $this->total_paid = '';
            $this->total_remaining = $this->total_receipt;

            return;
        }

        // Remove error automatically when valid
        $this->resetErrorBag('total_paid');

        // Calculate remaining amount
        $this->total_remaining = $this->total_receipt - $value;
    }
    public function render()
    {
        $superviser_ids = User::query()

            /** 🔥 Branch filter only for NON-admin users */
            ->when(auth()->user()->role_id != 1, function ($q) {
                $q->where('branch_id', auth()->user()->branch_id);
            })

            /** ✅ Load amount_received from driver_receipts */
            ->withSum('driverReceipts as total_driver_receipts', 'amount_received')

            /** ✅ Load amount_received from driver_receipts */
            ->withSum('driverDifferences as total_driver_difference', 'total_paid')

            /** ✅ Load total_paid from driver_differences */
            ->withSum('operationSuperviserDifference as driver_diff_paid', 'total_paid')

            ->where('role_id', 8)
            ->havingRaw('(COALESCE(total_driver_difference, 0) + COALESCE(total_driver_receipts, 0) - COALESCE(driver_diff_paid, 0)) > 0')->pluck('id')
            ->unique()
            ->toArray();

        $main_menu = $this->main_menu;
        $menu = $this->menu;

        $supervisers = User::whereIn('id', $superviser_ids)->get();
        return view('livewire.dms.operation-superviser.create-operation-superviser-difference', compact(
            'main_menu',
            'menu',
            'supervisers'
        ));
    }

    public function save()
    {
        // Validate required fields
        $this->validate([
            'superviser' => 'required|exists:drivers,id',
            'total_receipt' => 'required|numeric|min:0',
            'total_paid' => 'required|numeric|min:0|max:' . $this->total_receipt,
            'total_remaining' => 'required|numeric|min:0',
            'receipt_image' => 'required|image',
            'receipt_date' => 'required|date|before_or_equal:today',
        ]);

        $superviserData = User::where('id',$this->superviser)->first();

        if ($this->receipt_image) {
            $filename = now()->format('Ymd_His') . '-' . $superviserData->name . '.' . $this->receipt_image->getClientOriginalExtension();
            $path = $this->receipt_image->storeAs('receipts', $filename, 'public');
        } else {
            $path = null;
        }


        // Optional: Save difference as a separate table entry
        OperationSuperviserDifference::create([
            'superviser_id' => $this->superviser,
            'created_by' => auth()->user()->id,
            'total_receipt' => $this->total_receipt,
            'total_paid' => $this->total_paid,
            'total_remaining' => $this->total_remaining,
            'receipt_date' => $this->receipt_date,
            'receipt_image' => $path,
        ]);

        session()->flash('success', __('Superviser Difference created successfully.'));
        return redirect()->route('superviser-difference.index');
    }
}
