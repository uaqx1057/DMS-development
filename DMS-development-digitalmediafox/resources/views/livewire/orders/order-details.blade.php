<div>
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Order Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">Order</a></li>
                        <li class="breadcrumb-item active">Order Management</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="card">
           <div class="card-header d-flex justify-content-between align-items-center">
    <!-- Left Side: Title -->
    <h4 class="card-title mb-0">Orders <strong>{{ $branch_name }}</strong></h4>

    <!-- Right Side: Filters in One Line -->
    <div class="d-flex gap-2">
        <!-- Status Filter -->
        <select wire:change="StatusFilter($event.target.value)" model="status" class="form-select form-select-sm" >
            <option value="">All Status</option>
            <option value="Pickup">Out for Delivery</option>
            <option value="Drop">Delivered</option>
            <option value="Cancel">Cancelled</option>
        </select>

        <!-- Restaurant Filter -->
        <select wire:change="RestaurantFilter($event.target.value)" model='restaurant' class="form-select form-select-sm">
            <option value="">All Restaurants</option>
            @foreach($restaurants as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>

        <!-- Payment Filter -->
        <select wire:change="PaymentFilter($event.target.value)" model="payment" class="form-select form-select-sm">
            <option value="">All Payments</option>
            <option value="0">Wallet</option>
            <option value="1">Cash</option>
            <option value="2">Online</option>
        </select>

        <!-- Date Filter -->
        <input type="date" wire:model="date" wire:change="DateFilter($event.target.value)" class="form-control form-control-sm">

        <button title="Export CSV"  wire:click="exportExpensesCsv">
        <i class="ri-file-download-line text-success h2"></i>
    </button>
    
    </div>
</div>



            <!-- Table Body -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>SL. No</th>
                                <th>Restaurant</th>
                                <th>Driver Name</th>
                                <th>Order ID</th>
                                <th>Order Status</th>
                                <th>Date/Time</th>
                                <th>Payment Mode</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                                    <td>{{ $order->business->name ?? 'N/A' }}</td>
                                    <td>{{ $order->driver->name ?? 'N/A' }}</td>
                                    <td>{{ $order->order_id }}</td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'Pickup' => ['Out for Delivery', 'warning text-dark'],
                                                'Drop'   => ['Delivered', 'success'],
                                                'Cancel' => ['Cancelled', 'danger'],
                                            ];
                                            $statusLabel = $statusMap[$order->status][0] ?? 'Ready for Pickup';
                                            $badgeClass = $statusMap[$order->status][1] ?? 'info';
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">{{ $statusLabel }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}</td>
                                    <td>
                                  @switch($order->type)
                                    @case(1)
                                    Cash
                                    @break
                                    @case(2)
                                    Online
                                    @break
                                    @default
                                    
                                    @endswitch


                                    </td>
                                    <td>{{ number_format($order->amount_received, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
   
</div>
