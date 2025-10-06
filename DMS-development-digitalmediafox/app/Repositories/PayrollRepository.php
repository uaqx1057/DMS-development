<?php
namespace App\Repositories;

use App\Enums\CoordinatorReportStatus;
use App\Interfaces\PayrollInterface;
use App\Models\Branch;
use App\Models\Driver;
use App\Models\Field;

class PayrollRepository implements PayrollInterface
{
    public function all($perPage = 10,int|null $branch_id = null)
    {
        $drivers = Driver::when($branch_id, function ($query, $branch_id) {
        $query->where('branch_id', $branch_id);
        })
        ->whereHas('coordinator_report', function ($query) {
        $query->where('status', CoordinatorReportStatus::Approved);
        })
        ->with([
        'branch',
        'driver_type',
        'coordinator_report' => function ($query) {
        $query->where('status', CoordinatorReportStatus::Approved);
        }
        ])
        ->paginate($perPage);


        foreach ($drivers as $driver) {
            $comissionSum = 0;
            $groupedDrivers = collect();
            $total_orders = 0;
            $total_tip = 0;
            $total_other_tip = 0;
            $total_bonus = 0;

            foreach ($driver->coordinator_report as $report) {
                $orderFieldId = Field::where('name', 'Total Orders')->pluck('id')->first();
                $tipFieldId = Field::where('name', 'Tip')->pluck('id')->first();
                $otherTipFieldId = Field::where('name', 'Other Tip')->pluck('id')->first();
                $bonusFieldId = Field::where('name', 'Bonus')->pluck('id')->first();

                $tip = $report->report_fields->where('field_id', $tipFieldId)->where('value', '!=', '')->sum('value');
                $otherTip = $report->report_fields->where('field_id', $otherTipFieldId)->where('value', '!=', '')->sum('value');
                $bonus = $report->report_fields->where('field_id', $bonusFieldId)->where('value', '!=', '')->sum('value');

                $total_tip += $tip;
                $comissionSum += $tip;

                $total_other_tip += $otherTip;
                $comissionSum += $otherTip;

                $total_bonus += $bonus;
                $comissionSum += $bonus;

                $total_orders += $report->report_fields->where('field_id', $orderFieldId)->sum('value');

                $reportDate = $report->report_date;
                if (!$groupedDrivers->has($reportDate)) {
                    $groupedDrivers->put($reportDate, collect());
                }
                $groupedDrivers->get($reportDate)->push($driver);
            }

            $driver->working_days = count($groupedDrivers);
            $driver->total_comission = number_format($comissionSum, 2);

            $calculated_salary = $this->calculate_driver_order_price($total_orders, $driver->working_days, $driver->driver_type->is_freelancer);
            $driver->gross_salary = number_format($calculated_salary['gross_salary'], 2);
            $driver->base_salary = number_format($calculated_salary['base_salary'], 2);
            $driver->deductions = number_format($calculated_salary['deductions'], 2);
            $driver->salary = number_format($calculated_salary['gross_salary'] + $comissionSum - $calculated_salary['deductions'], 2);
            $driver->total_orders = number_format($total_orders);
            $driver->total_tip = number_format($total_tip, 2);
            $driver->total_other_tip = number_format($total_other_tip, 2);
            $driver->total_bonus = number_format($total_bonus, 2);
        }

        return $drivers;
    }


    /**
     * Calculate driver order price based on total orders, working days, and freelancer status.
     */
    private function calculate_driver_order_price($total_order, $working_days, $freelancer)
    {
        $WORKING_DAYS_PER_MONTH = 26;
        $BASE_SALARY_PER_MONTH = 400;
        $BASE_ORDER_LIMIT_PER_MONTH = 250;
        $COMMISSION_RATE = 9;

        $base_salary = $this->calculate_base_salary($working_days, $freelancer, $BASE_SALARY_PER_MONTH, $WORKING_DAYS_PER_MONTH); // 400
        $base_order_limit = $this->calculate_base_order_limit($working_days, $freelancer, $BASE_ORDER_LIMIT_PER_MONTH, $WORKING_DAYS_PER_MONTH); // 26
        $per_order_base_salary = $base_salary / $base_order_limit; // 400 / 250 => 1.6

        if ($total_order <= $base_order_limit) {
            $deductions = $per_order_base_salary * ($base_order_limit - $total_order); // 1.6 * (250 - 10) =>
            $commission_amount = 0;
        } else {
            $deductions = 0;
            $commission_amount = ($total_order - $base_order_limit) * $COMMISSION_RATE;
        }

        $final_salary = ($base_salary + $commission_amount) - $deductions;

        return [
            'gross_salary' => $final_salary > 0 ? $final_salary : 0,
            'base_salary' => $per_order_base_salary,
            'deductions' => $deductions,
        ];
    }

    /**
     * Calculate base salary based on working days and freelancer status.
     */
    private function calculate_base_salary($working_days, $freelancer, $BASE_SALARY_PER_MONTH, $WORKING_DAYS_PER_MONTH)
    {
        if ($freelancer) {

            return ($BASE_SALARY_PER_MONTH / $WORKING_DAYS_PER_MONTH) * min($working_days, $WORKING_DAYS_PER_MONTH);
        }
        return $BASE_SALARY_PER_MONTH;
    }

    /**
     * Calculate base order limit based on working days and freelancer status.
     */
    private function calculate_base_order_limit($working_days, $freelancer, $BASE_ORDER_LIMIT_PER_MONTH, $WORKING_DAYS_PER_MONTH)
    {
        if ($freelancer) {
            return ($BASE_ORDER_LIMIT_PER_MONTH / $WORKING_DAYS_PER_MONTH) * min($working_days, $WORKING_DAYS_PER_MONTH);
        }
        return $BASE_ORDER_LIMIT_PER_MONTH;
    }
}
