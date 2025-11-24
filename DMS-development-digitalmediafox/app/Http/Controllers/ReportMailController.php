<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\DailyDifferenceReportMail;
use App\Mail\MonthlyDifferenceReportMail;
use App\Mail\WeeklyDifferenceReportMail;
use App\Models\Driver;
use App\Models\DriverDifference;
use App\Models\DriverReceipt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReportMailController extends Controller
{
    public function dailyReports()
    {
        $supervisers = User::where('role_id', 8)->pluck('id')->toArray();

        $get_form_difference = DriverDifference::whereIn('user_id', $supervisers)
            ->whereDate('receipt_date', Carbon::today())
            ->sum('total_paid');

        $get_form_receipt = DriverReceipt::whereIn('user_id', $supervisers)
            ->whereDate('receipt_date', Carbon::today())
            ->sum('amount_received');

        $all_superviser_collected = number_format($get_form_difference + $get_form_receipt ?? 0, 2, '.', '');

        $total_superviser_collected = User::query()

            ->withSum([
                'driverDifferences as total_driver_difference' => function ($q) {
                    $q->whereDate('receipt_date', Carbon::today());
                }
            ], 'total_paid')
            ->withSum([
                'driverReceipts as total_driver_receipts' => function ($q) {
                    $q->whereDate('receipt_date', Carbon::today());
                }
            ], 'amount_received')
            ->withSum([
                'operationSuperviserDifference as driver_diff_paid' => function ($q) {
                    $q->whereDate('receipt_date', Carbon::today());
                }
            ], 'total_paid')
            ->with('branch')->where('role_id', 8)->get()
            ->transform(function ($supervisor) {
                $supervisor->total_driver_difference = $supervisor->total_driver_difference ?? 0;
                $supervisor->total_driver_receipts = $supervisor->total_driver_receipts ?? 0;

                $supervisor->total_receipt = number_format($supervisor->total_driver_receipts + $supervisor->total_driver_difference, 2, '.', '');

                $supervisor->total_paid = number_format($supervisor->driver_diff_paid, 2, '.', '') ?? 0;
                $supervisor->total_remaining = number_format($supervisor->total_receipt - $supervisor->total_paid, 2, '.', '');
                return $supervisor;
            });

        $total_drivers_collected = Driver::withSum([
            'coordinatorReportFieldValues as cash_collected_by_driver' => function ($q) {
                $q->where('field_id', 7)
                    ->whereHas('coordinatorReport', fn($query) => $query->whereDate('report_date', Carbon::today()));
            }
        ], 'value')
            ->withSum(['driverReceipts as total_driver_receipts' => fn($q) => $q->whereDate('receipt_date', Carbon::today())], 'amount_received')
            ->withSum(['driverDifferences as driver_diff_paid' => fn($q) => $q->whereDate('receipt_date', Carbon::today())], 'total_paid')
            ->get()
            ->transform(function ($driver) {
                $collected = number_format($driver->cash_collected_by_driver ?? 0, 2, '.', '');
                $receipts = number_format($driver->total_driver_receipts ?? 0, 2, '.', '');
                $driver_paid = number_format($driver->driver_diff_paid ?? 0, 2, '.', '');

                $driver->total_receipt = $collected;
                $driver->total_paid = number_format($driver_paid + $receipts, 2, '.', '');
                $driver->total_remaining = number_format($driver->total_receipt - $driver->total_paid, 2, '.', '');

                return $driver;
            });

        $data = [
            'all_superviser_collected' => $all_superviser_collected,
            'total_superviser_collected' => $total_superviser_collected,
            'total_drivers_collected' => $total_drivers_collected,
        ];

        $mailUsers = User::whereIn('role_id', [4, 8, 9])->pluck('email');

        foreach ($mailUsers as $email) {
            Mail::to($email)->queue(new DailyDifferenceReportMail($data));
        }

        return "Emails added to queue!";

    }
    public function weeklyReports()
    {
        $today = Carbon::today();
        // dd($today->toDateString()); 
        $last7Days = Carbon::today()->subDays(6);

        $supervisers = User::where('role_id', 8)->pluck('id')->toArray();

        $get_form_difference = DriverDifference::whereIn('user_id', $supervisers)
            ->whereBetween('receipt_date', [$last7Days, $today])
            ->sum('total_paid');

        $get_form_receipt = DriverReceipt::whereIn('user_id', $supervisers)
            ->whereBetween('receipt_date', [$last7Days, $today])
            ->sum('amount_received');

        $all_superviser_collected = number_format($get_form_difference + $get_form_receipt ?? 0, 2, '.', '');

        $total_superviser_collected = User::query()
            ->withSum([
                'driverDifferences as total_driver_difference' => function ($q) use ($last7Days, $today) {
                    $q->whereBetween('receipt_date', [$last7Days, $today]);
                }
            ], 'total_paid')

            ->withSum([
                'driverReceipts as total_driver_receipts' => function ($q) use ($last7Days, $today) {
                    $q->whereBetween('receipt_date', [$last7Days, $today]);
                }
            ], 'amount_received')

            ->withSum([
                'operationSuperviserDifference as driver_diff_paid' => function ($q) use ($last7Days, $today) {
                    $q->whereBetween('receipt_date', [$last7Days, $today]);
                }
            ], 'total_paid')
            ->with('branch')
            ->where('role_id', 8)
            ->get()
            ->transform(function ($supervisor) use ($last7Days, $today) {
                // Add a date range attribute
                $supervisor->date_range = $last7Days->toDateString() . ' to ' . $today->toDateString();

                $supervisor->total_driver_difference = $supervisor->total_driver_difference ?? 0;
                $supervisor->total_driver_receipts = $supervisor->total_driver_receipts ?? 0;

                $supervisor->total_receipt = number_format($supervisor->total_driver_receipts + $supervisor->total_driver_difference, 2, '.', '');

                $supervisor->total_paid = number_format($supervisor->driver_diff_paid, 2, '.', '') ?? 0;
                $supervisor->total_remaining = number_format($supervisor->total_receipt - $supervisor->total_paid, 2, '.', '');
                return $supervisor;
            });

        $total_drivers_collected = Driver::withSum([
            'coordinatorReportFieldValues as cash_collected_by_driver' => function ($q) use ($last7Days, $today) {
                $q->where('field_id', 7)
                    ->whereHas('coordinatorReport', function ($query) use ($last7Days, $today) {
                        $query->whereBetween('report_date', [$last7Days, $today]);
                    });
            }
        ], 'value')
            ->withSum([
                'driverReceipts as total_driver_receipts' => function ($q) use ($last7Days, $today) {
                    $q->whereBetween('receipt_date', [$last7Days, $today]);
                }
            ], 'amount_received')
            ->withSum([
                'driverDifferences as driver_diff_paid' => function ($q) use ($last7Days, $today) {
                    $q->whereBetween('receipt_date', [$last7Days, $today]);
                }
            ], 'total_paid')
            ->get()
            ->transform(function ($driver) use ($last7Days, $today) { // <--- add use here
                $collected = number_format($driver->cash_collected_by_driver ?? 0, 2, '.', '');
                $receipts = number_format($driver->total_driver_receipts ?? 0, 2, '.', '');
                $driver_paid = number_format($driver->driver_diff_paid ?? 0, 2, '.', '');

                $driver->total_receipt = $collected;
                $driver->total_paid = number_format($driver_paid + $receipts, 2, '.', '');
                $driver->total_remaining = number_format($driver->total_receipt - $driver->total_paid, 2, '.', '');

                $driver->date_range = $last7Days->toDateString() . ' to ' . $today->toDateString();

                return $driver;
            });

        $data = [
            'all_superviser_collected' => $all_superviser_collected,
            'total_superviser_collected' => $total_superviser_collected,
            'total_drivers_collected' => $total_drivers_collected,
        ];

        $mailUsers = User::whereIn('role_id', [4, 8, 9])->pluck('email');

        foreach ($mailUsers as $email) {
            Mail::to($email)->queue(new WeeklyDifferenceReportMail($data));
        }

        return "Emails added to queue!";

    }

    public function monthlyReports()
    {
        $today = Carbon::today(); // 2025-11-24
        $last30Days = (clone $today)->subMonth()->addDay(); // 2025-10-25

        $supervisers = User::where('role_id', 8)->pluck('id')->toArray();

        $get_form_difference = DriverDifference::whereIn('user_id', $supervisers)
            ->whereBetween('receipt_date', [$last30Days, $today])
            ->sum('total_paid');

        $get_form_receipt = DriverReceipt::whereIn('user_id', $supervisers)
            ->whereBetween('receipt_date', [$last30Days, $today])
            ->sum('amount_received');

        $all_superviser_collected = number_format($get_form_difference + $get_form_receipt ?? 0, 2, '.', '');

        $total_superviser_collected = User::query()
            ->withSum([
                'driverDifferences as total_driver_difference' => function ($q) use ($last30Days, $today) {
                    $q->whereBetween('receipt_date', [$last30Days, $today]);
                }
            ], 'total_paid')

            ->withSum([
                'driverReceipts as total_driver_receipts' => function ($q) use ($last30Days, $today) {
                    $q->whereBetween('receipt_date', [$last30Days, $today]);
                }
            ], 'amount_received')

            ->withSum([
                'operationSuperviserDifference as driver_diff_paid' => function ($q) use ($last30Days, $today) {
                    $q->whereBetween('receipt_date', [$last30Days, $today]);
                }
            ], 'total_paid')
            ->with('branch')
            ->where('role_id', 8)
            ->get()
            ->transform(function ($supervisor) use ($last30Days, $today) {
                // Add a date range attribute
                $supervisor->date_range = $last30Days->toDateString() . ' to ' . $today->toDateString();

                 $supervisor->total_driver_difference = $supervisor->total_driver_difference ?? 0;
                $supervisor->total_driver_receipts = $supervisor->total_driver_receipts ?? 0;

                $supervisor->total_receipt = number_format($supervisor->total_driver_receipts + $supervisor->total_driver_difference, 2, '.', '');

                $supervisor->total_paid = number_format($supervisor->driver_diff_paid, 2, '.', '') ?? 0;
                $supervisor->total_remaining = number_format($supervisor->total_receipt - $supervisor->total_paid, 2, '.', '');
                return $supervisor;
            });

        $total_drivers_collected = Driver::withSum([
            'coordinatorReportFieldValues as cash_collected_by_driver' => function ($q) use ($last30Days, $today) {
                $q->where('field_id', 7)
                    ->whereHas('coordinatorReport', function ($query) use ($last30Days, $today) {
                        $query->whereBetween('report_date', [$last30Days, $today]);
                    });
            }
        ], 'value')
            ->withSum([
                'driverReceipts as total_driver_receipts' => function ($q) use ($last30Days, $today) {
                    $q->whereBetween('receipt_date', [$last30Days, $today]);
                }
            ], 'amount_received')
            ->withSum([
                'driverDifferences as driver_diff_paid' => function ($q) use ($last30Days, $today) {
                    $q->whereBetween('receipt_date', [$last30Days, $today]);
                }
            ], 'total_paid')
            ->get()
            ->transform(function ($driver) use ($last30Days, $today) { // <--- add use here
                $collected = number_format($driver->cash_collected_by_driver ?? 0, 2, '.', '');
                $receipts = number_format($driver->total_driver_receipts ?? 0, 2, '.', '');
                $driver_paid = number_format($driver->driver_diff_paid ?? 0, 2, '.', '');

                $driver->total_receipt = $collected;
                $driver->total_paid = number_format($driver_paid + $receipts, 2, '.', '');
                $driver->total_remaining = number_format($driver->total_receipt - $driver->total_paid, 2, '.', '');

                $driver->date_range = $last30Days->toDateString() . ' to ' . $today->toDateString();

                return $driver;
            });

        $data = [
            'all_superviser_collected' => $all_superviser_collected,
            'total_superviser_collected' => $total_superviser_collected,
            'total_drivers_collected' => $total_drivers_collected,
        ];

        $mailUsers = User::whereIn('role_id', [4, 8, 9])->pluck('email');

        foreach ($mailUsers as $email) {
            Mail::to($email)->queue(new MonthlyDifferenceReportMail($data));
        }

        return "Emails added to queue!";

    }
}
