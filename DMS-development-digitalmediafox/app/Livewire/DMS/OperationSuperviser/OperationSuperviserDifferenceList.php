<?php

namespace App\Livewire\DMS\OperationSuperviser;

use App\Models\DriverReceipt;
use App\Models\OperationSuperviserDifference;
use App\Models\User;
use App\Traits\DataTableTrait;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OperationSuperviserDifferenceList extends Component
{
    use DataTableTrait;

    private string $main_menu = 'Difference';
    private string $menu = 'Supervisor Difference List';

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
            ['label' => 'Supvisor Name', 'column' => 'name', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Total Difference', 'column' => 'total_receipt', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Total Paid', 'column' => 'total_paid', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Total Remaining', 'column' => 'total_remaining', 'isData' => true, 'hasRelation' => false],
        ];

        $superviser_differences = User::query()

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


            /** SIMPLE SEARCH */
            ->when($this->search, function ($q) {
                $s = "%{$this->search}%";
                $q->where('name', 'like', $s);
            })

            ->where('role_id', 8)
            ->havingRaw('(COALESCE(total_driver_difference, 0) + COALESCE(total_driver_receipts, 0) - COALESCE(driver_diff_paid, 0)) > 0')
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);

        /** Final Calculations */
        $superviser_differences->getCollection()->transform(function ($superviser) {
            $superviser->total_driver_difference = $superviser->total_driver_difference ?? 0;
            $superviser->total_driver_receipts = $superviser->total_driver_receipts ?? 0;

            $superviser->total_receipt = number_format($superviser->total_driver_receipts + $superviser->total_driver_difference, 2, '.', '');

            $superviser->total_paid = number_format($superviser->driver_diff_paid, 2, '.', '') ?? 0;
            $superviser->total_remaining = number_format($superviser->total_receipt - $superviser->total_paid, 2, '.', '');
            return $superviser;
        });

        // logger($superviser_differences);

        return view('livewire.dms.operation-superviser.operation-superviser-difference-list', compact(
            'superviser_differences',
            'main_menu',
            'menu',
            'add_permission',
            'edit_permission',
            'columns'
        ));
    }
}
