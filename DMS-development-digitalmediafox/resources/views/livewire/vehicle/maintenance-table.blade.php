@if ($maintenanceRequests->isEmpty())
    <p class="text-muted">No maintenance requests found.</p>
@else
  <div wire:loading class="text-center col-12">
    <i class="spinner-border text-primary" role="status"></i>
</div>

    <div wire:loading.remove class="table-responsive">
        <table class="table table-bordered table-hover align-middle" id="maintenanceTable">
            <thead class="table-light">
                <tr>
                    <th>Vehicle</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Request by</th>
                    <th>Requested Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maintenanceRequests as $request)
                    <tr>
                        <td>{{ $request->vehicle->registration_number }} <small>({{ $request->vehicle->make }} {{ $request->vehicle->model }})</small></td>
                        <td>{{ ucwords(str_replace('_', ' ', $request->maintenance_type)) }}</td>
                        @php
                        $statusColor = match($request->status) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'secondary',
                        };
                        @endphp

                        <td>
                        <span class="badge bg-{{ $statusColor }}">
                        {{ ucfirst($request->status) }}
                        </span>
                        </td>

                       <td>{{ $request->request_by }}</td>
                        <td>{{ $request->created_at->format('d-m-Y') }}</td>

                        <td>
                         <div class="btn-group btn-group-sm gap-1">
                             <button data-bs-toggle="tooltip" title="Click to View" class="btn btn-outline-primary" wire:click="showVehicleDetails({{ $request->vehicle->id }})"><i class="ri-truck-line"></i></button>
                            @if ($request->status === 'approved')
                                <button wire:click="complete({{ $request->id }})" class="btn btn-outline-info">
                                    <i class="ri-tools-line"></i> Complete Maintenance
                                </button>
                                 <!-- Replacement Button -->
                            <button wire:click="replace({{ $request->vehicle_id }})"
                            class="btn btn-outline-dark me-1"
                            wire:loading.attr="disabled"
                            wire:target="replace({{ $request->vehicle_id }})">
                            <span wire:loading.remove wire:target="replace({{ $request->vehicle_id }})">Replace With</span>
                            <span wire:loading wire:target="replace({{ $request->vehicle_id }})">
                            <span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span>
                            </span>
                            </button>
                            @elseif ($request->status === 'pending')
                                <button wire:click="approve({{ $request->id }})" class="btn btn-outline-success">
                                    <i class="ri-check-line"></i> Approve
                                </button>
                                <button wire:click="reject({{ $request->id }})" class="btn btn-outline-danger">
                                    <i class="ri-close-line"></i> Reject
                                </button>
                            @endif
                            
                           
                        </div>

                        </td>


                        
                       
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $maintenanceRequests->links() }}
    </div>
@endif

<!-- Approve Maintenance Modal -->
<div wire:ignore.self class="modal fade" id="approveMaintenanceModal" tabindex="-1" aria-labelledby="approveMaintenanceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form wire:submit.prevent="saveApproval">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Approve Maintenance Request</h5>
          
        </div>
        <div class="modal-body">
          <div class="mb-3">
                <label class="form-label fw-bold">Request Details</label>
@php
    $maintenance = \App\Models\VehicleMaintenance::find($selectedMaintenanceId);
    $vehicle = $maintenance?->vehicle;
@endphp

<textarea class="form-control" rows="6" disabled>
Vehicle: {{ $vehicle?->registration_number . ' (' . $vehicle?->make . ' ' . $vehicle?->model.')' ?? '-' }}
Type: {{ $maintenance?->maintenance_type ?? '-' }}
Description: {{ $maintenance?->description ?? '-' }}
Urgency: {{ ucfirst($maintenance?->urgency ?? '-') }}
Request Date: {{ $maintenance?->created_at?->format('d/m/Y') ?? '-' }}
</textarea>


                </div>

          <div class="mb-3 mt-2">
            <label class="form-label">Estimated Cost <span class="text-danger">*</span></label>
            <input type="number" step="0.01" wire:model.defer="estimated_cost" class="form-control">
            @error('estimated_cost') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Scheduled Date <span class="text-danger">*</span></label>
            <input type="date" wire:model.defer="scheduled_date" class="form-control">
            @error('scheduled_date') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Approval Notes</label>
            <textarea wire:model.defer="approval_notes" class="form-control" rows="3"></textarea>
            @error('approval_notes') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Approve</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="$set('approvalModal', false)">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Vehicle Replacement Modal -->
<div wire:ignore.self class="modal fade" id="replacementModal" tabindex="-1" aria-labelledby="replacementModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form wire:submit.prevent="saveReplacement">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Request Vehicle Replacement</h5>
        </div>
        <div class="modal-body">
          
          <div class="mb-3">
            <label class="form-label">Vehicle</label>
            <input type="text" class="form-control" disabled wire:model="vehicle_name">
          </div>
          
           <div class="mb-3">
        <label class="form-label">Available Vehicle <span class="text-danger">*</span></label>
        <select class="form-control" wire:model="Avehicle_id">
        <option value="">-- Select Vehicle --</option>
        @foreach($Avehicles as $v)
        <option value="{{ $v->id }}">
        {{ $v->make }} / {{ $v->model }}
        </option>
        @endforeach
        </select>
         @error('Avehicle_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

          <div class="mb-3">
            <label class="form-label">Reason for Replacement <span class="text-danger">*</span></label>
            <select class="form-select" wire:model.defer="reason">
              <option value="">Select reason...</option>
              <option value="breakdown">Vehicle Breakdown</option>
              <option value="accident">Accident Damage</option>
              <option value="maintenance">Extended Maintenance</option>
              <option value="performance_issues">Performance Issues</option>
              <option value="safety_concerns">Safety Concerns</option>
            </select>
            @error('reason') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Replacement Period <span class="text-danger">*</span></label>
            <select class="form-select" wire:model.defer="replacement_period">
              <option value="">Select period...</option>
              <option value="temporary">Temporary</option>
              <option value="permanent">Permanent</option>
            </select>
            @error('replacement_period') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Additional Notes</label>
            <textarea class="form-control" wire:model.defer="notes" rows="3"></textarea>
            @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit Request</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="$set('showReplacementModal', false)">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div wire:ignore.self class="modal fade" id="completeMaintenanceModal" tabindex="-1" aria-labelledby="completeMaintenanceLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form wire:submit.prevent="saveCompletion">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Complete Maintenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="$set('completeModal', false)"></button>
                </div>
                <div class="modal-body">

                <div class="mb-3">
                <label class="form-label fw-bold">Request Details</label>
                @php
    $maintenance = \App\Models\VehicleMaintenance::find($selectedMaintenanceId);
    $vehicle = $maintenance?->vehicle;
@endphp

<textarea class="form-control" rows="6" disabled>
Vehicle: {{ $vehicle?->registration_number . ' (' . $vehicle?->make . ' ' . $vehicle?->model.')' ?? '-' }}
Type: {{ $maintenance?->maintenance_type ?? '-' }}
Description: {{ $maintenance?->description ?? '-' }}
Urgency: {{ ucfirst($maintenance?->urgency ?? '-') }}
Request Date: {{ $maintenance?->created_at?->format('d/m/Y') ?? '-' }}
</textarea>

                </div>


                    <div class="mb-3 mt-2">
                        <label class="form-label">Resolution Details <span class="text-danger">*</span></label>
                        <textarea class="form-control" wire:model.defer="resolution_details" rows="3"></textarea>
                        @error('resolution_details') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cost Incurred <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" wire:model.defer="cost_incurred">
                        @error('cost_incurred') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Next Maintenance Date (optional)</label>
                        <input type="date" class="form-control" wire:model.defer="next_maintenance_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Complete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="$set('completeModal', false)">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Reject Maintenance Modal -->
<div wire:ignore.self class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form wire:submit.prevent="saveRejection">
      <div class="modal-content">
       <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Reject Maintenance Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="$set('rejectModal', false)"></button>
        </div>
        <div class="modal-body">
        <div class="mb-3">
        <label class="form-label fw-bold">Request Details</label>
       @php
    $maintenance = \App\Models\VehicleMaintenance::find($selectedMaintenanceId);
    $vehicle = $maintenance?->vehicle;
@endphp

<textarea class="form-control" rows="6" disabled>
Vehicle: {{ $vehicle?->registration_number . ' (' . $vehicle?->make . ' ' . $vehicle?->model.')' ?? '-' }}
Type: {{ $maintenance?->maintenance_type ?? '-' }}
Description: {{ $maintenance?->description ?? '-' }}
Urgency: {{ ucfirst($maintenance?->urgency ?? '-') }}
Request Date: {{ $maintenance?->created_at?->format('d/m/Y') ?? '-' }}
</textarea>

        </div>
          <div class="mb-3">
            <label class="form-label">Rejection Notes (optional)</label>
            <textarea wire:model.defer="rejection_notes" class="form-control" rows="3"></textarea>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Reject Request</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
         window.addEventListener('show-replacement-modal', function () {
            const modal = $('#replacementModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });


window.addEventListener('hide-replacement-modal', () => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('replacementModal'));
    if (modal) modal.hide();
    setTimeout(function () {
                $('#success-alert').hide();
            }, 4000);
});
</script>

