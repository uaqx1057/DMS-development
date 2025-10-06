@php
    use Illuminate\Support\Facades\Auth;
    $driver = Auth::guard('driver')->user();
@endphp

<div class="mobile-wrapper">
    <x-layouts.driverheader />

    <style>
        .activeli { color: green; }
        .avatar {
            width: 50px;
            height: 50px;
            color: white;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border:2px solid #732d81;
        }
        .badge-active {
            background-color: #d4edda;
            color: #155724;
            font-size: 12px;
            padding: 2px 8px;
            width: max-content;
            border-radius: 12px;
        }
        .check-in-btn {
            background-color: #00c17c;
            color: white;
            border: none;
            padding: 12px;
            font-weight: 500;
        }
        .restaurant-btn {
            background-color: #2196f3;
            color: white;
            border-radius: 12px;
            padding: 15px;
            font-weight: 500;
        }
    .checkin-section{    background: #ffffff;
    padding: 11px;
    border-radius: 10px;
    margin-bottom: 10px;    
    }    
    </style>

    <div class="container bodySection">
        <!-- Header -->
        <div class="bg-white p-3 rounded shadow-sm mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    
                    {{-- Avatar (first letter of name or profile image) --}}
                    @if($driver->image)
                        <img src="{{ asset('storage/app/public/'.$driver->image) }}" class="avatar" alt="Driver">
                    @else
                        <div class="avatar">{{ strtoupper(substr($driver->name, 0, 1)) }}</div>
                    @endif
                    
                    <div>
                        <div class="small text-muted">Hello, Welcome 👋</div>
                        <div class="fw-bold">{{ $driver->name }}</div>
                        
                        {{-- Status badge --}}
                        @if($driver->status === 'Active')
                            <div class="badge-active mt-1 blink">Active</div>
                        @else
                            <div class="badge bg-secondary mt-1">{{ ucfirst($driver->status) }}</div>
                        @endif
                    </div>
                </div>

                
            </div>
        </div>

        <!-- Stats -->
        <div class="d-flex gap-2 mb-3">
            
        <div wire:poll.120s="calculateTotalHours({{ $driver->id}})"  class="flex-fill bg-success text-white text-start p-3 rounded mb-3">
        <div class="fw-bold">{{ $totalHours }}</div>
        <div class="small">Hours Online</div>
        </div>
        
        <div class="flex-fill bg-dark text-white text-start p-3 rounded mb-3">
        <div class="fw-bold">{{ $todayOrdersCount }}</div>
        <div class="small">Today Orders</div>
        </div>
        
        </div>
        
    @if($error)
    <div class="alert alert-danger">{{ $error }}</div>
@endif    

      <div class="checkin-section">
    @if ($attendance)
       <div>
           
    @if(!$showOutMeterInput)
        <button wire:click="checkOut" class="btn btn-danger w-100 rounded" wire:loading.attr="disabled">
            <span>Check Out</span>
            
        </button>
        <div class="mt-2 mb-2 small text-muted text-center">
        Checked in at: {{ \Carbon\Carbon::parse($attendance->checkin_time)->format('h:i A') }}
    </div>
    @else
        <div class="mb-3">
            <label class="form-label fw-bold">Meter Reading</label>
            <input type="number" wire:model="out_meter_reading" class="form-control" placeholder="Enter Meter Reading">
             @error('out_meter_reading') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Meter Image</label>
            <input type="file" wire:model="out_meter_image" class="form-control">
             @error('out_meter_image') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <button wire:click="saveCheckOutData" class="btn btn-danger w-100 rounded" wire:loading.attr="disabled">
            <span wire:loading.remove>Checkout</span>
            <span wire:loading>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Saving...
            </span>
        </button>
        
        <button wire:click="checkoutCancel" class="btn btn-primary w-100 rounded mt-2">Cancel</button>
    @endif

    
</div>

    @else
        <div>
    {{-- Step 1: Show Check In button --}}
    @if(!$showMeterInput)
        <div class="mb-3">
            <button wire:click="checkIn" class="btn btn-success check-in-btn w-100 rounded" wire:loading.attr="disabled">Check In</button>
        </div>
    @endif

    {{-- Step 2: Show meter reading + image after Check In --}}
    @if($showMeterInput)
        <form wire:submit.prevent="saveCheckInData" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Meter Reading</label>
                <input type="number" class="form-control" wire:model="meter_reading" placeholder="Enter meter reading">
                @error('meter_reading') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Meter Image</label>
                <input type="file" class="form-control" wire:model="meter_image" accept="image/*">
                @error('meter_image') <span class="text-danger">{{ $message }}</span> @enderror

                <div wire:loading wire:target="meter_image" class="mt-2">
                    <span class="spinner-border spinner-border-sm"></span> Uploading...
                </div>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-success w-100 rounded check-in-btn" wire:loading.attr="disabled">
                    <span wire:loading.remove>Check In</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm"></span> Saving...
                    </span>
                </button>
            </div>
        </form>
        <button wire:click="checkinCancel" class="btn btn-danger w-100 rounded">Cancel</button>

    @endif
</div>

    @endif
</div>

    @if ($attendance)
    
<!--<div class="container py-5">-->
<!--    <div class="performance-card">-->
<!--        <h6 class="mb-3">Today's Performance</h6>-->
<!--        <canvas id="performanceChart" height="120"></canvas>-->

<!--        <div class="row text-center pt-4">-->
<!--            <div class="col">-->
<!--                <div class="stat-value">$0.00</div>-->
<!--                <div class="stat-label">Total Earning</div>-->
<!--            </div>-->
<!--            <div class="col">-->
<!--                <div class="stat-value">-->
<!--                    <i class="bi bi-bicycle"></i> 0.0k-->
<!--                </div>-->
<!--                <div class="stat-label">Total Distance</div>-->
<!--            </div>-->
<!--            <div class="col">-->
<!--                <div class="stat-value">$0</div>-->
<!--                <div class="stat-label">Avg. Daily Earning</div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!--  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>-->
<!--<script>-->
<!--    const ctx = document.getElementById('performanceChart').getContext('2d');-->
<!--    const performanceChart = new Chart(ctx, {-->
<!--        type: 'line',-->
<!--        data: {-->
<!--            labels: ['24', '25', '26', '27', '28', '29', '30'],-->
<!--            datasets: [-->
<!--                {-->
<!--                    label: 'Dataset 1',-->
<!--                    data: [3, 5, 4, 6, 5, 6, 4],-->
<!--                    borderColor: 'red',-->
<!--                    backgroundColor: 'transparent',-->
<!--                    tension: 0.4-->
<!--                },-->
<!--                {-->
<!--                    label: 'Dataset 2',-->
<!--                    data: [4, 3, 5, 7, 4, 5, 5],-->
<!--                    borderColor: 'green',-->
<!--                    backgroundColor: 'transparent',-->
<!--                    tension: 0.4-->
<!--                },-->
<!--                {-->
<!--                    label: 'Dataset 3',-->
<!--                    data: [2, 4, 6, 5, 7, 6, 5],-->
<!--                    borderColor: 'blue',-->
<!--                    backgroundColor: 'transparent',-->
<!--                    tension: 0.4-->
<!--                }-->
<!--            ]-->
<!--        },-->
<!--        options: {-->
<!--            responsive: true,-->
<!--            plugins: {-->
<!--                legend: {-->
<!--                    display: false-->
<!--                }-->
<!--            },-->
<!--            scales: {-->
<!--                x: {-->
<!--                    grid: {-->
<!--                        display: false-->
<!--                    }-->
<!--                },-->
<!--                y: {-->
<!--                    display: false-->
<!--                }-->
<!--            }-->
<!--        }-->
<!--    });-->
<!--</script>-->
    @endif




        <!-- Restaurants -->
        <div class="mb-2 d-flex justify-content-between align-items-center">
            <div class="fw-bold">Restaurants</div>
            <a href="order" class="small text-primary text-decoration-none">See all</a>
        </div>

        <div class="row g-2 mb-4">
        @foreach($businesses as $business)
       
        <div class="col-6">
             <a href="order">
            <div class="restaurant-btn text-center">
                {{ $business->name }}
            </div>
            </a>
        </div>
        
        @endforeach
        </div>


        <!-- Ninja Section -->
        <!--<div class="d-flex justify-content-between align-items-center">-->
        <!--    <div class="fw-bold">Ninja</div>-->
        <!--    <a href="#" class="small text-success text-decoration-none">More</a>-->
        <!--</div>-->
        <!--<div class="row g-2 mb-4">-->
        <!--    <div class="col-6"><div class="restaurant-btn text-center">Desert Delights</div></div>-->
        <!--    <div class="col-6"><div class="restaurant-btn text-center">Green Garden</div></div>-->
        <!--    <div class="col-6"><div class="restaurant-btn text-center">Hunger Station</div></div>-->
        <!--    <div class="col-6"><div class="restaurant-btn text-center">Jahez</div></div>-->
        <!--</div>-->
    </div>

    <x-layouts.driverfooter />
</div>
