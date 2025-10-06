<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;

class OrderDetails extends Component
{
    use WithPagination;

    public $status, $restaurant, $payment, $date, $branch_id,$branch_name;

    protected $paginationTheme = 'bootstrap';

    /**
     * Mount component and set default branch_id
     */
    public function mount()
    {
        if(auth()->user()->role_id!=1){
            $this->branch_id = auth()->user()->branch_id;
        }
        
        if($this->branch_id)
        {
            $bb=Branch::where('id',$this->branch_id)->first();
            $this->branch_name= ' : '.$bb->name;
        }
      
    }

    /**
     * Reset all filters.
     */
    public function resetFilter()
    {
        $this->status = '';
        $this->restaurant = '';
        $this->payment = '';
        $this->date = '';
    }

    /**
     * Filter setters
     */
    public function StatusFilter($status) { $this->status = $status; }
    public function RestaurantFilter($restaurant) { $this->restaurant = $restaurant; }
    public function PaymentFilter($payment) { $this->payment = $payment; }
    public function DateFilter($date) { $this->date = $date; }

    /**
     * Build the filtered query.
     */
    protected function filteredQuery()
{
    $query = Order::with(['business', 'driver'])
        ->orderBy('orders.created_at', 'desc'); // prefix here too

    // Apply branch filter through driver
    if ($this->branch_id) {
        $query->whereHas('driver', function ($q) {
            $q->where('branch_id', $this->branch_id);
        });

    }

    if ($this->status) {
        $query->where('orders.status', $this->status); // prefix with table
    }

    if ($this->restaurant) {
        $query->where('orders.business_id', $this->restaurant);
    }

    if ($this->payment !== null && $this->payment !== '') {
        $query->where('orders.type', $this->payment);
    }

    if ($this->date) {
        $query->whereDate('orders.created_at', $this->date);
    }

    return $query;
}


    /**
     * Render Livewire view with paginated orders and restaurants.
     */
    public function render()
    {
        $orders = $this->filteredQuery()->paginate(10);
        if($this->branch_id)
        {
            $restaurants = Business::where('branch_id',$this->branch_id)->pluck('name', 'id');
        }else
        {
            $restaurants = Business::pluck('name', 'id');
        }
        

        return view('livewire.orders.order-details', [
            'orders' => $orders,
            'restaurants' => $restaurants,
            'branch_name' => $this->branch_name,
        ]);
    }

    /**
     * Export filtered orders as CSV
     */
    public function exportExpensesCsv()
    {
        $filename = 'orders_' . now()->format('d-m-Y') . '.csv';

        $columns = [
            'business_name' => 'Restaurant',
            'driver_name'   => 'Driver',
            'order_id'      => 'Order ID',
            'pickup_time'   => 'Pickup Time',
            'delivered_time'=> 'Delivered Time',
            'cancelled_time'=> 'Cancelled Time',
            'status_label'  => 'Status',
            'type_label'    => 'Payment Type',
            'amount_received'=> 'Amount Received',
            'created_at'    => 'Order Date',
        ];

        // Use the same filters as filteredQuery
        $orders = $this->filteredQuery()
            ->leftJoin('drivers', 'orders.driver_id', '=', 'drivers.id')
            ->leftJoin('businesses', 'orders.business_id', '=', 'businesses.id')
            ->select(
                'businesses.name as business_name',
                'drivers.name as driver_name',
                'orders.order_id',
                'orders.pickup_time',
                'orders.delivered_time',
                'orders.cancelled_time',
                'orders.status',
                'orders.type',
                'orders.amount_received',
                'orders.created_at'
            )
            ->orderBy('orders.id', 'asc')
            ->get()
            ->map(function ($order) {
                // Map type to human-readable labels
                $order->type_label = match ($order->type) {
                    0 => 'Wallet',
                    1 => 'Cash',
                    2 => 'Online',
                    default => '',
                };

                // Map status to human-readable labels
                $order->status_label = match ($order->status) {
                    'Drop' => 'Delivered',
                    '' => 'Pending',
                    'Cancel' => 'Cancelled',
                    'Pickup' => 'Picked Up',
                    default => $order->status,
                };

                return $order;
            });

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array_values($columns));

            foreach ($orders as $row) {
                $csvRow = [];
                foreach (array_keys($columns) as $key) {
                    $csvRow[] = $row->{$key} ?? '';
                }
                fputcsv($file, $csvRow);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
        ]);
    }
}
