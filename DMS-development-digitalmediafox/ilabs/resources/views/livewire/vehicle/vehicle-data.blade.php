  <!-- Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Vehicle Details</h5>
                    
                        <button data-bs-toggle="modal" data-bs-target="#addVehicleModal" wire:click="create" class="btn btn-sm btn-primary"><i class="ri-add-line"></i> Add New Vehicle</button>
                   
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    @foreach ($columns as $column)
                                        <th>{{ $column['label'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vehicles as $item)
                                    <tr>
                                        @foreach ($columns as $column)
                                            <td>
                                                @switch($column['column'])
                                                @case('vehicle_info')
                                                {{ $item->make }} / {{ $item->model }}
                                                <br>
                                                <small>({{ $item->year }})</small>
                                                @break

                                                        

                                                    @case('status')
                                                    @php
                                                    $color = match($item->status) {
                                                    'available' => 'success',
                                                    'assigned' => 'secondary',
                                                    'out_of_service' => 'danger',
                                                    default => 'primary',
                                                    };

                                                    $label = $item->status === 'out_of_service'
                                                    ? 'Out of Service'
                                                    : ucfirst(strtolower($item->status));
                                                    @endphp

                                                    <span class="badge bg-{{ $color }} rounded-pill">{{ $label }}</span>
                                                    @break

                                                    @case('driver_name')
                                                    {{ $item->driver_name ?? 'Unassigned' }}
                                                    @break

                                                    @case('action')
                                                    <div class="btn-group btn-group-sm">
                                                    
                                                <button
                                                    wire:click="edit({{ $item->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="edit({{ $item->id }})"
                                                    class="btn btn-outline-warning me-1"
                                                >
                                                    <span wire:loading.remove wire:target="edit({{ $item->id }})">Edit</span>

                                                    <!-- Spinner shown while loading -->
                                                    <span wire:loading wire:target="edit({{ $item->id }})">
                                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    </span>
                                                </button>
                                                <!-- Delete Button -->
<!-- Delete Button -->
<button type="button"
    class="btn btn-outline-danger me-1"
    wire:click="$set('confirmingDeleteId', {{ $item->id }})"
    wire:loading.attr="disabled"
    wire:target="$set('confirmingDeleteId', {{ $item->id }})"
    data-bs-toggle="modal"
    data-bs-target="#confirmDeleteModal">

    <span wire:loading.remove wire:target="$set('confirmingDeleteId', {{ $item->id }})">Delete</span>
    <span wire:loading wire:target="$set('confirmingDeleteId', {{ $item->id }})">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
    </span>
</button>

@if ($item->status !== 'maintenance')
    <!-- Assign / Unassign Button -->
    @if ($item->driver_id)
        <button wire:click="confirmUnassign({{ $item->id }})"
            class="btn btn-outline-danger me-1"
            wire:loading.attr="disabled"
            wire:target="confirmUnassign({{ $item->id }})">
            <span wire:loading.remove wire:target="confirmUnassign({{ $item->id }})">Unassign</span>
            <span wire:loading wire:target="confirmUnassign({{ $item->id }})">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </span>
        </button>
    @else
        <button wire:click="assign({{ $item->id }})"
            class="btn btn-outline-primary me-1"
            wire:loading.attr="disabled"
            wire:target="assign({{ $item->id }})">
            <span wire:loading.remove wire:target="assign({{ $item->id }})">Assign</span>
            <span wire:loading wire:target="assign({{ $item->id }})">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </span>
        </button>
    @endif

    <!-- Maintenance Button -->
    <button wire:click="maintenance({{ $item->id }})"
        class="btn btn-outline-info me-1"
        wire:loading.attr="disabled"
        wire:target="maintenance({{ $item->id }})">
        <span wire:loading.remove wire:target="maintenance({{ $item->id }})">Maintenance</span>
        <span wire:loading wire:target="maintenance({{ $item->id }})">
            <span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span>
        </span>
    </button>

    <!-- Replacement Button -->
    <button wire:click="replace({{ $item->id }})"
        class="btn btn-outline-dark me-1"
        wire:loading.attr="disabled"
        wire:target="replace({{ $item->id }})">
        <span wire:loading.remove wire:target="replace({{ $item->id }})">Replacement</span>
        <span wire:loading wire:target="replace({{ $item->id }})">
            <span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span>
        </span>
    </button>
@endif

                                                        @break

                                                    @default
                                                        {{ $item->{$column['column']} }}
                                                @endswitch
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($columns) }}" class="text-center text-muted">No vehicles found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- Pagination links -->
                        <div class="mt-3">
                            {{ $vehicles->links() }}
                        </div>
                    </div> <!-- table-responsive -->
                </div> <!-- card-body -->

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
            <label class="form-label">Reason for Replacement</label>
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
            <label class="form-label">Replacement Period</label>
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

<!-- Maintenance Request Modal -->
<div wire:ignore.self class="modal fade" id="maintenanceModal" tabindex="-1" aria-labelledby="maintenanceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form wire:submit.prevent="saveMaintenance">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Request Maintenance</h5>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label">Vehicle</label>
            <input type="text" class="form-control" disabled wire:model.defer="vehicle_name">
          </div>

          <div class="mb-3">
            <label class="form-label">Maintenance Type</label>
            <select class="form-select" wire:model.defer="maintenance_type">
              <option value="">-- Select --</option>
              <option value="regular_service">Regular Service</option>
              <option value="repair">Repair</option>
              <option value="inspection">Inspection</option>
              <option value="tire_change">Tire Change</option>
              <option value="battery_replacement">Battery Replacement</option>
              <option value="other">Other</option>
            </select>
            @error('maintenance_type') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" wire:model.defer="description" rows="3"></textarea>
            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Urgency</label>
            <select class="form-select" wire:model.defer="urgency">
              <option value="">-- Select --</option>
              <option value="low">Low</option>
              <option value="normal">Normal</option>
              <option value="high">High</option>
              <option value="critical">Critical</option>
            </select>
            @error('urgency') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit Request</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="$set('showMaintenanceModal', false)">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>



<!-- Unassign Modal -->
<div wire:ignore.self class="modal fade" id="unassignModal" tabindex="-1" aria-labelledby="unassignModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Unassign</h5>
      </div>
      <div class="modal-body">
        Are you sure you want to unassign the driver from this vehicle?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" wire:click="unassign" class="btn btn-danger">Unassign</button>
      </div>
    </div>
  </div>
</div>


<!-- Confirm Delete Modal -->
<div wire:ignore.self class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                
            </div>
            <div class="modal-body">
                Are you sure you want to delete this vehicle?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" wire:click="confirmDelete">Delete</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="dmcb">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Vehicle Modal -->
<div wire:ignore.self class="modal fade" id="assignVehicleModal" tabindex="-1" aria-labelledby="assignVehicleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form wire:submit.prevent="saveAssignment">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Assign Vehicle to Driver</h5>
         
        </div>
        <div class="modal-body">
          
          <div class="mb-3">
            <label class="form-label">Vehicle</label>
            <input type="text" class="form-control" disabled wire:model="vehicle_name">
          </div>

          <div class="mb-3">
            <label class="form-label">Select Driver</label>
            <select class="form-select" wire:model="driver_id">
              <option value="">-- Choose Driver --</option>
              @foreach($drivers as $driver)
                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
              @endforeach
            </select>
            @error('driver_id') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Assign Date</label>
            <input type="date" class="form-control" disabled wire:model="assign_date">
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Assign</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="$set('showAssignModal', false)">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

    

                <!-- Add New Vehicle Modal -->
<div wire:ignore.self class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form wire:submit.prevent="save">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title"> {{ $isEdit ? 'Edit Vehicle' : 'Add New Vehicle' }}</h2>
         
        </div>

       
        <div class="modal-body row g-3">
          <div class="col-md-6">
            <label class="form-label">Registration Number</label>
            <input type="text" wire:model.defer="registration_number" class="form-control">
            @error('registration_number') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Make</label>
            <input type="text" wire:model.defer="make" class="form-control">
            @error('make') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Model</label>
            <input type="text" wire:model.defer="model" class="form-control">
            @error('model') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="col-md-3">
            <label class="form-label">Year</label>
            <input type="number" wire:model.defer="year" class="form-control">
            @error('year') <small class="text-danger">{{ $message }}</small> @enderror
          </div>


          <div class="col-md-3">
             <label class="form-label">Fuel Type</label>
            <select wire:model.defer="fuel_type" class="form-select">
              <option value="petrol">Petrol</option>
              <option value="diesel">Diesel</option>
              <option value="electric">Electric</option>
              <option value="hybrid">Hybrid</option>
            </select>
            @error('fuel_type') <small class="text-danger">{{ $message }}</small> @enderror
          </div>


          <div class="col-md-6">
            <label class="form-label">VIN</label>
            <input type="text" wire:model.defer="vin" class="form-control">
             @error('vin') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Current Location</label>
            <input type="text" wire:model.defer="current_location" class="form-control">
             @error('current_location') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Mileage</label>
            <input type="number" wire:model.defer="mileage" class="form-control">
             @error('mileage') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Status</label>
            <select wire:model.defer="status" class="form-select">
              <option value="available">Available</option>
              <option value="assigned">Assigned</option>
              <option value="maintenance">Maintenance</option>
              <option value="out_of_service">Out of Service</option>
            </select>
            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
          </div>




          <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea wire:model.defer="notes" class="form-control" rows="2"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">  {{ $isEdit ? 'Update Vehicle' : 'Save Vehicle' }}</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="$set('showModal', false)">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('scripts')

<script>
    function modelHide(id) {
        const cancelBtn = $('#' + id);
        if (cancelBtn.length) {
            cancelBtn.click();
        } else {
            console.error('Cancel button not found');
        }
    }

    document.addEventListener('livewire:init', function () {
        Livewire.on('close-add-modal', function () {
            setTimeout(function () {
                $('#success-alert').hide();
            }, 3000);
        });

        Livewire.on('close-delete-modal', function () {
            modelHide('dmcb');
            setTimeout(function () {
                $('#success-alert').hide();
            }, 3000);
        });
    });

    $(document).ready(function () {
        window.addEventListener('show-add-edit-modal', function () {
            const modal = $('#addVehicleModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });

        
        window.addEventListener('hide-add-edit-modal', function () {
            const modal = $('#addVehicleModal');
            const modalInstance = bootstrap.Modal.getInstance(modal[0]);
            if (modalInstance) modalInstance.hide();
            setTimeout(function () {
                $('#success-alert').hide();
            }, 3000);
        });

        window.addEventListener('open-assign-modal', function () {
            const modal = new bootstrap.Modal($('#assignVehicleModal')[0]);
            modal.show();
        });

        window.addEventListener('close-modal', function () {
            const modalEl = $('#assignVehicleModal');
            const modal = bootstrap.Modal.getInstance(modalEl[0]);
            if (modal) modal.hide();
        });

        window.addEventListener('show-unassign-modal', function () {
            const modal = new bootstrap.Modal($('#unassignModal')[0]);
            modal.show();
        });

        window.addEventListener('hide-unassign-modal', function () {
            const modal = bootstrap.Modal.getInstance($('#unassignModal')[0]);
            if (modal) modal.hide();
            setTimeout(function () {
                $('#success-alert').hide();
            }, 3000);
        });

        window.addEventListener('show-maintenance-modal', function () {
            const modal = new bootstrap.Modal($('#maintenanceModal')[0]);
            modal.show();
        });

        window.addEventListener('hide-maintenance-modal', function () {
            setTimeout(function () {
                $('#success-alert').hide();
            }, 4000);
            const modal = bootstrap.Modal.getInstance($('#maintenanceModal')[0]);
            if (modal) modal.hide();
        });
    });


    window.addEventListener('show-replacement-modal', () => {
    const modal = new bootstrap.Modal(document.getElementById('replacementModal'));
    modal.show();
});

window.addEventListener('hide-replacement-modal', () => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('replacementModal'));
    if (modal) modal.hide();
    setTimeout(function () {
                $('#success-alert').hide();
            }, 4000);
});

window.addEventListener('show-approval-modal', () => {
    const modal = new bootstrap.Modal(document.getElementById('approveMaintenanceModal'));
    modal.show();
});
window.addEventListener('hide-approval-modal', () => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('approveMaintenanceModal'));
    if (modal) modal.hide();
    setTimeout(function () {
                $('#success-alert').hide();
            }, 4000);
});

window.addEventListener('show-complete-modal', () => {
    new bootstrap.Modal(document.getElementById('completeMaintenanceModal')).show();
});

window.addEventListener('hide-complete-modal', () => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('completeMaintenanceModal'));
    if (modal) modal.hide();
     setTimeout(function () {
                $('#success-alert').hide();
            }, 4000);
});


</script>

@endpush



            </div>
        </div>
    </div>
