@if ($replacementRequests->isEmpty())
    <p class="text-muted">No replacement requests found.</p>
@else
<div wire:loading class="text-center col-12">
    <i class="spinner-border text-primary" role="status"></i>
</div>

    <div wire:loading.remove class="table-responsive">
        

          <table class="table table-bordered table-hover align-middle mb-0" id="replacementTable">
        <thead class="table-light">
          <tr>
            <th>Original Vehicle</th>
            <th>Replacement Vehicle</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Period</th>
            <th>Requested At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($replacementRequests as $request)
            <tr>
              <td> {{ $request->vehicle->registration_number }} <small>({{ $request->vehicle->make }} {{ $request->vehicle->model }})</small></td>
              <td>
              @php
              $replacementVehicle = \App\Models\Vehicle::find($request->replacement_vehicle);
              @endphp

              @if ($replacementVehicle)
              {{ $replacementVehicle->registration_number }} <small>({{ $replacementVehicle->make }} {{ $replacementVehicle->model }})</small>
              @else
              <span class="text-muted">Not assigned</span>
              @endif
              </td>

              <td>{{ ucfirst(str_replace('_', ' ', $request->reason)) }}</td>
              <td>
                <span class="badge bg-{{ 
                $request->status === 'approved' ? 'success' : 
                ($request->status === 'pending' ? 'warning' : 
                ($request->status === 'rejected' ? 'danger' : 'secondary')) 
                }}">
                {{ ucfirst($request->status) }}
                </span>

              </td>
               <td>{{ ucfirst($request->replacement_period) }}</td>
              <td>{{ $request->created_at->format('d-m-Y') }}</td>
              <td>
                 
           <div class="btn-group btn-group-sm gap-1">
               <button data-bs-toggle="tooltip" title="Click to View" class="btn btn-outline-primary" wire:click="showVehicleDetails({{ $request->vehicle->id }})"><i class="ri-truck-line"></i></button>
    @if ($request->status === 'approved')
        <button wire:click="returnOriginal({{ $request->id }})"
                wire:loading.attr="disabled"
                wire:target="returnOriginal({{ $request->id }})"
                class="btn btn-sm btn-outline-info">
            <span wire:loading.remove wire:target="returnOriginal({{ $request->id }})">
                <i class="ri-refresh-line"></i> Return Original Vehicle
            </span>
            <span wire:loading wire:target="returnOriginal({{ $request->id }})">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </span>
        </button>
    @elseif ($request->status !== 'rejected' && $request->status !== 'completed')
        <button wire:click="approveReplacement({{ $request->id }})"
                wire:loading.attr="disabled"
                wire:target="approveReplacement({{ $request->id }})"
                class="btn btn-sm btn-outline-success">
            <span wire:loading.remove wire:target="approveReplacement({{ $request->id }})">
                <i class="ri-check-line"></i> Approve
            </span>
            <span wire:loading wire:target="approveReplacement({{ $request->id }})">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </span>
        </button>

        <button wire:click="replacement_reject({{ $request->id }})"
                wire:loading.attr="disabled"
                wire:target="replacement_reject({{ $request->id }})"
                class="btn btn-sm btn-outline-danger">
            <span wire:loading.remove wire:target="replacement_reject({{ $request->id }})">
                <i class="ri-close-line"></i> Reject
            </span>
            <span wire:loading wire:target="replacement_reject({{ $request->id }})">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </span>
        </button>
    @endif
</div>

        </td>

              
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">No replacement requests found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      {{ $replacementRequests->links() }}
    </div>
@endif
<!-- Vehicle Replacement Approval Modal -->
<div wire:ignore.self class="modal fade" id="replacementApprovalModal" tabindex="-1" aria-labelledby="replacementApprovalModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form wire:submit.prevent="saveReplacementApproval">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Approve Vehicle Replacement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="$set('replacementApprovalModal', false)"></button>
        </div>
        <div class="modal-body">



    @php
    $replacement = \App\Models\VehicleReplacement::with(['vehicle', 'replacementVehicle'])
        ->find($selectedReplacementRequestId);

    $details = "Original Vehicle : " 
        . ($replacement?->vehicle
            ? $replacement->vehicle->registration_number . ' (' . $replacement->vehicle->make . ' ' . $replacement->vehicle->model . ')'
            : 'Not assigned') . "\n";

    $details .= "Replacement Vehicle : " 
        . ($replacement?->replacementVehicle
            ? $replacement->replacementVehicle->registration_number . ' (' . $replacement->replacementVehicle->make . ' ' . $replacement->replacementVehicle->model . ')'
            : 'Not assigned') . "\n";

    $details .= "Reason : " . ucfirst($replacement?->reason ?? '-') . "\n";
    $details .= "Period : " . ucfirst($replacement?->replacement_period ?? '-');
@endphp


     <div class="mb-3">
    <label class="form-label"><strong>Request Details</strong></label>
    <table class="table table-bordered table-sm mb-0">
        <tbody>
            <tr>
                <th width="30%">Original Vehicle</th>
                <td>
                    {{ $replacement?->vehicle
                        ? $replacement->vehicle->registration_number . ' (' . $replacement->vehicle->make . ' ' . $replacement->vehicle->model . ')'
                        : 'Not assigned' }}
                </td>
            </tr>
            <tr>
                <th>Replacement Vehicle</th>
                <td>
                    {{ $replacement?->replacementVehicle
                        ? $replacement->replacementVehicle->registration_number . ' (' . $replacement->replacementVehicle->make . ' ' . $replacement->replacementVehicle->model . ')'
                        : 'Not assigned' }}
                </td>
            </tr>
            <tr>
                <th>Reason</th>
                <td>{{ ucfirst($replacement?->reason ?? '-') }}</td>
            </tr>
            <tr>
                <th>Period</th>
                <td>{{ ucfirst($replacement?->replacement_period ?? '-') }}</td>
            </tr>
        </tbody>
    </table>
</div>





          <!-- Replacement Period -->
          <div class="mb-3">
            <label class="form-label">Replacement Period <span class="text-danger">*</span></label>
            <select class="form-select" wire:model.defer="replacement_period">
               <option value="">--Select--</option>
              <option value="temporary">Temporary</option>
              <option value="permanent">Permanent</option>
            </select>
            @error('replacement_period') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <!-- Expected Return Date -->
          <div class="mb-3">
            <label class="form-label">Expected Return Date</label>
            <input type="date" class="form-control" wire:model.defer="expected_return_date">
            @error('expected_return_date') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <!-- Approval Notes -->
          <div class="mb-3">
            <label class="form-label">Approval Notes</label>
            <textarea class="form-control" wire:model.defer="replacement_approval_notes" rows="3"></textarea>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Approve Replacement</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Replacement Rejection Modal -->
<div wire:ignore.self class="modal fade" id="replacementRejectionModal" tabindex="-1" aria-labelledby="replacementRejectionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="replacementRejectionModalLabel">Reject Replacement Request</h5>
        
      </div>

      <div class="modal-body">
       
     <div class="mb-3">
    <label class="form-label"><strong>Request Details</strong></label>
    <table class="table table-bordered table-sm mb-0">
        <tbody>
            <tr>
                <th width="30%">Original Vehicle</th>
                <td>
                    {{ $replacement?->vehicle
                        ? $replacement->vehicle->registration_number . ' (' . $replacement->vehicle->make . ' ' . $replacement->vehicle->model . ')'
                        : 'Not assigned' }}
                </td>
            </tr>
            <tr>
                <th>Replacement Vehicle</th>
                <td>
                    {{ $replacement?->replacementVehicle
                        ? $replacement->replacementVehicle->registration_number . ' (' . $replacement->replacementVehicle->make . ' ' . $replacement->replacementVehicle->model . ')'
                        : 'Not assigned' }}
                </td>
            </tr>
            <tr>
                <th>Reason</th>
                <td>{{ ucfirst($replacement?->reason ?? '-') }}</td>
            </tr>
            <tr>
                <th>Period</th>
                <td>{{ ucfirst($replacement?->replacement_period ?? '-') }}</td>
            </tr>
        </tbody>
    </table>
</div>

        <!-- Rejection Notes -->
        <div class="mb-3">
          <label class="form-label">Rejection Note</label>
          <textarea class="form-control" wire:model.defer="replacement_rejection_note" rows="3" placeholder="Enter reason for rejection"></textarea>
          @error('replacement_rejection_note') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" wire:click="saveReplacementRejection" class="btn btn-danger">Reject Request</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
