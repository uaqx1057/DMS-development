<div>
    @if ($error)
        <div class="container d-flex flex-column justify-content-center align-items-center text-center" style="height: 100vh;">
            <div class="alert alert-danger d-flex align-items-center justify-content-center mb-3" role="alert" style="min-width: 300px;">
                {{ $error }}
            </div>
            <a class="btn btn-sm btn-secondary" href="{{ route('driver.dashboard') }}">Dismiss</a>
        </div>
    @else
        <style>
            html, body { height: 100%; overscroll-behavior-y: contain; }
            .nav-tabs .nav-link.active {
            background-color: #ffffff;
            color: #3577f1;
            }
            .bg-gray{background-color:rgb(221 221 221);}
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            .rotate-icon.rotating { animation: spin 1s linear infinite; }
            .br-none { border-radius: 5px !important; }
            .nav-link { color: #717171; }
            .nav-tabs { border-radius: 10px; }
            .bg-light1{background-color:#299cdb1c;}
            .bg-light2{background-color:#0fd50f29;}
            .ao-color{color:#3577f1;}
            .bb{border-bottom: 1px solid black;}
             .rotate {
        display: inline-block;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .reload-btn{padding:2px 7px; font-size:20px;border-radius:30px;}
        </style>

        <div class="mobile-wrapper">
            <x-layouts.driverheader />

            <div class="container bodySection">

                {{-- Tabs --}}
                <ul class="nav nav-tabs nav-fill mb-4 bg-gray p-1" id="orderTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button 
                        wire:click="PUO" 
                        class="nav-link br-none fw-bold {{ $activeTab === 'pickup' ? 'active' : '' }}" 
                        id="pickup-tab" 
                        type="button" 
                        aria-controls="pickup" 
                        aria-selected="{{ $activeTab === 'pickup' ? 'true' : 'false' }}">
                        Pick Up Orders
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button 
                        wire:click="MyOrders" 
                        class="nav-link br-none fw-bold {{ $activeTab === 'myorders' ? 'active' : '' }}" 
                        id="myorders-tab" 
                        type="button" 
                        aria-controls="myorders" 
                        aria-selected="{{ $activeTab === 'myorders' ? 'true' : 'false' }}">
                        My Orders
                    </button>
                </li>
            </ul>




<div wire:ignore.self class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <!--<div class="modal-header bg-light bb">-->
      <!--  <h5 class="modal-title fw-bold text-dark">Payment Method</h5>-->
      <!--  <button type="button" class="btn-close px-4" data-bs-dismiss="modal" aria-label="Close"></button>-->
      <!--</div>-->

    <div class="modal-body">
  <div class="mb-3">
    <label class="form-label d-block">Payment Method</label>
    
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" id="cash" name="payMethdo" value="1"
               wire:change="selectMethod($event.target.value)">
        <label class="form-check-label" for="cash">Cash</label>
    </div>

    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" id="online" name="payMethdo" value="2"
               wire:change="selectMethod($event.target.value)">
        <label class="form-check-label" for="online">Online</label>
    </div>

    @error('paymentMethod') 
        <small class="text-danger d-block">{{ $message }}</small> 
    @enderror
</div>

    {{-- Show only if Cash is selected --}}
    @if($paymentMethod == 1)
        <div class="mb-3">
            <label class="form-label">Cash Amount <span class="text-danger">*</span></label>
            <input type="number" wire:model="cashAmount" class="form-control" placeholder="Enter received amount">
            @error('cashAmount') 
                <small class="text-danger">{{ $message }}</small> 
            @enderror
        </div>
    @endif
</div>


      <div class="modal-footer">
         <button type="button" class="btn btn-sm btn-success" wire:click="confirmDeliver">Confirm Deliver</button>
        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>




                {{-- Tab Content --}}
                <div class="tab-content" id="orderTabsContent">
                    
                    
                    @if($picUpOrderTab)
                     @if(!$selectedBusiness)

                    {{-- Pick Up Orders --}}
                    <div class="tab-pane fade show active" id="pickup" role="tabpanel" aria-labelledby="pickup-tab">
                        <h3 class="mb-3 fw-bold">Select Restaurant</h3>

                        {{-- Restaurant Cards --}}
                        @foreach($businesses as $restaurant)
                            <div class="card mb-3 shadow-sm" wire:click="selectBusiness({{ $restaurant->id }})">
                                <div class="card-body d-flex align-items-center gap-3">
                                    @if($restaurant->image)
                                        <div class="rounded-circle overflow-hidden" style="width: 50px; height: 50px;">
                                              <img src="{{ asset('storage/app/public/' . $restaurant->image) }}" 
                                                 alt="{{ $restaurant->name }}"
                                                 class="w-100 h-100 object-fit-cover">
                                        </div>
                                    @else
                                        <div class="bg-primary text-white fw-bold rounded-circle text-center d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            {{ strtoupper(substr($restaurant->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $restaurant->name }}</h6>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                    @if($selectedBusiness)
                    <div class="card">
                        <div class="card-body">
                            <h2 class="fw-bold mb-2">{{ strtoupper($selectedBusiness->name) }}</h3>
                            <a href="order" class="text-primary"><i class="ri-arrow-left-line"></i> Back to restaurants</a>
                        </div>
                        
                    </div>
                    
                    @if(!$picOrderStatus)
                    <div>
                        <div class="card">
                            <div class="card-body">
                                <div class="card-heading text-center mb-4">
                                     <h1 class="fw-bold">Order Number</h1>
                                </div>
                                
                                
                            <div class="mb-2">
                                <label class="form-label">Enter order number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="orderNumber" >
                                 @error('orderNumber') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                            <button wire:click="submitPickup" class="btn btn-primary  w-100">Pickup</button>
                            </div>     
                                    
                            <div class="d-flex justify-content-center mt-3">
                           <a href="order">Cancel</a>
                            </div>           
                                
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    
                    
                    
                    
                    
                    


  <style>
    .order-card {
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      margin-bottom: 1rem;
      padding: 1rem;
    }
    .progress-step {
      display: flex;
      justify-content: space-between;
      margin-top: 1rem;
      position: relative;
    }
    .progress-step .step {
      text-align: center;
      width: 33.33%;
      position: relative;
    }
    .progress-step .step::before {
      content: "";
      position: absolute;
      top: 15px;
      left: 50%;
      width: 100%;
      height: 3px;
      background-color: #dee2e6;
      z-index: -1;
    }
    .progress-step .step:first-child::before {
      left: 50%;
      width: 50%;
    }
    .progress-step .step:last-child::before {
      left: 0;
      width: 50%;
    }
    .progress-step .step.completed .circle {
      background-color: #28a745;
      color: white;
    }
    .progress-step .circle {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: inline-flex;
      justify-content: center;
      align-items: center;
      background-color: #dee2e6;
      margin-bottom: 0.5rem;
      z-index: 1;
      position: relative;
    }
  </style>

@if($picOrderStatus)
  <div class="container">

    <h5 class="mb-3 text-success"><i class="ri-checkbox-circle-line"></i> Your Active Orders</h5>

     
    <div class="order-card bg-white">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0 fw-bold">#{{$pickupDetails['orderNumber']}}</h6>
      </div>
     

      <div class="mt-2 mb-2">
        <div class="d-flex justify-content-between align-items-center">
         <small class="text-muted">Assigned at {{$pickupDetails['assign_time']}}</small>
          <span class="badge bg-success">Assigned to you</span>
        </div>
      </div>

      <!--<div class="mb-3 text-muted">-->
      <!--  <i class="ri-map-pin-line"></i> Address to be provided-->
      <!--</div>-->

     
        @php
        $status = $OrderData->status;
        @endphp
    
        
        <div class="progress-step">
            <!-- Step 1: Accepted -->
            <div class="step active completed">
                <div class="circle">
                 <i class="ri-check-line"></i>
                </div>
                <small>Accepted</small>
            </div>
        
            <!-- Step 2: Picked Up -->
            <div class="step {{ $status === 'Pickup' || $status === 'Drop' ? 'completed' : ($status === 'Accepted' ? 'active' : '') }}">
                <div class="circle">
                    @if ($status === 'Pickup' || $status === 'Drop')
                        <i class="ri-check-line"></i>
                    @else
                        2
                    @endif
                </div>
                <small>Picked Up</small>
            </div>
        
            <!-- Step 3: Delivered -->
            <div class="step {{ $status === 'Drop' ? 'completed' : '' }}">
                <div class="circle">
                    @if ($status === 'Drop')
                        <i class="ri-check-line"></i>
                    @else
                        3
                    @endif
                </div>
                <small>Delivered</small>
            </div>
        </div>


      <div class="mt-3 d-flex gap-2">
        @if(!$dilBtn)
        <button class="btn btn-primary flex-grow-1" wire:click="PicupOrder({{ $pickupDetails['order_id'] }})" wire:loading.attr="disabled">
        <span wire:loading wire:target="PicupOrder({{ $pickupDetails['order_id'] }})" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        Pick Up Order
        </button>
        @else
        <button class="btn btn-success flex-grow-1" 
        wire:click="openPaymentModal({{ $pickupDetails['order_id'] }})" 
        wire:loading.attr="disabled">
        <span wire:loading wire:target="openPaymentModal" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        Deliver Order
        </button>
        @endif
    
        
        <button class="btn btn-outline-danger" wire:click="confirmCancel({{ $pickupDetails['order_id'] }})">Cancel</button>
      </div>
      
        @if ($showCancelModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="d-flex justify-content-center align-items-center min-vh-100">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                            <p>Are you sure you want to cancel this order : <strong>#{{$pickupDetails['orderNumber']}}</strong> ?</p>
                        </div>
                        <div class="modal-footer text-center">
                            <button class="btn btn-secondary" wire:click="$set('showCancelModal', false)">No</button>
                            <button class="btn btn-danger" wire:click="cancelOrder">Yes, Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
    <a href="order" class="btn btn-primary w-100 mt-3"><i class="ri-add-line"></i> Add New Order</a>

  </div>
@endif

 @endif
 @endif
 
 
 
 
                    
                    
                    
            @if($myOrderTab) 
            
            {{-- My Orders --}}
            <div class="tab-pane fade {{ $activeTab === 'myorders' ? 'show active' : '' }}" 
                 id="myorders" role="tabpanel" aria-labelledby="myorders-tab">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Left side -->
                <h3 class="mb-0 fw-bold">My Orders</h3>
            
                <!-- Right side -->
                <div class="d-flex">
                    <button wire:click="showFilterModal" class="ms-2 btn border reload-btn">
                        <i class="ri-filter-line text-primary"></i>
                    </button>
                   
                    <button wire:click="refreshOrders" class="ms-2 btn border reload-btn">
                        <i class="ri-refresh-line text-success"
                           wire:loading.class="rotate"
                           wire:target="refreshOrders"></i>
                    </button>
                </div>
            </div>


<!-- Modal -->
@if($showFilter)
<div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-light bb">
                <h5 class="modal-title fw-bold text-dark">Filter Orders</h5>
                <button type="button" class="btn-close px-4" wire:click="$set('showFilter', false)"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" wire:model="filterDate" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select wire:model="filterStatus" class="form-select">
                        <option value="">All</option>
                        <option value="Pickup">In-Transit</option>
                        <option value="Drop">Delivered</option>
                        <option value="Cancel">Cancelled</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Restaurant</label>
                    <select wire:model="filterSearch" class="form-select">
                        <option value="">-- Select Restaurant --</option>
                        @foreach($businesses as $business)
                            <option value="{{ $business->id }}">{{ $business->name }}</option>
                        @endforeach
                    </select>
                    @error('filterSearch') 
                        <small class="text-danger">{{ $message }}</small> 
                    @enderror
                </div>


            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light border-dark" wire:click="resetFilters">Reset</button>
                <button type="button" class="btn btn-primary" wire:click="applyFilters">Apply</button>
            </div>

        </div>
    </div>
</div>
@endif


 
            
            <div class="row text-center mb-3">
                <div class="col-6">
                    <div class="bg-light1 rounded p-3">
                        <h2 class="ao-color mb-1 fw-bold">{{ $activeCount }}</h2>
                        <h2 class="ao-color fw-bold">Active Orders</h2>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-light2 rounded p-3">
                        <h2 class="text-success mb-1 fw-bold">{{ $completedCount }}</h2>
                        <h2 class="text-success fw-bold">Completed Today</h2>
                    </div>
                </div>
            </div>

    <ul class="nav nav-tabs nav-fill mb-4 bg-gray p-1" id="orderTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button 
            wire:click="setTab('all')" 
            class="nav-link br-none fw-bold {{ $ordersTab === 'all' ? 'active' : '' }}" 
            id="all-tab" 
            type="button" 
            aria-controls="all" 
            aria-selected="{{ $ordersTab === 'all' ? 'true' : 'false' }}">
            All ({{ $allCount }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button 
            wire:click="setTab('active')" 
            class="nav-link br-none fw-bold {{ $ordersTab === 'active' ? 'active' : '' }}" 
            id="active-tab" 
            type="button" 
            aria-controls="active" 
            aria-selected="{{ $ordersTab === 'active' ? 'true' : 'false' }}">
            Active ({{ $activeCount }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button 
            wire:click="setTab('completed')" 
            class="nav-link br-none fw-bold {{ $ordersTab === 'completed' ? 'active' : '' }}" 
            id="completed-tab" 
            type="button" 
            aria-controls="completed" 
            aria-selected="{{ $ordersTab === 'completed' ? 'true' : 'false' }}">
            Completed ({{ $completedCount }})
        </button>
    </li>
</ul>

            <div wire:loading wire:target="refreshOrders" class="justify-content-center">
    <div class="text-center">
        <div class="text-primary mb-2" role="status"></div>
        <div>Loading...</div>
    </div>
    </div>



            @if($orders->count() > 0)
               @foreach ($orders as $order)
    <div class="card shadow-lg mb-3 rounded-3 border-0" >
        
        <div class="card-body">
            
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h3 class="mb-1 fw-bold">#{{ $order->order_id ?? $order->id }}</h3>
                    <strong class="text-muted">
                        {{ $order->business_name ?? 'Unknown Business' }}
                    </strong>
                </div>
                <span class="badge rounded-pill
                @switch($order->status)
                    @case('Pickup') bg-warning text-dark @break
                    @case('Drop') bg-success @break
                    @case('Cancel') bg-danger @break
                    @default bg-secondary
                @endswitch">
                
                @switch($order->status)
                    @case('Pickup') Out for Delivery @break
                    @case('Drop') Delivered @break
                    @case('Cancel') Cancelled @break
                    @default Ready for Pickup
                @endswitch
            </span>

            </div>

            {{-- Customer Placeholder (if you add relation, show customer name/address here) --}}
            <!--<div class="mt-2">-->
            <!--    <strong>Customer</strong>-->
            <!--    <p class="mb-1 text-muted small">-->
            <!--        {{ $order->customer->address ?? 'Address to be provided' }}-->
            <!--    </p>-->
            <!--</div>-->

            {{-- Total & Time --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <!--<span>-->
                <!--    Total: -->
                <!--    <strong class="text-dark">-->
                <!--        AED {{ number_format($order->amount_paid ?? 0, 2) }}-->
                <!--    </strong>-->
                <!--</span>-->
                <small class="text-muted">
                    {{ $order->created_at ? $order->created_at->format('d M H:i') : '' }}
                </small>
            </div>

            {{-- Status Timeline --}}
            <div class="d-flex flex-wrap gap-2 align-items-center small text-muted mb-2">
            @if($order->created_at)
            <span class="text-success">● Assigned {{ $order->created_at->format('H:i') }}</span>
            @endif
            @if($order->pickup_time)
            <span class="text-success">● Picked Up {{ \Carbon\Carbon::parse($order->pickup_time)->format('H:i') }}</span>
            @endif
            @if($order->delivered_time)
            <span class="text-success">● Delivered {{ \Carbon\Carbon::parse($order->delivered_time)->format('H:i') }}</span>
            @endif
            @if($order->cancelled_time)
            <span class="text-danger">● Cancelled {{ \Carbon\Carbon::parse($order->cancelled_time)->format('H:i') }}</span>
            @if($order->cancel_reason)
            <span class="fst-italic text-danger">Reason: {{ $order->cancel_reason }}</span>
            @endif
            @endif
        </div>


            {{-- Footer Button --}}
        <div class="text-center d-flex">
            @if($order->status === 'Drop' || $order->status === 'Cancel')
                {{-- Only View button, full width --}}
                <button wire:click="showOrderDetails({{ $order->id }})"
                    class="btn btn-outline-dark btn-sm w-100">
                    View Details
                </button>
            @else
                {{-- View button, half width --}}
                <button wire:click="showOrderDetails({{ $order->id }})"
                    class="btn btn-outline-dark btn-sm w-50">
                    View Details
                </button>
        
                @if($order->status === null)
                    <button class="btn btn-primary btn-sm w-50 flex-grow-1 mx-2"
                        wire:click="PicupOrder({{ $order->id }})"
                        wire:loading.attr="disabled">
                        <span wire:loading wire:target="PicupOrder({{ $order->id }})"
                              class="spinner-border spinner-border-sm me-2"
                              role="status" aria-hidden="true"></span>
                        Pick Up Order
                    </button>
                @elseif($order->status === 'Pickup')
                    <button class="btn btn-success btn-sm w-50 flex-grow-1 mx-2"
                        wire:click="openPaymentModal({{ $order->id }})"
                        wire:loading.attr="disabled">
                        <span wire:loading wire:target="openPaymentModal({{ $order->id }})"
                              class="spinner-border spinner-border-sm me-2"
                              role="status" aria-hidden="true"></span>
                        Deliver Order
                    </button>
                
                @endif
            @endif
        </div>


            

        </div>
    </div>
    
    <div>
    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3">
          <div class="modal-header bg-light bb">
            <h5 class="modal-title text-dark" id="orderDetailsLabel">Order Details</h5>
            <button type="button" class="btn-close px-4" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
          <div class="modal-body">
            @if($ViewData)
              <!-- Order Info -->
              <h6 class="fw-bold mb-2">Order Information</h6>
                 <div class="p-3 rounded bg-light mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <strong class="text-muted">Order Number:</strong>
                        <span class="text-dark">#{{ $ViewData->id }}</span>
                    </div>
                
                    <div class="d-flex justify-content-between mb-2">
                        <strong class="text-muted">Status:</strong>
                        @php
                        [$statusLabel, $statusClass] = match($ViewData->status) {
                        null     => ['Ready for Pickup', 'secondary'],
                        'Pickup' => ['Out for Delivery', 'warning  text-dark'],
                        'Drop'   => ['Delivered', 'success'],
                        'Cancel' => ['Cancel', 'danger'],
                        default  => ['Unknown', 'secondary'],
                        };
                        @endphp
                        
                        <span class="badge bg-{{ $statusClass }} rounded-pill">{{ $statusLabel }}</span>

                    </div>
                
                    <!--<div class="d-flex justify-content-between mb-2">-->
                    <!--    <strong class="text-muted">Total:</strong>-->
                    <!--    <span class="text-dark">AED {{ number_format($ViewData->total, 2) }}</span>-->
                    <!--</div>-->
                
                    <div class="d-flex justify-content-between mb-2">
                        <strong class="text-muted">Payment Method:</strong>
                        <span class="text-dark fw-bold">
                        {{ match($ViewData->type) {
                        1 => 'Cash',
                        2 => 'Online',
                        0 => 'Wallet',
                        null => 'Not Found',
                        default => 'Not Found'
                        } }}
                        </span>

                    </div>
                    
                    
                @if($ViewData->type == 1)
                
                <div class="d-flex justify-content-between mb-2">
                <strong class="text-muted">Amount</strong>
                <span class="text-dark fw-bold">
                {{ $ViewData->amount_received }}
                </span>
                
                </div>
                @endif
                
               
            </div>

             <!-- Restaurant -->
              <h6 class="fw-bold mb-2">Restaurant</h6>
              <div class="p-3 rounded bg-light">
                <p class="mb-1 fw-semibold">{{ $ViewData->business->name ?? 'N/A' }}</p>
                <small>{{ $ViewData->restaurant->address ?? '' }}</small>
              </div>
                    
                
              <!-- Customer -->
              <!--<h6 class="fw-bold mb-2">Customer</h6>-->
              <!--<div class="p-3 rounded bg-light">-->
              <!--  <p class="mb-1 fw-semibold">{{ $ViewData->customer->name ?? 'Customer' }}</p>-->
              <!--  <small>{{ $ViewData->customer->address ?? 'Address to be provided' }}</small>-->
              <!--</div>-->
            @else
              <p class="text-muted">Loading order details...</p>
            @endif
          </div>
          
          <div class="modal-footer justify-content-start">
            <button type="button" class="btn btn-light border-dark" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
</div>
@endforeach

            @else
                <div class="text-center p-4 bg-light rounded">
                    <div class="mb-2">
                        <i class="bi bi-file-earmark-text" style="font-size: 2rem; color: #bbb;"></i>
                    </div>
                    <h6 class="mb-1">No Orders Yet</h6>
                    <small class="text-muted">No orders have been assigned to you today</small>
                </div>
            @endif
        </div>
    </div>
</div>




@endif

                </div>
            </div>

            <x-layouts.driverfooter />
        </div>

        <script>
            document.querySelectorAll('.refresh-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const icon = this.querySelector('.rotate-icon');
                    icon.classList.add('rotating');
                    setTimeout(() => icon.classList.remove('rotating'), 1000);
                });
            });
             window.addEventListener('show-order-modal', () => {
        var myModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        myModal.show();
    });
    

    window.addEventListener('show-payment-modal', () => {
        new bootstrap.Modal(document.getElementById('paymentModal')).show();
    });

    window.addEventListener('hide-payment-modal', () => {
        const modalEl = document.getElementById('paymentModal');
        bootstrap.Modal.getInstance(modalEl).hide();
    });
        </script>
        
    @endif
</div>
