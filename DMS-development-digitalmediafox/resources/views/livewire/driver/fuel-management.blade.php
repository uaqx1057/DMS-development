@php
    use Illuminate\Support\Facades\Auth;
    $driver = Auth::guard('driver')->user();
@endphp

<div class="mobile-wrapper">
    <style>
    html, body {
  height: 100%;
  overscroll-behavior-y: contain; /* ✅ disables pull-to-refresh but keeps scroll bounce */
}

    .card{border-radius:10px;}
        .active {
            background-color: #ffffff !important;
        }

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

    <x-layouts.driverheader />

    <div class="container" style="max-width: 500px; position: relative;">
        <div class="mb-4">
            <h4 class="fw-bold">Fuel Management</h4>
            
            @if (session()->has('success'))
                <div class="mt-2 alert alert-success small">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Toggle Button -->
            <button wire:click="toggleRequestForm" class="btn w-100 mt-3 {{ $showRequestForm ? 'btn-outline-danger' : 'btn-primary' }}">
                {{ $showRequestForm ? 'Cancel' : 'Request Fuel' }}
            </button>

            <!-- Fuel Request Form -->
            @if ($showRequestForm)
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="mb-3">New Fuel Request</h6>

                        @if (session()->has('success'))
                            <div class="alert alert-success small">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Form -->
                        <div class="mb-2">
                            <label class="form-label">Iqama Number <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                class="form-control" 
                                value="{{ Auth::guard('driver')->user()->iqaama_number ?? '' }}" 
                                disabled
                            >
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Vehicle Registration <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control" 
                                value="{{ $currentVehicle->registration_number ?? 'Not Assigned' }}" 
                                disabled
                            >
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Total Orders Today <span class="text-danger">*</span></label>
                            <input type="number" wire:model="number_of_order_deliver" class="form-control">
                            @error('number_of_order_deliver')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Attach ScreenShots of Orders</label>
                            <input type="file" wire:model="scrrenShorts" class="form-control" accept="image/*">
                            @error('scrrenShorts')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button wire:click="submit" class="btn btn-success w-100">Submit Request</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        

        <!-- Tabs -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <!-- Tabs -->
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a wire:click.prevent="setTab('requests')" 
                       class="nav-link {{ $activeTab === 'requests' ? 'active' : '' }}" 
                       href="#">Requests</a>
                </li>
                <li class="nav-item">
                    <a wire:click.prevent="setTab('history')" 
                       class="nav-link {{ $activeTab === 'history' ? 'active' : '' }}" 
                       href="#">History</a>
                </li>
            </ul>
            
        <div class="d-flex">
             <button wire:click="showFilterModal" class="ms-2 btn border reload-btn">
                        <i class="ri-filter-line text-primary"></i>
                    </button>
                    
                    <button wire:click="refreshRequests" class="ms-2 btn border reload-btn">
        <i class="ri-refresh-line text-success" 
        wire:loading.class="rotate" 
        wire:target="refreshRequests"></i>
        </button>
        </div>
        
        
    </div>


<!-- Modal -->
@if($showFilter)
<div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-light bb">
                <h5 class="modal-title fw-bold text-dark">Fuel Request Filter</h5>
                <button type="button" class="btn-close px-4" wire:click="$set('showFilter', false)"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Date Time</label>
                    <input type="datetime-local" wire:model="filterDate" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select wire:model="filterStatus" class="form-select">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
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
  

        <!-- Tab Body -->
        <div class="position-relative">
            <!-- Loading Spinner (only inside tab body) -->
            @if(empty($fuelRequests))
            <div class="text-center p-4 bg-light rounded">
                    <div class="mb-2">
                        <i class="bi bi-droplet" style="font-size: 2rem; color: #bbb;"></i>
                    </div>
                    <h6 class="mb-1">No Request Yet</h6>
                    <small class="text-muted">Fuel Request Not Found</small>
                </div>
            @endif

            <!-- Fuel Cards -->
            @foreach ($fuelRequests as $request)
                @php
                    $isToday = $request['date_only'] === \Carbon\Carbon::today()->format('Y-m-d');
                @endphp

                @if ($activeTab === 'requests' && $isToday)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>Iqama Number : {{ ucfirst($request['iqaama_number']) }}</strong>
                                <span class="badge rounded-pill 
                                    @if($request['status'] === 'pending') bg-warning text-dark
                                    @elseif($request['status'] === 'approved') bg-success
                                    @elseif($request['status'] === 'rejected') bg-danger
                                    @endif">
                                    {{ ucfirst($request['status']) }}
                                </span>
                            </div>
                            <div class="text-muted" style="font-size: 10px">{{ $request['date'] }}</div>
                            <div class="mt-2" style="font-size: 10px">
                                 @if($request['status'] === 'rejected')
                                  <strong>Reason:</strong> {{ $request['reject_notes'] }}<br>
                                 @endif
                                <strong>Orders : </strong> {{ $request['orders'] }}
                            </div>
                             @if ($request['status'] != 'pending')
                           <hr>
                           @endif
                             @if ($request['status'] === 'rejected')
                                <div class="mt-2" style="font-size: 10px;">
                                    <strong>Rejecte By :</strong> {{ $request['reject_by'] ?? '-' }}<br>
                                    <strong>Rejecte Date :</strong> {{ $request['reject_date'] }}<br>
                                </div>
                            @endif
                            @if ($request['status'] === 'approved')
                                <div class="mt-2" style="font-size: 10px;">
                                    <strong>Approved By :</strong> {{ $request['approved_by'] ?? '-' }}<br>
                                    <strong>Approval Date :</strong> {{ $request['approval_date'] }}<br>
                                </div>
                            @endif
                        </div>
                    </div>

                @elseif ($activeTab === 'history' && !$isToday)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                 <strong>Iqama Number : {{ ucfirst($request['iqaama_number']) }}</strong>
                                <span class="badge rounded-pill 
                                    @if($request['status'] === 'approved') bg-success
                                    @elseif($request['status'] === 'rejected') bg-danger
                                    @elseif($request['status'] === 'pending') bg-warning text-dark
                                    @endif">
                                    {{ ucfirst($request['status']) }}
                                </span>
                            </div>
                            <div class="text-muted" style="font-size: 10px;">{{ $request['date'] }}</div>
                            <div class="mt-2" style="font-size: 10px;">
                                 @if($request['status'] === 'rejected')
                                  <strong>Reason:</strong> {{ $request['reject_notes'] }}<br>
                                 @endif
                               
                                <strong>Orders:</strong> {{ $request['orders'] }}
                            </div>
                            @if ($request['status'] != 'pending')
                           <hr>
                           @endif
                             @if ($request['status'] === 'rejected')
                                <div class="mt-2" style="font-size: 10px;">
                                    <strong>Rejecte By :</strong> {{ $request['reject_by'] ?? '-' }}<br>
                                    <strong>Rejecte Date :</strong> {{ $request['reject_date'] }}<br>
                                </div>
                            @endif
                            @if ($request['status'] === 'approved')
                                <div class="mt-2" style="font-size: 10px;">
                                    <strong>Approved By :</strong> {{ $request['approved_by'] ?? '-' }}<br>
                                    <strong>Approval Date :</strong> {{ $request['approval_date'] }}<br>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    @push('styles')
    <style>
        .nav-tabs .nav-link {
            color: #6c757d;
            cursor: pointer;
        }
        .nav-tabs .nav-link.active {
            color: #495057;
            font-weight: 600;
        }
    </style>
    @endpush

    <x-layouts.driverfooter />
</div>
