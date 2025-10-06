<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DriverStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\{CreateOrderRequest, UpdateOrderRequest};
use App\Models\{Driver, DriverAttendance, Order};
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Fetch the authenticated driver
            $driver = Driver::with('orders', 'driver_attendance')->find($request->user()->id);

            // Get the filter for status from the request (optional)
            $status = $request->query('status'); // e.g., 'Pickup', 'Drop', 'Cancel'

            // Get the date filters from the request (optional)
            $startDate = $request->query('start_date'); // e.g., '2024-09-01'
            $endDate = $request->query('end_date'); // e.g., '2024-09-30'

            // Check if check_in_orders flag is true
            $checkInOrders = $request->query('check_in_orders') === 'true';
            // Start the query to get the orders for the driver
            $query = $driver->orders()->with('business');

            if ($checkInOrders) {

                if (getLastCheckIn()) {
                    // Filter orders by today's attendance
                    $query->where('driver_attendance_id', getLastCheckIn()->id);
                } else {
                    // No attendance record, return empty
                    return sendResponse(200, 'success', 'No orders found for today\'s check-in', []);
                }
            } else {
                // Apply the status filter if provided
                if ($status) {
                    $query->where('status', $status);
                }

                // Apply the date filter if both start_date and end_date are provided
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($startDate)->startOfDay(),
                        Carbon::parse($endDate)->endOfDay()
                    ]);
                } elseif ($startDate) {  // Apply only start_date filter
                    $query->whereDate('created_at', '>=', Carbon::parse($startDate)->startOfDay());
                } elseif ($endDate) {    // Apply only end_date filter
                    $query->whereDate('created_at', '<=', Carbon::parse($endDate)->endOfDay());
                } else {
                    // If no dates are provided, apply the default filter for the last 7 days
                    $query->whereBetween('created_at', [
                        Carbon::now()->subDays(7)->startOfDay(),
                        Carbon::now()->endOfDay()
                    ]);
                }
            }

            // Get the filtered orders
            $orders = $query->orderBy('id', 'desc')->get();

            return sendResponse(200, 'success', 'Orders fetched successfully', $orders);
        } catch (\Exception $e) {
            return sendResponse(500, 'error', 'Something went wrong', $e->getMessage());
        }
    }

    public function create(CreateOrderRequest $request){
        try{
            $driver = $request->user();
            $validated = $request->validated();
            $validated['pickup_time'] = now();
            $validated['status'] = OrderStatus::Pickup->value;
            $currentCheckIn = DriverAttendance::where('driver_id', $driver->id)->orderBy('id', 'desc')
            ->first();
            $validated['driver_attendance_id'] = $currentCheckIn->id;
            $order = $driver->orders()->create($validated);
            $driver->status = DriverStatus::Busy->value;
            $driver->update();
            return sendResponse(200, 'success', 'Order Created Successfull', $order);

            // Check If Driver Previously Checkout
        }catch(\Exception $e){
            return sendResponse(500, 'error', 'something went wrong', $e->getMessage());
        }
    }

    public function update(UpdateOrderRequest $request, Order $order){
        try{
            $validated = $request->validated();
            $driver = $request->user();

            if($order->status !== OrderStatus::Pickup->value){
                return sendResponse(400, 'error', 'can not update this order status');
            }

            if($validated['status'] === OrderStatus::Cancel->value){
                $validated['cancelled_time'] = now();
            }elseif($validated['status'] === OrderStatus::Drop->value){
                $validated['drop_time'] = now();
                $validated['type'] = $request->has('type') ? $request->type : 0;
                $validated['amount_paid'] = $request->has('amount_paid') ? $request->amount_paid : 0;
                $validated['amount_received'] = $request->has('amount_received') ? $request->amount_received : 0;
            }



            $order->update($validated);

            $order = $order->fresh();


            // Change Status to Active
            $orders = Order::where('driver_id', $driver->id)->where('status', OrderStatus::Pickup->value)->count();
            if($orders <= 0){
                $driver->status = DriverStatus::Active->value;
                $driver->update();
            }

            return sendResponse(200, 'success', 'Order Status Updated Successfull', $order);

        }catch(\Exception $e){
            return sendResponse(500, 'error', 'something went wrong', $e->getMessage());
        }
    }
}
