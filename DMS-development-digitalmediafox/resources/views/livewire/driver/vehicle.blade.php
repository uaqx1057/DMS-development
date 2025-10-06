<div class="mobile-wrapper">
    <x-layouts.driverheader />

    <style>
    html, body {
  height: 100%;
  overscroll-behavior-y: contain; /* ✅ disables pull-to-refresh but keeps scroll bounce */
}

        .activeli { color: green; }
        .card { background-color: #ffffff; }
        .fw-semibold { font-weight: 600; }
        .bs{box-shadow:0px 0px 2px 0px slategray; border-radius:10px;}
    </style>

    <div class="container bodySection">
        <div class="card shadow-sm rounded-4 p-3">
            <h5 class="text-center fw-bold mb-3">Vehicle Information</h5>


        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
               <button class="nav-link active" id="current-tab" data-bs-toggle="tab" data-bs-target="#current" type="button" role="tab">
                        Current
                    </button>
            </li>
           <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                        History
                    </button>
        </ul>
        



            <div class="tab-content" id="vehicleTabsContent">

                <!-- Current Vehicles -->
<div class="tab-pane fade show active" id="current" role="tabpanel">
    @if($currentAssignments->count() > 0)
        @foreach($currentAssignments as $assignment)
            @php $vehicle = $assignment->vehicle; @endphp
            <div class="border bs p-2 mb-2">
                <div class="row mb-1">
                    <div class="col-6">
                        <small class="text-muted">Vehicle Number</small><br>
                        <span class="fw-semibold">{{ $vehicle->registration_number }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Vehicle Type</small><br>
                        <span class="fw-semibold">{{ $vehicle->make }} {{ $vehicle->model }}</span>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-6">
                        <small class="text-muted">VIN</small><br>
                        <span class="fw-semibold">{{ $vehicle->vin }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Fuel Type</small><br>
                      <span class="fw-semibold">{{ strtoupper($vehicle->fuel_type) }}</span>

                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Status</small><br>
                        <span class="fw-semibold text-success">Assigned</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Assign Date</small><br>
                        <span class="fw-semibold">{{ $assignment->created_at->format('d-m-Y') }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center text-muted">No current assigned vehicle.</div>
    @endif
</div>

<!-- History Vehicles -->
<div class="tab-pane fade" id="history" role="tabpanel">
    @if($historyAssignments->count() > 0)
        @foreach($historyAssignments as $assignment)
            @php $vehicle = $assignment->vehicle; @endphp
            <div class="border bs p-2 mb-3">
                <div class="row mb-1">
                    <div class="col-6">
                        <small class="text-muted">Vehicle Number</small><br>
                        <span class="fw-semibold">{{ $vehicle->registration_number }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Vehicle Type</small><br>
                        <span class="fw-semibold">{{ $vehicle->make }} {{ $vehicle->model }}</span>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-6">
                        <small class="text-muted">VIN</small><br>
                        <span class="fw-semibold">{{ $vehicle->vin }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Fuel Type</small><br>
                        <span class="fw-semibold">{{ strtoupper($vehicle->fuel_type) }}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Assign Date</small><br>
                        <span class="fw-semibold">{{ $assignment->created_at->format('d-m-Y') }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Unassign Date</small><br>
                        <span class="fw-semibold">{{ $assignment->updated_at->format('d-m-Y') }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center text-muted">No history available.</div>
    @endif
</div>

            </div>
        </div>
    </div>

    <x-layouts.driverfooter />
</div>
