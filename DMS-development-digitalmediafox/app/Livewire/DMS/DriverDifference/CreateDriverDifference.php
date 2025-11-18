<?php

namespace App\Livewire\DMS\DriverDifference;

use App\Models\CoordinatorReport;
use App\Models\CoordinatorReportFieldValue;
use App\Models\DriverDifference;
use App\Models\DriverReceipt;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Driver;

class CreateDriverDifference extends Component
{
    use WithFileUploads;

    public string $main_menu = 'Difference';
    public string $menu = 'Driver Difference';

    public $driver_receipt;
    public $user_id;
    public $total_receipt;
    public $total_paid;
    public $total_remaining;

    public function updatedDriverReceipt($dirverID)
    {
        if ($dirverID) {

            $already_paid = DriverDifference::where('driver_id', $dirverID)->sum('total_paid');

            $total_driver_receipt = DriverReceipt::where('driver_id', $dirverID)->sum('amount_received');
            $driver_coordinate_reports_ids = CoordinatorReport::where('driver_id', $dirverID)->pluck('id')->unique()->toArray();

            // get from coordinator_report_field_values Table 
            $cash_collected_by_driver = CoordinatorReportFieldValue::whereIn('coordinator_report_id', $driver_coordinate_reports_ids)->where('field_id', 7)->sum('value');

            $already_paid = $already_paid ?? 0;
            $cash_collected_by_driver = $cash_collected_by_driver ?? 0;
            $total_driver_receipt = $total_driver_receipt ?? 0;


            // logger($cash_collected_by_driver);

            $this->total_receipt = $cash_collected_by_driver - $total_driver_receipt + $already_paid;
            

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
        $receipt_driver_ids = Driver::query()

            /** 🔥 Branch filter only for NON-admin users */
            ->when(auth()->user()->role_id != 1, function ($q) {
                $q->where('branch_id', auth()->user()->branch_id);
            })
            /** ✅ Load cash collected (field_id = 7) */
            ->withSum(['coordinatorReportFieldValues as cash_collected_by_driver' => function ($q) {
                $q->where('field_id', 7);
            }], 'value')

            /** ✅ Load amount_received from driver_receipts */
            ->withSum('driverReceipts as total_driver_receipts', 'amount_received')

            /** ✅ Load total_paid from driver_differences */
            ->withSum('driverDifferences as driver_diff_paid', 'total_paid')

            ->whereHas('coordinatorReportFieldValues', function ($q) {
                $q->where('field_id', 7);
            })->pluck('id')
            ->unique()
            ->toArray();

        $main_menu = $this->main_menu;
        $menu = $this->menu;

        $drivers = Driver::whereIn('id', $receipt_driver_ids)->get();
        return view('livewire.dms.driver-difference.create-driver-difference', compact(
            'main_menu',
            'menu',
            'drivers'
        ));
    }

    public function save()
    {
        // Validate required fields
        $this->validate([
            'driver_receipt' => 'required|exists:drivers,id',
            'total_receipt' => 'required|numeric|min:0',
            'total_paid' => 'required|numeric|min:0|max:' . $this->total_receipt,
            'total_remaining' => 'required|numeric|min:0',
        ]);


        // Optional: Save difference as a separate table entry
        DriverDifference::create([
            'driver_id' => $this->driver_receipt,
            'user_id' => auth()->user()->id,
            'total_receipt' => $this->total_receipt,
            'total_paid' => $this->total_paid,
            'total_remaining' => $this->total_remaining,
        ]);

        session()->flash('success', __('Driver Difference created successfully.'));
        return redirect()->route('driver-difference.index');
    }


}
