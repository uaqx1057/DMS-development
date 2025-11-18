<?php

namespace App\Livewire\DMS\DriverDifference;

use App\Models\Driver;
use App\Traits\DataTableTrait;
use Livewire\Component;

class DriverDifferenceList extends Component
{
    use DataTableTrait;

    private string $main_menu = 'Difference';
    private string $menu = 'Driver Difference List';

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
        $add_permission = CheckPermission(config('const.ADD'), config('const.DRIVERDIFFERENCE'));
        $edit_permission = false;

        $columns = [
            ['label' => 'Driver Name', 'column' => 'name', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Iqaama Number', 'column' => 'iqaama_number', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Total Difference', 'column' => 'total_receipt', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Total Paid', 'column' => 'total_paid', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Total Remaining', 'column' => 'total_remaining', 'isData' => true, 'hasRelation' => false],
        ];

        $drivers = Driver::query()

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
            })

            /** SIMPLE SEARCH */
            ->when($this->search, function ($q) {
                $s = "%{$this->search}%";
                $q->where('name', 'like', $s)
                    ->orWhere('iqaama_number', 'like', $s);
            })

            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);

        /** Final Calculations */
        $drivers->getCollection()->transform(function ($driver) {

            $collected = $driver->cash_collected_by_driver ?? 0;
            $receipts = $driver->total_driver_receipts ?? 0;
            $driver_paid = $driver->driver_diff_paid ?? 0;

            /** Driver still has this pending */
            $driver->total_receipt = $collected - $receipts;

            /** PAID = driver_difference.total_paid + driver_receipts.amount_received */
            $driver->total_paid = $driver_paid + $receipts;

            /** remaining = total_receipt - total_paid */
            $driver->total_remaining = $driver->total_receipt - $driver->total_paid;

            return $driver;
        });

        return view('livewire.dms.driver-difference.driver-difference-list', compact(
            'drivers', 'main_menu', 'menu', 'add_permission', 'edit_permission', 'columns'
        ));
    }
}
