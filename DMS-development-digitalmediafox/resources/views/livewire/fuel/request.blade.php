<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Vehicle</th>
                <th>Fuel Type</th>
                <!--<th>Amount (L)</th>-->
                <!--<th>Reason</th>-->
                <th>Status</th>
                <th>Orders</th>
                <th>Requested At</th>
                <th>File</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fuelRequests as $index => $fuel)
                <tr>
                    <td>{{ $fuel->vehicle->registration_number ?? 'N/A' }}</td>
                    <td class="text-capitalize">{{ $fuel->fuel_type }}</td>
                    <!--<td>{{ $fuel->request_amount }}</td>-->
                    <!--<td class="text-capitalize">{{ str_replace('_', ' ', $fuel->reason_for_request) }}</td>-->
                    <td>
                            <span class="badge rounded-pill
                            @if($fuel->status === 'pending') bg-warning
                            @elseif($fuel->status === 'approved') bg-success
                            @elseif($fuel->status === 'rejected') bg-danger
                            @else bg-secondary
                            @endif
                            ">
                            {{ ucfirst($fuel->status) }}
                            </span>
                    </td>
                    <td>{{ $fuel->number_of_order_deliver ?? '-' }}</td>
                    <td>{{ $fuel->created_at->format('d-m-Y') }}</td>
                    <td>
                        @if($fuel->upload_order_screenshort)
                            <a href="{{ asset('storage/app/public/'.$fuel->upload_order_screenshort) }}" target="_blank" class="btn btn-sm btn-outline-info">View</a>
                        @else
                            -
                        @endif
                    </td>
                     <td>
                        @if ($fuel->status === 'rejected' || $fuel->status === 'approved')
                            <button class="btn btn-sm btn-outline-info" wire:click="showFuelDetails({{ $fuel->id }})">Details</button>
                        @else
                            <button class="btn btn-sm btn-outline-success" wire:click="openApproveModal({{ $fuel->id }})" >Approve</button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="confirmReject({{ $fuel->id }})">Reject</button>
                            <button class="btn btn-sm btn-outline-info" wire:click="showFuelDetails({{ $fuel->id }})">Details</button>
                        @endif

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">No fuel requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Fuel Request Modal -->
<div wire:ignore.self class="modal fade" id="fuelRequestModal" tabindex="-1" aria-labelledby="fuelRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form wire:submit.prevent="saveFuelRequest" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="fuelRequestModalLabel">New Fuel Request</h5>
                    
                </div>

                <div class="modal-body row g-3">
                    <!-- Vehicle -->
                    <div class="col-md-6">
                        <label for="vehicle_id" class="form-label">Vehicle</label>
                        <select wire:model="vehicle_id" class="form-select">
                            <option value="">Select vehicle...</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->registration_number }} ({{ $vehicle->make }} {{ $vehicle->model }})</option>
                            @endforeach
                        </select>
                        @error('vehicle_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Fuel Type -->
                    <div class="col-md-6">
                        <label for="fuel_type" class="form-label">Fuel Type</label>
                        <select wire:model="fuel_type" class="form-select">
                            <option value="">Select fuel type...</option>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                             <option value="cng">CNG</option>
                              <option value="electric">Electric</option>
                        </select>
                          @error('fuel_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Requested Amount -->
                    <div class="col-md-6">
                        <label for="request_amount" class="form-label">Requested Amount (Liters)</label>
                        <input type="number" wire:model="request_amount" class="form-control" min="1" placeholder="Enter Requested Amount (Liters)">
                         @error('request_amount') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Reason for Request -->
                    <div class="col-md-6">
                        <label for="reason_for_request" class="form-label">Reason for Request</label>
                        <select wire:model="reason_for_request" class="form-select">
                            <option value="">Select reason...</option>
                            <option value="regular_refill">Regular Refill</option>
                            <option value="additional_orders">Additional Orders</option>
                            <option value="long_distance">Long Distance Trip</option>
                            <option value="emergency">Emergency</option>
                            <option value="other">Other</option>
                        </select>
                        @error('reason_for_request') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Number of Orders to Deliver -->
                    <div class="col-md-6">
                        <label for="number_of_order_deliver" class="form-label">Number of Orders to Deliver</label>
                        <input type="number" wire:model="number_of_order_deliver" class="form-control" min="0" placeholder="Enter Number of Orders to Deliver">
                        @error('number_of_order_deliver') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Upload Order Screenshot -->
                    <div class="col-md-6">
                        <label for="upload_order_screenshort" class="form-label">Upload Order Screenshot (Optional)</label>
                        <input type="file" wire:model="upload_order_screenshort" class="form-control">
                    </div>

                    <!-- Additional Notes -->
                    <div class="col-12">
                        <label for="additional_notes" class="form-label">Additional Notes (Optional)</label>
                        <textarea wire:model="additional_notes" class="form-control" rows="3" placeholder="Enter any additional notes..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    
                    <button type="submit" class="btn btn-primary">
                       Save Request
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Fuel Details Modal -->
<div class="modal fade" id="fuelDetailsModal" tabindex="-1" aria-labelledby="fuelDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fuel Request Details</h5>
                
            </div>

            <div class="modal-body">
                @if($selectedFuel)
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Vehicle:</strong> {{ $selectedFuel->vehicle->registration_number ?? '-' }}</li>
                        <li class="list-group-item"><strong>Fuel Type:</strong> {{ ucfirst($selectedFuel->fuel_type) }}</li>
                        <li class="list-group-item"><strong>Requested Amount:</strong> {{ $selectedFuel->request_amount }} Liters</li>
                        <li class="list-group-item"><strong>Reason:</strong> {{ str_replace('_', ' ', ucfirst($selectedFuel->reason_for_request)) }}</li>
                        <li class="list-group-item"><strong>Orders to Deliver:</strong> {{ $selectedFuel->number_of_order_deliver ?? '-' }}</li>
                        <li class="list-group-item"><strong>Status:</strong>
                            <span class="badge rounded-pill
                                @if($selectedFuel->status === 'pending') bg-warning
                                @elseif($selectedFuel->status === 'approved') bg-success
                                @elseif($selectedFuel->status === 'rejected') bg-danger
                                @else bg-secondary
                                @endif
                            ">
                                {{ ucfirst($selectedFuel->status) }}
                            </span>
                        </li>
                        <li class="list-group-item"><strong>Additional Notes:</strong> {{ $selectedFuel->additional_notes ?? '-' }}</li>
                        @if($selectedFuel->upload_order_screenshort)
                        <li class="list-group-item">
                            <strong>Screenshot:</strong><br>
                            <a href="{{ asset('storage/app/public/'.$selectedFuel->upload_order_screenshort) }}" target="_blank" class="btn btn-sm btn-info">View Screenshot</a>
                        </li>
                        @endif
                    </ul>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Fuel Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                @php use Illuminate\Support\Str; 
                @endphp
                @if ($fuelToReject)

                    <div class="bg-gray-50 p-4 rounded-lg mb-4 border">
                    <h5 class="fw-semibold mb-3 text-dark">Request Details:</h5>
                    
                    <div class="mb-2 d-flex justify-content-between small">
                        <span class="text-dark fw-semibold">Iqama Number :</span>
                        <span class="text-dark">{{ $fuelToReject->driver->iqaama_number ?? 'Unknown' }}</span>
                    </div>

                    <div class="mb-2 d-flex justify-content-between small">
                        <span class="text-muted fw-semibold">Vehicle:</span>
                        <span class="text-dark">{{ $fuelToReject->vehicle->registration_number ?? 'Unknown' }}</span>
                    </div>

                    <div class="mb-2 d-flex justify-content-between small">
                        <span class="text-muted fw-semibold">Requested By:</span>
                       <span class="text-dark">{{ $fuelToReject->requestedBy->name ?? '-' }}</span>

                    </div>

                    <div class="mb-2 d-flex justify-content-between small">
                        <span class="text-muted fw-semibold">Fuel Type:</span>
                        <span class="text-dark text-capitalize">{{ $fuelToReject->fuel_type }}</span>
                    </div>

                    <!--<div class="mb-2 d-flex justify-content-between small">-->
                    <!--    <span class="text-muted fw-semibold">Requested Amount:</span>-->
                    <!--    <span class="text-dark">{{ $fuelToReject->request_amount }} L</span>-->
                    <!--</div>-->

                    <!--<div class="mb-2 d-flex justify-content-between small">-->
                    <!--    <span class="text-muted fw-semibold">Reason:</span>-->
                    <!--    <span class="text-dark text-capitalize"> {{ Str::of($fuelToReject->reason_for_request)->replace('_', ' ')->title() }}</span>-->
                    <!--</div>-->

                    <div class="mb-2 d-flex justify-content-between small">
                        <span class="text-muted fw-semibold">Number of Orders:</span>
                        <span class="text-dark">{{ $fuelToReject->number_of_order_deliver ?? '-' }}</span>
                    </div>

                    <div class="mb-0 d-flex justify-content-between small">
                        <span class="text-muted fw-semibold">Request Date:</span>
                        <span class="text-dark">{{ $fuelToReject->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>


                    <div class="mb-3">
                        <label class="form-label">Rejection Notes</label>
                        <textarea wire:model.defer="rejectionNotes" class="form-control" rows="3" placeholder="Enter reason for rejection..."></textarea>
                        @error('rejectionNotes') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" wire:click="rejectFuelRequest">Reject Request</button>
            </div>
        </div>
    </div> 
</div>
<div wire:ignore.self class="modal fade" id="approveFuelModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form wire:submit.prevent="approveFuelRequest">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Fuel Request</h5>
                </div>
                <div class="modal-body">
                @if ($fuelToApprove)

                    <div class="bg-gray-50 p-4 rounded-lg mb-4 border">
                    <h5 class="fw-semibold mb-3 text-dark">Request Details:</h5>

                    <div class="mb-2 d-flex justify-content-between small">
                        <span class="text-dark fw-semibold">Iqama Number :</span>
                        <span class="text-dark">{{ $fuelToApprove->driver->iqaama_number ?? 'Unknown' }}</span>
                    </div>
                    
                    <div class="mb-2 d-flex justify-content-between small">
                        <span class="text-dark fw-semibold">Vehicle :</span>
                        <span class="text-dark">{{ $fuelToApprove->vehicle->registration_number ?? 'Unknown' }}</span>
                    </div>

                    <div class="mb-2 d-flex justify-content-between small">
                        <span class="text-dark fw-semibold">Requested By :</span>
                       <span class="text-dark">{{ $fuelToApprove->requestedBy->name ?? '-' }}</span>

                    </div>

                    <div class="mb-2 d-flex justify-content-between small">
                        <span class="text-dark fw-semibold">Fuel Type :</span>
                        <span class="text-dark text-capitalize">{{ $fuelToApprove->fuel_type }}</span>
                    </div>

                    <!--<div class="mb-2 d-flex justify-content-between small">-->
                    <!--    <span class="text-muted fw-semibold">Requested Amount:</span>-->
                    <!--    <span class="text-dark">{{ $fuelToApprove->request_amount }} L</span>-->
                    <!--</div>-->

                    <!--<div class="mb-2 d-flex justify-content-between small">-->
                    <!--    <span class="text-muted fw-semibold">Reason:</span>-->
                    <!--    <span class="text-dark text-capitalize"> {{ Str::of($fuelToApprove->reason_for_request)->replace('_', ' ')->title() }}</span>-->
                    <!--</div>-->

                    <div class="mb-2 d-flex justify-content-between small">
                        <span class="text-dark fw-semibold">Number of Orders :</span>
                        <span class="text-dark">{{ $fuelToApprove->number_of_order_deliver ?? '-' }}</span>
                    </div>

                    <div class="mb-0 d-flex justify-content-between small">
                        <span class="text-dark fw-semibold">Request Date :</span>
                        <span class="text-dark">{{ $fuelToApprove->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
                @endif
                    <!--<div class="mb-3">-->
                    <!--    <label>Approved Amount (Liters) *</label>-->
                    <!--    <input type="number" wire:model.defer="approved_amount" class="form-control" required>-->
                    <!--</div>-->
                    <!--<div class="mb-3">-->
                    <!--    <label>Approved Fuel Type</label>-->
                    <!--    <select wire:model.defer="approved_fuel_type" class="form-select" required>-->
                    <!--    <option value="">Select Fuel</option>-->
                    <!--    <option value="petrol">Petrol</option>-->
                    <!--    <option value="diesel">Diesel</option>-->
                    <!--    <option value="cng">CNG</option>-->
                    <!--    <option value="electric">Electric</option>-->
                    <!--    </select>-->

                    <!--</div>-->
                    <!--<div class="mb-3">-->
                    <!--    <label>Estimated Cost (Optional)</label>-->
                    <!--    <input type="number" step="0.01" wire:model.defer="estimated_cost" class="form-control">-->
                    <!--</div>-->
                    <!--<div class="mb-3">-->
                    <!--    <label>Scheduled Fuel Date (Optional)</label>-->
                    <!--    <input type="date" wire:model.defer="scheduled_date" class="form-control">-->
                    <!--</div>-->
                    <!--<div class="mb-3">-->
                    <!--    <label>Approval Notes</label>-->
                    <!--    <textarea wire:model.defer="notes" class="form-control" rows="3" placeholder="Enter any special instructions or notes..."></textarea>-->
                    <!--</div>-->
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Request</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>

function closeAlert()
{
    setTimeout(function () {
    $('#success-alert').hide();
    }, 3000);
}


    window.addEventListener('close-fuel-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('fuelRequestModal'));
        if (modal) modal.hide();
        closeAlert();
    });

    window.addEventListener('show-add-edit-modal', function () {
            const modal = $('#fuelRequestModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });

      window.addEventListener('show-fuel-details-modal', function () {
            const modal = $('#fuelDetailsModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });

     window.addEventListener('show-reject-modal', function () {
            const modal = $('#rejectModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });


    window.addEventListener('hide-reject-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
        if (modal) modal.hide();
        closeAlert();
    });

window.addEventListener('download-csv', function () {
        window.location.href = "{{ route('fuel.export') }}";
    });

 window.addEventListener('show-approve-modal', function () {
            const modal = $('#approveFuelModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });
    window.addEventListener('close-approve-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('approveFuelModal'));
        if (modal) modal.hide();
        closeAlert();
    });

</script>
