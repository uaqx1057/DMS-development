<?php
namespace App\Livewire\DMS\Payroll;

use App\Services\PayrollService;
use App\Traits\DataTableTrait;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Payroll List')]
class PayrollList extends Component
{
    use DataTableTrait;
    public string $main_menu = 'Payroll';
    public string $menu = 'Payroll List';

    protected $payrollService;

    public function boot(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $payrolls = $this->payrollService->all($this->perPage);

        $columns = [
            ['label' => 'IQAAMA NUMBER', 'column' => 'iqaama_number', 'isData' => true,'hasRelation'=> false],
            ['label' => 'NAME', 'column' => 'name', 'isData' => true,'hasRelation'=> false],
            ['label' => 'CONTRACT TYPE', 'column' => 'driver_type', 'isData' => true,'hasRelation'=> true, 'columnRelation' => 'name'],
            ['label' => 'WORKING DAYS', 'column' => 'working_days', 'isData' => true,'hasRelation'=> false],
            ['label' => 'TOTAL ORDERS', 'column' => 'total_orders', 'isData' => true,'hasRelation'=> false],
            ['label' => 'DEDUCTIONS', 'column' => 'deductions', 'isData' => true,'hasRelation'=> false],
            ['label' => 'BONUS', 'column' => 'total_bonus', 'isData' => true,'hasRelation'=> false],
            ['label' => 'TIP', 'column' => 'total_tip', 'isData' => true,'hasRelation'=> false],
            ['label' => 'OTHER TIP', 'column' => 'total_other_tip', 'isData' => true,'hasRelation'=> false],
            ['label' => 'COMMISSION AMOUNT', 'column' => 'total_comission', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Per Order SALARY', 'column' => 'base_salary', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Final SALARY', 'column' => 'gross_salary', 'isData' => true,'hasRelation'=> false],
        ];

        return view('livewire.dms.payroll.payroll-list', compact('main_menu', 'menu', 'payrolls', 'columns'));
    }
}
