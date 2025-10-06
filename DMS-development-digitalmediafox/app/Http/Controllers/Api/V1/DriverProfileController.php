<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Driver;
use App\Models\DriverAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DriverProfileController extends Controller
{
    public function show(Request $request){
        try{
            $driver = Driver::with('branch')->find($request->user()->id);
            return sendResponse(200, 'success', 'Profile Fetcehd Successfully!', $driver);

            // Check If Driver Previously Checkout
        }catch(\Exception $e){
            return sendResponse(500, 'error', 'something went wrong', $e->getMessage());
        }
    }

    public function getBusinesses(){
        try{
            $only_driver_businesses = request()->has('only_driver_businesses') && request()->only_driver_businesses === 'true';
            $driver = Driver::with('businesses')->find(request()->user()->id);
            $businesses = $only_driver_businesses ? $driver->businesses : Business::all();
            return sendResponse(200, 'success', 'Driver Businesses Fetcehd Successfully!', $businesses);
            // Check If Driver Previously Checkout
        }catch(\Exception $e){
            return sendResponse(500, 'error', 'something went wrong', $e->getMessage());
        }
    }

    public function getStats(Request $request){

        // Total Users
        // Total Rides
        // Order Progress

        try {
            // Get the authenticated driver
            $driver = $request->user();

            // By default, get today's orders
            $today = Carbon::today();
            $query = $driver->orders()->whereDate('created_at', $today);

            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $currentMonthOrders = $driver->orders()
            ->whereMonth('created_at', $currentMonth)->where('status', OrderStatus::Drop->value)->count();

            // Get filter parameters from the query
            $day = $request->query('day');         // Optional day filter (Y-m-d)
            $month = $request->query('month');     // Optional month filter (Y-m)
            $year = $request->query('year');       // Optional year filter (Y)
            $startDate = $request->query('start'); // Optional custom start date (Y-m-d)
            $endDate = $request->query('end');     // Optional custom end date (Y-m-d)

            // Filter by day
            if ($day) {
                $query->whereDate('created_at', $day);
            }

            // Filter by month
            if ($month) {
                $query->whereMonth('created_at', Carbon::parse($month)->month)
                      ->whereYear('created_at', Carbon::parse($month)->year);
            }

            // Filter by year
            if ($year) {
                $query->whereYear('created_at', $year);
            }

            // Filter by custom date range
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            }

            // Get total orders for the selected time period
            $totalOrders = $query->count();

            // Total delivered orders for the selected period
            $totalDeliveredOrders = $query->where('status', OrderStatus::Drop->value)->count();

            // Total pickup orders for the selected period
            $totalPickupOrders = $query->where('status', OrderStatus::Pickup->value)->count();

            // Calculate the percentage of delivered orders (if total orders > 0)
            $percentageDelivered = $totalOrders > 0 ? ($totalDeliveredOrders / $totalOrders) * 100 : 0;

            // Prepare the response data
            $response = [
                'total_orders' => $totalOrders,
                'total_delivered_orders' => $totalDeliveredOrders,
                'total_pickup_orders' => $totalPickupOrders,
                'percentage_delivered' => round($percentageDelivered, 2), // Rounded to 2 decimal places
                'monthly_orders' => $currentMonthOrders,
                'filters' => [
                    'day' => $day,
                    'month' => $month,
                    'year' => $year,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ];

            return sendResponse(200, 'success', 'Order Stats Fetched Successfully!', $response);

        } catch (\Exception $e) {
            return sendResponse(500, 'error', 'Something went wrong', $e->getMessage());
        }

    }

    private function calculateTotalHours($attendances){
        // Initialize total hours
        $totalHours = 0;

        // Loop through the attendance records and calculate the difference
        foreach ($attendances as $attendance) {
            $checkinTime = Carbon::parse($attendance->checkin_time);
            $checkoutTime = Carbon::parse($attendance->checkout_time);

            // Calculate the difference in hours between checkin and checkout
            $hoursDifference = $checkinTime->diffInHours($checkoutTime);

            // Add the difference to the total hours
            $totalHours += $hoursDifference;
        }

        return number_format($totalHours, 2);
   }

   public function getUniqueOrderBusiness(Request $request){
        try {
            $latestDroppedOrders = getLatestDroppedOrders();
            return sendResponse(200, 'success', 'Latest Uniqe Order\'s Businesses Fetched Successfully!', $latestDroppedOrders);

        } catch (\Exception $e) {
            return sendResponse(500, 'error', 'Something went wrong', $e->getMessage());
        }
   }
}
