
  <!-- Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">

                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="card-title mb-0 me-auto">Vehicle Details</h5>
                
                
                
                <button type="button" id="dateRange">
                <i class="ri-calendar-line btn btn-outline-secondary"></i>
                </button>
               
   
                    <!-- Status Filter (custom handler) -->
                    <select id="fuel_status" wire:change="filterByVehicle($event.target.value)" class="form-select w-auto bg-light">
                    <option value="">Filter by Status</option>
                    <option value="available">Available</option>
                    <option value="assigned">Assigned</option>
                    <option value="maintenance">Maintenance</option>
                     <option value="out_of_service">Out of Service</option>
                    </select>
                    <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary">
                    <i class="ri-refresh-line"></i></button>    
                

                <div class="input-group" style="max-width: 250px;">
                    <span class="input-group-text"><i class="ri-search-line"></i></span>
                    <input type="text" id="tableSearch" class="form-control" placeholder="Search...">
                </div>

                <button  wire:click="create" class="btn btn-success">
                    <i class="ri-add-line"></i> Add New
                </button>
                
                <button wire:click="exportPdf" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Export
                </button>

                </div>


               



                <div class="card-body">
                   <div wire:loading class="text-center col-12">
    <i class="spinner-border text-primary" role="status"></i>
</div>

    <div wire:loading.remove class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 table-responsive" id="vechicleTable">
                            <thead class="table-light">
                                <tr>
    @foreach ($columns as $column)
        <th style="white-space: nowrap;">{{ $column['label'] }}</th>
    @endforeach
</tr>

                            </thead>
                            <tbody>
                                @forelse ($vehicles as $item)
                                    <tr>
                                        
                                        @foreach ($columns as $column)
                                        
                                        
                                        
                                            <td style="white-space: nowrap;">
                                                @switch($column['column'])
                                                @case('created_at')
                                                {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}
                                                @break
                                                
                                                @case('vehicle_info')
                                                {{ $item->make }} {{ $item->model }}
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
                                                    'in-replacement'=>'dark',
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
                                                        
                                                        <button data-bs-toggle="tooltip" title="Click to Export Report" class="btn btn-outline-primary me-1" wire:click="exportVehicleHistory({{ $item->id }})"> <i class="ri-download-2-line"></i></button>
                                                        
                                                        <button data-bs-toggle="tooltip" title="Click to View" class="btn btn-outline-primary me-1" wire:click="showVehicleDetails({{ $item->id }})"><i class="ri-truck-line"></i></button>
                                                        
                                                        
                                                        
                                                        
                                                        
                                                    
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
<button type="button"class="btn btn-outline-danger me-1" wire:click="openDelete({{ $item->id }})" >
    <span wire:loading.remove wire:target="$set('confirmingDeleteId', {{ $item->id }})">Delete</span>
    <span wire:loading wire:target="$set('confirmingDeleteId', {{ $item->id }})">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
    </span>
</button>



@if (!in_array($item->status, ['out_of_service', 'maintenance']))
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

@if(!in_array($item->status,['in-replacement']))
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
    <button wire:click="make_replace({{ $item->id }})"
        class="btn btn-outline-dark me-1"
        wire:loading.attr="disabled"
        wire:target="make_replace({{ $item->id }})">
        <span wire:loading.remove wire:target="make_replace({{ $item->id }})">Make as Replacement</span>
        <span wire:loading wire:target="make_replace({{ $item->id }})">
            <span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span>
        </span>
    </button>
@endif    
@elseif (!in_array($item->status, ['out_of_service']))
<button wire:click="replace({{ $item->id }})"
class="btn btn-outline-dark me-1"
wire:loading.attr="disabled"
wire:target="replace({{ $item->id }})">
<span wire:loading.remove wire:target="replace({{ $item->id }})">Replace With</span>
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
<div wire:ignore.self class="modal fade" id="make-replacementModal" tabindex="-1" aria-labelledby="make-replacementModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form wire:submit.prevent="saveMakeReplacement">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Make as Replacement</h5>
        </div>
        <div class="modal-body">
          
         


        <div class="mb-3">
    <label class="form-label">Vehicle <span class="text-danger">*</span></label>
    <select class="form-control" wire:model="Mvehicle_id">
        <option value="">-- Select Vehicle --</option>
        @foreach($Mvehicles as $v)
            <option value="{{ $v->id }}">
                {{ $v->make }} / {{ $v->model }}
            </option>
        @endforeach
    </select>
     @error('Mvehicle_id') <small class="text-danger">{{ $message }}</small> @enderror
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
            <label class="form-label">Maintenance Type <span class="text-danger">*</span></label>
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
            <label class="form-label">Description <span class="text-danger">*</span></label>
            <textarea class="form-control" wire:model.defer="description" rows="3"></textarea>
            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Urgency <span class="text-danger">*</span></label>
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
            <label class="form-label">Select Driver <span class="text-danger">*</span></label>
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
            <label class="form-label">Registration Number/Number Plate <span class="text-danger">*</span></label>
            <input type="text" wire:model.defer="registration_number" class="form-control" placeholder="Enter registration number">
            @error('registration_number') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Make/Brand <span class="text-danger">*</span></label>
            <input type="text" wire:model.defer="make" class="form-control" placeholder="Enter make">
            @error('make') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Model/Car Name <span class="text-danger">*</span></label>
            <input type="text" wire:model.defer="model" class="form-control" placeholder="Enter model">
            @error('model') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="col-md-3">
            <label class="form-label">Year <span class="text-danger">*</span></label>
            <input type="number" wire:model.defer="year" class="form-control" placeholder="Enter year">
            @error('year') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="col-md-3">
            <label class="form-label">Fuel Type <span class="text-danger">*</span></label>
            <select wire:model.defer="fuel_type" class="form-select">
              <option value="petrol">Petrol</option>
              <option value="diesel">Diesel</option>
              <option value="electric">Electric</option>
              <option value="hybrid">Hybrid</option>
            </select>
            @error('fuel_type') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Color <span class="text-danger">*</span></label>
            <select id="carColor" aria-label="Car color select" class="form-select" wire:model.defer="color">
              <!-- neutrals -->
              <option value="White">White</option>
              <option value="Pearl White">Pearl White</option>
              <option value="Champagne">Champagne</option>
              <option value="Silver">Silver</option>
              <option value="Metallic Silver">Metallic Silver</option>
              <option value="Gray">Gray</option>
              <option value="Charcoal">Charcoal</option>
              <option value="Black">Black</option>
              <option value="Jet Black">Jet Black</option>

              <!-- classics -->
              <option value="Red">Red</option>
              <option value="Mahogany">Mahogany</option>

              <option value="Navy Blue">Navy Blue</option>
              <option value="Royal Blue">Royal Blue</option>
              <option value="Cobalt Blue">Cobalt Blue</option>
              <option value="Midnight Blue">Midnight Blue</option>
              <option value="Sky Blue">Sky Blue</option>

              <option value="Forest Green">Forest Green</option>

              <option value="Orange">Orange</option>

              <option value="Gold">Gold</option>
              <option value="Bronze">Bronze</option>

              <option value="Brown">Brown</option>
              <option value="Chestnut">Chestnut</option>
              <option value="Beige">Beige</option>

              <option value="Purple">Purple</option>

              <option value="Pink">Pink</option>
              <option value="Rose">Rose</option>

              <option value="Turquoise">Turquoise</option>
              <option value="Teal">Teal</option>
              <option value="Aqua">Aqua</option>

              <option value="Yellow">Yellow</option>

              <!-- trendy / specialty -->
              <option value="Pearl Blue">Pearl Blue</option>
              <option value="Pearl Sand">Pearl Sand</option>

              <!-- sport / vivid -->
              <option value="Racing Red">Racing Red</option>
              <option value="Hot Orange">Hot Orange</option>
              <option value="Neon Green">Neon Green</option>
              <option value="Electric Blue">Electric Blue</option>

              <!-- uncommon -->
              <option value="Moss">Moss</option>
              <option value="Hunter Green">Hunter Green</option>
              <option value="Rustic Brown">Rustic Brown</option>
              <option value="Dusty Rose">Dusty Rose</option>
              <option value="Deep Steel Blue">Deep Steel Blue</option>
          </select>
          @error('color') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          {{-- <div class="col-md-6">
            <label class="form-label">VIN <span class="text-danger">*</span></label>
            <input type="text" wire:model.defer="vin" class="form-control" placeholder="Enter VIN">
            @error('vin') <small class="text-danger">{{ $message }}</small> @enderror
          </div> --}}

          {{-- <div class="col-md-6">
            <label class="form-label">Current Location <span class="text-danger">*</span></label>
            <input type="text" wire:model.defer="current_location" class="form-control" placeholder="Enter current location">
            @error('current_location') <small class="text-danger">{{ $message }}</small> @enderror
          </div> --}}

          {{-- rental company  --}}
          <div class="col-md-6">
            <label class="form-label">Rental Company <span class="text-danger">*</span></label>
            <input type="text" wire:model.defer="rental_company" class="form-control" placeholder="Enter rental company">
            @error('rental_company') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          {{-- company sticker  --}}
          <div class="col-md-6">
            <label class="form-label">Company Sticker <span class="text-danger">*</span></label>
            <select wire:model.defer="company_sticker" class="form-select">
              <option value="no">No</option>
              <option value="yes">Yes</option>
            </select>
            @error('company_sticker') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          {{-- GPS  --}}
          <div class="col-md-6">
            <label class="form-label">GPS <span class="text-danger">*</span></label>
            <select wire:model.defer="gps" class="form-select">
              <option value="installed">installed </option>
              <option value="not installed">not installed</option>
            </select>
            @error('gps') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Mileage <span class="text-danger">*</span></label>
            <input type="number" wire:model.defer="mileage" class="form-control" placeholder="Enter mileage">
            @error('mileage') <small class="text-danger">{{ $message }}</small> @enderror
          </div>


           <div class="col-md-6">
    <label class="form-label">Upload Documents</label>
    <input type="file" wire:model="documents" class="form-control" multiple wire:key="documents-{{ $uploadIteration }}">
    @error('documents.*') <small class="text-danger">{{ $message }}</small> @enderror
    
    <!-- Preview Selected Files -->
    @if($documents)
        <ul class="mt-2 list-unstyled">
            @foreach($documents as $index => $doc)
                <li class="d-flex justify-content-between align-items-center mb-1">
                    <span>{{ $doc->getClientOriginalName() }}</span>
                    <button type="button" 
                            wire:click="removeDocument({{ $index }})" 
                            class="btn btn-sm btn-danger">
                         <i class="ri-trash-line"></i>
                    </button>
                </li>
            @endforeach
        </ul>
    @endif
</div>

             <div class="col-md-6">
            <label class="form-label">Branch <span class="text-danger">*</span></label>
           <select class="form-control" wire:model.defer="branch_id" >
               <option value="">--Select Branch--</option>
               @foreach($branch as $b)
               <option value="{{ $b->id }}">{{ $b->name }}</option>
               @endforeach
           </select>
           @error('branch_id')<small class="text-danger">{{ $message }}</small> @enderror
          </div>
    
          <div class="col-md-6">
            <label class="form-label">Notes</label>
            <textarea wire:model.defer="notes" class="form-control" rows="2" placeholder="Enter notes..."></textarea>
          </div>
          
        
          
          
        </div>
        
  


        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Vehicle' : 'Save Vehicle' }}</button>
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

  

    $(document).ready(function () {

 window.addEventListener('open-delete-modal', function () {
            const modal = $('#confirmDeleteModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });

 window.addEventListener('close-delete-modal', function () {
            const modal = $('#confirmDeleteModal');
            const modalInstance = bootstrap.Modal.getInstance(modal[0]);
            if (modalInstance) modalInstance.hide();
            setTimeout(function () {
                $('#success-alert').hide();
            }, 3000);
        });


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
            const modal = $('#assignVehicleModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });


        window.addEventListener('close-modal', function () {
            const modalEl = $('#assignVehicleModal');
            const modal = bootstrap.Modal.getInstance(modalEl[0]);
            if (modal) modal.hide();
            setTimeout(function () {
                $('#success-alert').hide();
            }, 3000);
        });


         window.addEventListener('show-unassign-modal', function () {
            const modal = $('#unassignModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });


        window.addEventListener('hide-unassign-modal', function () {
            const modal = bootstrap.Modal.getInstance($('#unassignModal')[0]);
            if (modal) modal.hide();
            setTimeout(function () {
                $('#success-alert').hide();
            }, 3000);
        });

         window.addEventListener('show-maintenance-modal', function () {
            const modal = $('#maintenanceModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });

       

        window.addEventListener('hide-maintenance-modal', function () {
            setTimeout(function () {
                $('#success-alert').hide();
            }, 4000);
            const modal = bootstrap.Modal.getInstance($('#maintenanceModal')[0]);
            if (modal) modal.hide();
        });
    });
    
    
    
      window.addEventListener('show-make-replacement-modal', function () {
            const modal = $('#make-replacementModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });


window.addEventListener('hide-make-replacement-modal', () => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('make-replacementModal'));
    if (modal) modal.hide();
    setTimeout(function () {
                $('#success-alert').hide();
            }, 4000);
});


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


 window.addEventListener('show-approval-modal', function () {
            const modal = $('#approveMaintenanceModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
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


 window.addEventListener('show-reject-modal', function () {
            const modal = $('#rejectModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });

window.addEventListener('hide-reject-modal', () => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
    if (modal) modal.hide();
    setTimeout(function () {
                $('#success-alert').hide();
            }, 4000);
});


 window.addEventListener('show-replacement-approval-modal', function () {
            const modal = $('#replacementApprovalModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });

window.addEventListener('hide-replacement-approval-modal', () => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('replacementApprovalModal'));
    if (modal) modal.hide();
     setTimeout(function () {
                $('#success-alert').hide();
            }, 4000);
});

window.addEventListener('hide-alert', () => {
     setTimeout(function () {
                $('#success-alert').hide();
            }, 4000);
});

 window.addEventListener('show-replacement-rejection-modal', function () {
            const modal = $('#replacementRejectionModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });



window.addEventListener('hide-replacement-rejection-modal', () => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('replacementRejectionModal'));
    if (modal) modal.hide();
     setTimeout(function () {
                $('#success-alert').hide();
            }, 4000);
});

 


</script>
<script>
    function bindSearchInputs() {
       // console.log("🔁 Binding search inputs...");

        // Vehicle table search
        $('#tableSearch').off('keyup').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $('#vechicleTable tbody tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Maintenance table search
        $('#tableSearch2').off('keyup').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $('#maintenanceTable tbody tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Replacement table search
        $('#tableSearch3').off('keyup').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $('#replacementTable tbody tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
        

    
    
      flatpickr("#dateRange", {
    mode: "range",
    dateFormat: "Y-m-d",
    onClose: function (selectedDates, dateStr, instance) {
        if (selectedDates.length === 2) {
            const from = instance.formatDate(selectedDates[0], "Y-m-d");

            // Add 1 day to the "to" date
            const toDate = new Date(selectedDates[1]);
            //toDate.setDate(toDate.getDate() + 1);
            const to = instance.formatDate(toDate, "Y-m-d");

            window.Livewire.dispatch('setDateRange', { from, to });
        }
    }
});
    
    
    
    
    
    
    }

    // Initial bind on page load
    document.addEventListener("DOMContentLoaded", function () {
        bindSearchInputs();
    });

    // Re-bind after Livewire DOM update
    window.addEventListener('bind-search-events', function () {
        //console.log("📣 Browser event received, rebinding search...");
        setTimeout(() => {
            bindSearchInputs();
        }, 500); // slight delay ensures DOM is ready
    });
    
 

</script>
@endpush







            </div>
        </div>
    </div>
