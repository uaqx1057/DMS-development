<?php
namespace App\Repositories;

use App\Enums\CoordinatorReportStatus;
use App\Interfaces\RevenueReportInterface;
use App\Models\Driver;
use App\Models\Field;
use App\Models\Business;
use Carbon\Carbon;

class RevenueReportRepository implements RevenueReportInterface
{

    public function all(int|null $branch_id = null)
    {
        $currentDate = Carbon::now();
        $startDate = $currentDate->startOfMonth()->toDateString();
        $endDate = Carbon::today()->toDateString();

        // Calculate the difference in days between the start date and end date
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $daysDifference = $start->diffInDays($end) + 1;

       if($branch_id!=''){
           $qry=Driver::where('branch_id', $branch_id);
       }else{
            $qry=Driver::query();
       }

        $drivers = $qry
        ->whereHas('coordinator_report', function ($query) {
        $query->where('status', CoordinatorReportStatus::Approved);
        })
        ->with([
        'branch',
        'driver_type',
        'coordinator_report' => function ($query) {
        $query->where('status', CoordinatorReportStatus::Approved)
          ->with('report_fields');
        }
        ])
        ->get()

        ->map(function ($driver) use ($daysDifference) {
            // Branch and Contract Type
            $branch = $driver->branch->name ?? '';
            $contract = $driver->driver_type->name ?? '';

            // Total Orders Calculation
            $fieldId = Field::where('name', 'Total Orders')->pluck('id')->first();
            $total_orders = $driver->coordinator_report->flatMap(function ($report) use ($fieldId) {
                return $report->report_fields->where('field_id', $fieldId);
            })->sum('value');

            // Total Revenue Calculation
            $business_reports = $driver->coordinator_report->flatMap->report_fields
                ->groupBy('business_id')
                ->map(function ($reports, $business_id) use ($fieldId) {
                    $total_orders = $reports->where('field_id', $fieldId)->sum('value');
                    return [
                        'business_id' => $business_id,
                        'total_orders' => $total_orders,
                    ];
                });

            $total_revenue = $business_reports->sum(function ($business_report) {
                $businessCalculations = Business::with('driver_calculations')->find($business_report['business_id']);
                $total_orders = $business_report['total_orders'];
                $total_revenue = 0;

                if ($businessCalculations && $businessCalculations->driver_calculations) {
                    foreach ($businessCalculations->driver_calculations as $calculation) {
                        if ($calculation->calculation_type == 'RANGE' && $total_orders >= $calculation->from_value && $total_orders <= $calculation->to_value) {
                            $total_revenue += $total_orders * $calculation->amount;
                            break;
                        } elseif ($calculation->calculation_type == 'FIXED') {
                            $total_revenue += $total_orders * $calculation->amount;
                        }
                    }
                }

                return $total_revenue;
            });
            // dd($total_revenue);
            // Total Cost Calculation
            $calculated_salary = $this->calculate_driver_order_price($total_orders, 26, $driver->driver_type->is_freelancer);
            $total_salary = $calculated_salary > 0 ? $calculated_salary : 0;
            $total_gprs = ($driver->gprs / 30) * $daysDifference;
            $total_fuel = ($driver->fuel / 30) * $daysDifference;
            $total_government_cost = ($driver->government_cost / 30) * $daysDifference;
            $total_accommodation = ($driver->accommodation / 30) * $daysDifference;
            $total_vehicle_monthly_cost = ($driver->vehicle_monthly_cost / 30) * $daysDifference;
            $total_mobile_data = ($driver->mobile_data / 30) * $daysDifference;
            $total_cost = $total_salary + $total_gprs + $total_fuel + $total_government_cost + $total_accommodation + $total_vehicle_monthly_cost + $total_mobile_data;

            // Profit/Loss Calculation
            $gross_profit = $total_revenue - $total_cost;

            return (object) [
                'name' => $driver->name,
                'branch' => $branch,
                'contract' => $contract,
                'total_orders' => number_format($total_orders),
                'total_revenue' => number_format($total_revenue),
                'total_cost' => number_format($total_cost, 2),
                'profit_loss' => number_format($gross_profit, 2),
            ];
        });

        return $drivers;
    }

public function getBusinesses(int|null $branch_id = null)
{
    // Fetch businesses along with their reports and fields
    $businesses = Business::with(['coordinator_report' => function ($query) use ($branch_id) {
        $query->with('report_fields');

        // Apply branch filter if branch_id is provided
        if ($branch_id!='') {
            $query->whereHas('driver', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            });
        }
    }])->get();

    // Get the field ID for "Total Orders"
    $fieldId = Field::where('name', 'Total Orders')->pluck('id')->first();

    foreach ($businesses as $business) {
        $totalSum = 0;

        foreach ($business->coordinator_report as $report) {
            if ($report->status == CoordinatorReportStatus::Approved->value) {
                $totalSum += $report->report_fields
                    ->where('business_id', $business->id)
                    ->where('field_id', $fieldId)
                    ->sum('value');
            }
        }

        $business->total_orders = $totalSum;
    }

    return $businesses;
}



public function getRevenueStats(int|null $branch_id = null)
{
    $currentDate = Carbon::now();
    $startDate = $currentDate->startOfMonth()->toDateString();
    $endDate = Carbon::today()->toDateString();
    $daysDifference = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

    // Get the field ID for "Total Orders"
    $fieldId = Field::where('name', 'Total Orders')->value('id');

    $drivers = Driver::when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
        ->whereHas('coordinator_report', fn($q) => $q->where('status', CoordinatorReportStatus::Approved))
        ->with([
            'branch',
            'driver_type',
            'coordinator_report' => fn($q) => $q->where('status', CoordinatorReportStatus::Approved)->with('report_fields'),
        ])
        ->get();

    $total_cost = 0;
    $total_orders = 0;
    $total_revenue = 0;

    foreach ($drivers as $driver) {
        $business_reports = [];

        foreach ($driver->coordinator_report as $report) {
            foreach ($report->report_fields->where('field_id', $fieldId)->where('value', '!=', '') as $field) {
                $business_id = $field->business_id;

                if (!isset($business_reports[$business_id])) {
                    $business_reports[$business_id] = [
                        'business_id' => $business_id,
                        'total_orders' => 0,
                    ];
                }

                $business_reports[$business_id]['total_orders'] += intval($field->value);
            }
        }

        // Store per-driver totals
        $driver->business_reports = array_values($business_reports);
        $driver->total_orders = array_sum(array_column($driver->business_reports, 'total_orders'));

        // ✅ Calculate revenue ONLY from "Total Orders"
        foreach ($driver->business_reports as $business_report) {
            $business = Business::with('driver_calculations')->find($business_report['business_id']);

            if ($business && $business->driver_calculations) {
                foreach ($business->driver_calculations as $calc) {
                    if ($calc->calculation_type == 'RANGE' &&
                        $business_report['total_orders'] >= $calc->from_value &&
                        $business_report['total_orders'] <= $calc->to_value) {
                        $total_revenue += $business_report['total_orders'] * $calc->amount;
                        break;
                    }

                    if ($calc->calculation_type == 'FIXED') {
                        $total_revenue += $business_report['total_orders'] * $calc->amount;
                    }
                }
            }
        }

        // ✅ Calculate driver costs
        $calculated_salary = $this->calculate_driver_order_price($driver->total_orders, 26, $driver->driver_type->is_freelancer);
        $driver->total_salary = max(0, $calculated_salary);

        $driver->total_cost =
            ($driver->gprs / 30 * $daysDifference) +
            ($driver->fuel / 30 * $daysDifference) +
            ($driver->government_cost / 30 * $daysDifference) +
            ($driver->accommodation / 30 * $daysDifference) +
            ($driver->vehicle_monthly_cost / 30 * $daysDifference) +
            ($driver->mobile_data / 30 * $daysDifference) +
            $driver->total_salary;

        $total_cost += $driver->total_cost;
        $total_orders += $driver->total_orders;
    }

    $gross_profit = $total_revenue - $total_cost;

    return (object) [
        'gross_profit'   => number_format($gross_profit, 2),
        'total_cost'     => number_format($total_cost, 2),
        'total_orders'   => number_format($total_orders, 2),
        'total_revenue'  => number_format($total_revenue, 2),
    ];
}


    // public function getRevenueStats(){
    //     $currentDate = Carbon::now();
    //     $startDate = $currentDate->startOfMonth()->toDateString();
    //     $endDate = Carbon::today()->toDateString();

    //     // Calculate the difference in days between the start date and end date
    //     $start = Carbon::parse($startDate);
    //     $end = Carbon::parse($endDate);
    //     $daysDifference = $start->diffInDays($end) + 1;

    //     $drivers = Driver::whereHas('coordinator_report', function ($query)  {
    //         $query->where('status', CoordinatorReportStatus::Approved);
    //     })
    //     ->with([
    //         'branch',
    //         'driver_type',
    //         'coordinator_report' => function ($query) {
    //             $query->where('status', CoordinatorReportStatus::Approved);
    //         }
    //     ])
    //     ->get();

    //     $total_cost = 0;
    //     $total_orders = 0;
    //     $total_revenue = 0;
    //     foreach ($drivers as $driver) {
    //         $totalSum = 0;
    //         $business_reports = [];
    //         foreach ($driver->coordinator_report as $report) {
    //             $fieldId = Field::where(['name' => 'Total Orders'])->pluck('id')->first();
    //             $reportSum = $report->report_fields->where('field_id', $fieldId)->sum('value');
    //             $totalSum += $reportSum;

    //             // Aggregating total report_fields for each business_id
    //             $business_id = $report->business_id;
    //             if (!isset($business_reports[$business_id])) {
    //                 $business_reports[$business_id] = [
    //                     'business_id' => $business_id,
    //                     'total_orders' => 0,
    //                 ];
    //             }
    //             $business_reports[$business_id]['total_orders'] += $reportSum;

    //         }
    //         $driver->total_orders = $totalSum;
    //         $driver->business_reports = array_values($business_reports);

    //         foreach ($driver->business_reports as $business_report) {
    //             $businessCalculations = Business::with('driver_calculations')->find($business_report['business_id']);

    //             if ($businessCalculations && $businessCalculations->driver_calculations) {
    //                 foreach ($businessCalculations->driver_calculations as $calculation) {
    //                     if ($calculation->calculation_type == 'RANGE' &&
    //                         $business_report['total_orders'] >= $calculation->from_value &&
    //                         $business_report['total_orders'] <= $calculation->to_value) {

    //                         $total_revenue += $business_report['total_orders'] * $calculation->amount;
    //                         break; // Assuming each total_orders fits only one range, you can break the loop once matched
    //                     }
    //                     if($calculation->calculation_type == 'FIXED'){
    //                         $total_revenue += $business_report['total_orders'] * $calculation->amount;
    //                     }
    //                 }
    //             }
    //         }

    //         // Sum of specific fields from the driver
    //         $calculated_salary = $this->calculate_driver_order_price($driver->total_orders, 26, $driver->driver_type->is_freelancer);
    //         $driver->total_salary =  $calculated_salary > 0 ? $calculated_salary : 0;
    //         $total_coordinate_days = $driver->coordinator_report->count();
    //         $total_gprs = $daysDifference ? ($driver->gprs / 30) * $daysDifference : 0;
    //         $total_fuel =  $daysDifference ? ($driver->fuel / 30) * $daysDifference: 0;
    //         $total_government_cost = $daysDifference ? ($driver->government_cost / 30) * $daysDifference: 0;
    //         $total_accommodation = $daysDifference ? ($driver->accommodation / 30) * $daysDifference: 0;
    //         $total_vehicle_monthly_cost = $daysDifference ? ($driver->vehicle_monthly_cost / 30) * $daysDifference: 0;
    //         $total_mobile_data = $daysDifference ? ($driver->mobile_data / 30) * $daysDifference: 0;

    //         $driver->total_cost = $driver->total_salary + $total_gprs + $total_fuel + $total_government_cost + $total_accommodation + $total_vehicle_monthly_cost + $total_mobile_data;
    //         $total_cost += $driver->total_cost;
    //         $total_orders += $driver->total_orders;
    //     }
    //     $gross_profit = $total_revenue - $total_cost;

    //     return (object) [
    //         'gross_profit' => number_format($gross_profit, 2),
    //         'total_cost' => number_format($total_cost, 2),
    //         'total_orders' => number_format($total_orders, 2),
    //         'total_revenue' => number_format($total_revenue, 2),
    //     ];
    // }

    private function calculate_driver_order_price($total_order, $working_days, $freelancer)
    {
        $WORKING_DAYS_PER_MONTH = 26;
        $BASE_SALARY_PER_MONTH = 400;
        $BASE_ORDER_LIMIT_PER_MONTH = 250;
        $COMMISSION_RATE = 9;

        $base_salary = $this->calculate_base_salary($working_days, $freelancer, $BASE_SALARY_PER_MONTH, $WORKING_DAYS_PER_MONTH);
        $base_order_limit = $this->calculate_base_order_limit($working_days, $freelancer, $BASE_ORDER_LIMIT_PER_MONTH, $WORKING_DAYS_PER_MONTH);
        $per_order_base_salary = $base_salary / $base_order_limit;

        if ($total_order <= $base_order_limit) {
            $deductions = $per_order_base_salary * ($base_order_limit - $total_order);
            $commission_amount = 0;
        } else {
            $deductions = 0;
            $commission_amount = ($total_order - $base_order_limit) * $COMMISSION_RATE;
        }

        $final_salary = ($base_salary + $commission_amount) - $deductions;

        return $final_salary > 0 ? $final_salary : 0;
    }

    private function calculate_base_salary($working_days, $freelancer, $BASE_SALARY_PER_MONTH, $WORKING_DAYS_PER_MONTH)
    {
        if ($freelancer) {
            return ($BASE_SALARY_PER_MONTH / $WORKING_DAYS_PER_MONTH) * min($working_days, $WORKING_DAYS_PER_MONTH);
        }
        return $BASE_SALARY_PER_MONTH;
    }

    private function calculate_base_order_limit($working_days, $freelancer, $BASE_ORDER_LIMIT_PER_MONTH, $WORKING_DAYS_PER_MONTH)
    {
        if ($freelancer) {
            return ($BASE_ORDER_LIMIT_PER_MONTH / $WORKING_DAYS_PER_MONTH) * min($working_days, $WORKING_DAYS_PER_MONTH);
        }
        return $BASE_ORDER_LIMIT_PER_MONTH;
    }



}
