@if ($maintenanceRequests->isEmpty())
    <p class="text-muted">No maintenance requests found.</p>
@else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
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
                        <td>{{ $request->vehicle->make }} / {{ $request->vehicle->model }}</td>
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

                       <td>{{ auth()->user()->name ?? 'Admin' }}</td> {{-- fallback in case user is null --}}
                        <td>{{ $request->created_at->format('Y-m-d') }}</td>

                        <td>
                        <div class="btn-group btn-group-sm">
                        @if ($request->status === 'approved')
                        <button wire:click="complete({{ $request->id }})" class="btn btn-info">
                        <i class="ri-tools-line"></i> Complete Maintenance
                        </button>
                        @else
                        <button wire:click="approve({{ $request->id }})" class="btn btn-success">
                        <i class="ri-check-line"></i> Approve
                        </button>
                        <button wire:click="reject({{ $request->id }})" class="btn btn-danger">
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
          <p><strong>Request Details:</strong></p>
          <p>Vehicle: {{ optional(\App\Models\VehicleMaintenance::find($selectedMaintenanceId)?->vehicle)->make ?? '-' }}</p>
          <p>Type: {{ \App\Models\VehicleMaintenance::find($selectedMaintenanceId)?->maintenance_type ?? '-' }}</p>
          <p>Description: {{ \App\Models\VehicleMaintenance::find($selectedMaintenanceId)?->description ?? '-' }}</p>
          <p>Urgency: {{ ucfirst(\App\Models\VehicleMaintenance::find($selectedMaintenanceId)?->urgency ?? '-') }}</p>

          <div class="mb-3 mt-2">
            <label class="form-label">Estimated Cost</label>
            <input type="number" step="0.01" wire:model.defer="estimated_cost" class="form-control">
            @error('estimated_cost') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Scheduled Date</label>
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

<div wire:ignore.self class="modal fade" id="completeMaintenanceModal" tabindex="-1" aria-labelledby="completeMaintenanceLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form wire:submit.prevent="saveCompletion">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Complete Maintenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="$set('completeModal', false)"></button>
                </div>
                <div class="modal-body">

                   <table class="table table-bordered">
    <tbody>
        <tr>
            <th>Vehicle</th>
            <td>{{ optional(\App\Models\VehicleMaintenance::find($selectedMaintenanceId)?->vehicle)->make ?? '-' }}</td>
        </tr>
        <tr>
            <th>Type</th>
            <td>{{ \App\Models\VehicleMaintenance::find($selectedMaintenanceId)?->maintenance_type ?? '-' }}</td>
        </tr>
        <tr>
            <th>Description</th>
            <td>{{ \App\Models\VehicleMaintenance::find($selectedMaintenanceId)?->description ?? '-' }}</td>
        </tr>
        <tr>
            <th>Urgency</th>
            <td>{{ ucfirst(\App\Models\VehicleMaintenance::find($selectedMaintenanceId)?->urgency ?? '-') }}</td>
        </tr>
    </tbody>
</table>


                    <div class="mb-3 mt-2">
                        <label class="form-label">Resolution Details</label>
                        <textarea class="form-control" wire:model.defer="resolution_details" rows="3"></textarea>
                        @error('resolution_details') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cost Incurred</label>
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
