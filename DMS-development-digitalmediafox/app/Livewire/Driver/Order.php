<?php

namespace App\Livewire\Driver;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\DriverAttendance;
use Carbon\Carbon;
use App\Models\Business;
use App\Models\Order as OD;

#[Layout('components.layouts.plain')] // Or 'layouts.plain' if stored under views/layouts
class Order extends Component
{
    public $error = null;
    public $orderNumber;
    public $selectedBusiness = null;
    public $picOrderStatus = false;
    public $pickupDetails = []; 
    public $showCancelModal = false;
    public $orderToCancelId = null;
    public $afterCancleOrder = false;
    public $dilBtn = false;
    public $orderID;
    public $OrderData = [];
    public $myOrderTab = false;
    public $picUpOrderTab = true;
    public $orderFilter = 'all'; // all, Pickup, Drop
    public $allOrders = []; // Unfiltered collection
    public string $activeTab = 'pickup';
    public string $ordersTab = 'all'; // all, active, completed
    public $orders = [];
    public $activeCount;
    public $completedCount;
    public $allCount;
    public $ViewData;
    public $paymentMethod='';
    public $cashAmount;
    
  public function showOrderDetails($orderId)
{
    $this->orderId = $orderId;

    // eager load relationship 'business'
    $this->ViewData = OD::with('business')->find($orderId);

    $this->dispatch('show-order-modal');
}

    public function selectMethod($method)
    {
        $this->paymentMethod=$method;
    }
    public function MyOrders()
    {
        $this->activeTab = 'myorders';
        $this->myOrderTab=true;
        $this->picUpOrderTab=false;
        $this->loadOrders();
    }
    
    public function PUO()
    {
        $this->activeTab = 'pickup';
        $this->picUpOrderTab=true;
        $this->myOrderTab=false;
        $this->selectedBusiness=null;
        $this->loadOrders();
    }

    public function OrderDetails()
    {
      $this->OrderData = OD::find($this->orderID);

    }
    
    public function deliverOrder($orderId)
    {
        $this->orderID=$orderId;
        $order = OD::find($orderId);
        
        if ($order) {
        $order->status = 'Drop';
        $order->delivered_time = now();
        $order->save();
        $this->showCancelModal = false;
        $this->orderToCancelId = null;
        $this->picOrderStatus=false;
        $this->orderNumber='';
        $this->dilBtn=false; 
         $this->loadOrders();
        
        session()->flash('message', "Order #{$order->order_id} has been Deliver.");
        }
         
    }
    
    
    public function openPaymentModal($orderId)
    {
        $this->reset(['paymentMethod', 'cashAmount']);
        $this->orderID = $orderId;
    
        $this->dispatch('show-payment-modal');
    }

    public function confirmDeliver()
    {
        
         if ($this->paymentMethod == '') {
            $this->addError('paymentMethod', 'Please select payment method.');
            return;
        }
        
        if ($this->paymentMethod === '1' && empty($this->cashAmount)) {
            $this->addError('cashAmount', 'Please enter the cash amount.');
            return;
        }
    
        // Call your existing deliverOrder
        $this->deliverOrder($this->orderID);
    
        // Save payment method (optional if you have these columns)
        OD::where('id', $this->orderID)->update([
            'type' => $this->paymentMethod,
            'amount_received'  => $this->paymentMethod === '1' ? $this->cashAmount : 0.00,
        ]);
    
         $this->paymentMethod='';
        $this->dispatch('hide-payment-modal');
    }
    
    
    
    public function PicupOrder($orderId)
    {
        $this->orderID=$orderId;
        $order = OD::find($orderId);
        
        if ($order) {
        $order->status = 'Pickup';
        $order->pickup_time = now();
        $order->save();
        
        session()->flash('message', "Order #{$order->order_id} has been Pickup.");
        }
        $this->dilBtn=true;   
        $this->loadOrders();
    }
    
    
    public function confirmCancel($orderId)
    {
        $this->orderToCancelId = $orderId;
        $this->showCancelModal = true;
        
        
        
    }
    
    public function cancelOrder()
    {
        $order = OD::find($this->orderToCancelId);
    
        if ($order) {
            $order->status = 'Cancel';
            $order->cancelled_time = now();
            $order->save();
    
            session()->flash('message', "Order #{$order->order_id} has been cancelled.");
        }
    
        $this->showCancelModal = false;
        $this->orderToCancelId = null;
        $this->picOrderStatus=false;
        $this->orderNumber='';
        $this->dilBtn=false; 
        $this->loadOrders();
        
    }
    
    
public function loadOrders()
{
    $driverId = auth('driver')->id();

    // --- Counts ---
    $this->activeCount = OD::where('driver_id', $driverId)
        ->where(function ($q) {
            $q->whereNull('status')
              ->orWhere('status', 'Pickup');
        })
        ->count();

    $this->completedCount = OD::where('driver_id', $driverId)
        ->where('status', 'Drop')
        ->count();

    $this->allCount = OD::where('driver_id', $driverId)
        ->where(function ($q) {
            $q->whereNull('status')
              ->orWhereIn('status', ['Pickup', 'Drop', 'Cancel']);
        })
        ->count();

    // --- Orders List ---
    $query = OD::with('business:id,name,image')
        ->where('driver_id', $driverId)
        ->orderBy('id', 'desc');

    if ($this->orderFilter === 'active') {
        $query->where(function ($q) {
            $q->whereNull('status')
              ->orWhere('status', 'Pickup');
        });
    } elseif ($this->orderFilter === 'completed') {
        $query->where('status', 'Drop');
    } elseif ($this->orderFilter === 'all') {
        $query->where(function ($q) {
            $q->whereNull('status')
              ->orWhereIn('status', ['Pickup', 'Drop', 'Cancel']);
        });
    }

    // Map orders for UI
    $this->orders = $query->get()->map(fn ($order) => (object) [
        'id'              => $order->id,
        'status'          => $order->status,
        'title'           => '#' . $order->id,
        'business_id'     => $order->business_id,
        'business_name'   => optional($order->business)->name,
        'pickup_time'     => $order->pickup_time,
        'delivered_time'  => $order->delivered_time,
        'amount_paid'     => $order->amount_paid,
        'amount_received' => $order->amount_received,
        'created_at'      => $order->created_at,
        'cancelled_time'  => $order->cancelled_time,
        'cancel_reason'   => $order->cancel_reason,
    ]);
}







    
    public function refreshOrders()
    {
        $this->loadOrders(); // re-fetch data
    }
    
     public function setTab($tab)
    {
        $this->ordersTab = $tab;
       $this->orderFilter=$tab;
       $this->loadOrders(); 
    }

    public function mount()
    {
        $this->loadOrders();
        $driver = Auth::guard('driver')->user();

        if (!$driver) {
            return redirect()->route('driver.login');
        }

        // Check if driver is checked in today
        $attendance = DriverAttendance::where('driver_id', $driver->id)
            ->whereDate('checkin_time', Carbon::today())
            ->whereNull('checkout_time')
            ->first();

        if (!$attendance) {
            $this->error = "You need to check in first to pick up an order.";
        }
    }

    public function render()
    {
        $activeOrders = collect($this->orders)->whereIn('status', [null, 'Pickup']);
        $completedOrders = collect($this->orders)->where('status', 'Drop');


        $filtered = match ($this->ordersTab) {
            'active' => $activeOrders,
            'completed' => $completedOrders,
            default => collect($this->orders),
        };
        
        $this->OrderDetails();
        return view('livewire.driver.order', [
            'menu' =>'order',
            'error' => $this->error,
            'businesses' => Business::select('id', 'name', 'image')->where('branch_id',auth()->user()->branch_id)->get(),
            'ordersList' => $filtered,
        ]);
    }
    
    

    

    public function selectBusiness($businessId)
    {
        $this->selectedBusiness = Business::find($businessId);
    }

    public function submitPickup()
    {
        $this->validate([
            'orderNumber' => 'required|string',
        ]);

        if (!$this->selectedBusiness) {
            $this->addError('selectedBusiness', 'Please select a business.');
            return;
        }

        $driver = Auth::guard('driver')->user();

        // Get current active attendance
        $attendance = DriverAttendance::where('driver_id', $driver->id)
            ->whereNull('checkout_time')
            ->first();

        if (!$attendance) {
            $this->error = "You must check in before picking up an order.";
            return;
        }

        // Create new order
        $order=OD::create([
            'driver_id' => $driver->id,
            'business_id' => $this->selectedBusiness->id,
            'order_id' => $this->orderNumber,
            'pickup_time' => null,
            'delivered_time' => null,
            'cancelled_time' => null,
            'drop_time' => null,
            'cancel_reason' => null,
            'status' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'driver_attendance_id' => $attendance->id,
            'type' => null,
            'amount_paid' => 0,
            'amount_received' => 0,
        ]);

        $orderId = $order->id;
        session()->flash('message', 'Order successfully added.');

        // Clear input
        $this->picOrderStatus = true;
        $this->pickupDetails = [
        'order_id'=>$orderId,
        'orderNumber' => $this->orderNumber,
        'business_name' => $this->selectedBusiness->name,
        'assign_time' => now()->format('H:i'), // 24-hour format
        ];
        
        $this->orderID=$orderId;
        $this->OrderDetails();
    }
    
    
    //Filter Logic
    public $showFilter = false;

public $filterDate, $filterStatus, $filterSearch;

public function showFilterModal()
{
    $this->showFilter = true;
}

public function applyFilters()
{
    $query = OD::with('business')
        ->where('driver_id', auth()->id())
        ->orderBy('id', 'desc');

    if ($this->filterDate) {
        $query->whereDate('created_at', $this->filterDate);
    }

    if ($this->filterStatus) {
        $query->where('status', $this->filterStatus);
    }

    if ($this->filterSearch) {
       $query->where('business_id', $this->filterSearch);
    }

    $this->orders = $query->get()->map(fn ($order) => (object) [
        'id'              => $order->id,
        'status'          => $order->status,
        'title'           => '#' . $order->id,
        'business_id'     => $order->business_id,
        'business_name'   => optional($order->business)->name,
        'pickup_time'     => $order->pickup_time,
        'delivered_time'  => $order->delivered_time,
        'amount_paid'     => $order->amount_paid,
        'amount_received' => $order->amount_received,
        'created_at'      => $order->created_at,
        'cancelled_time'  => $order->cancelled_time,
        'cancel_reason'   => $order->cancel_reason,
    ]);
    $this->showFilter = false; // hide modal after applying
}

public function resetFilters()
{
    $this->filterDate = null;
    $this->filterStatus = null;
    $this->filterSearch = '';
    $this->refreshOrders();
    
}

    
    
    
    
    
    
    
    
    
    
    
}
