<div>
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Vehicle Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">Vehicle</a></li>
                        <li class="breadcrumb-item active">Vehicle Management</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

     @if (session()->has('success'))
        <div id="success-alert" class="alert alert-success my-alert">
        <h2><i class="ri-checkbox-circle-fill"></i> {{ session('success') }}</h2>
        </div>
    @endif

     @if (session()->has('del_success'))
        <div id="del-success-alert" class="alert alert-success my-alert">
        <h2><i class="ri-checkbox-circle-fill"></i> {{ session('del_success') }}</h2>
        </div>
    @endif
    

<ul class="vehicle nav nav-tabs mb-3" role="tablist">
  <li class="nav-item" role="presentation">
    <button type="button"
      class="nav-link @if($activeTab === 'vehicles') active bg-primary text-white @endif"
      wire:click="switchTab('vehicles')"
      role="tab"
      aria-selected="{{ $activeTab === 'vehicles' ? 'true' : 'false' }}">
      Vehicles
    </button>
  </li>

  <li class="nav-item" role="presentation">
    <button type="button"
      class="nav-link @if($activeTab === 'maintenance') active bg-primary text-white @endif"
      wire:click="switchTab('maintenance')"
      role="tab"
      aria-selected="{{ $activeTab === 'maintenance' ? 'true' : 'false' }}">
      Maintenance Requests
    </button>
  </li>

  <li class="nav-item" role="presentation">
    <button type="button"
      class="nav-link @if($activeTab === 'replacement') active bg-primary text-white @endif"
      wire:click="switchTab('replacement')"
      role="tab"
      aria-selected="{{ $activeTab === 'replacement' ? 'true' : 'false' }}">
      Replacement Requests
    </button>
  </li>
</ul>



<!-- Tab Content -->
<div class="tab-content" id="vehicleTabsContent">
  
<!-- Vehicle Tab -->
@if($activeTab === 'vehicles')
  <div class="tab-pane fade show active">
    @include('livewire.vehicle.vehicle-data')
  </div>
@endif

<!-- Maintenance Tab -->
@if($activeTab === 'maintenance')
  <div class="tab-pane fade show active">
    <div class="card shadow-sm">
<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div class="col-lg-12 mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

        <!-- Title on the Left -->
        <h5 class="mb-0 card-title">Maintenance Requests</h5>

        <!-- Controls on the Right -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            
            <div class="">
            <button type="button" id="dateRange2">
            <i class="ri-calendar-line btn btn-outline-secondary"></i>
            </button>
            </div>

            <!-- Filter by Status -->
            <select wire:change="mrFilter($event.target.value)" class="form-select bg-light w-auto">
                <option value="">Filter by Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="completed">Completed</option>
                <option value="rejected">Rejected</option>
            </select>

            <!-- Reset Button -->
            <button type="button" wire:click="resetFilters2" class="btn btn-outline-secondary">
                <i class="ri-refresh-line"></i>
            </button>

            <!-- Search Input -->
            <div class="input-group" style="max-width: 200px;">
                <span class="input-group-text"><i class="ri-search-line"></i></span>
                <input type="text" wire:model.debounce.500ms="search" class="form-control" placeholder="Search..." id="tableSearch2">
            </div>

            <!-- Export PDF Button -->
            <button wire:click="exportPdf2" wire:loading.attr="disabled" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </button>

        </div>
    </div>
</div>

    
</div>

      <div class="card-body">
        @include('livewire.vehicle.maintenance-table')
      </div>
    </div>
  </div>
@endif

<!-- Replacement Tab -->
@if($activeTab === 'replacement')
  <div class="tab-pane fade show active">
    <div class="card shadow-sm">
    
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
       <div class="col-lg-12 mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

        <!-- Title on the Left -->
        <h5 class="card-title mb-0">Replacement Requests</h5>
        
       
        

        <!-- Controls on the Right -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            
            <div class="">
            <button type="button" id="dateRange3">
            <i class="ri-calendar-line btn btn-outline-secondary"></i>
            </button>
            </div>

            <!-- Status Filter -->
            <select id="rep_status" wire:change="repFilter($event.target.value)" class="form-select w-auto bg-light">
                <option value="">Filter by Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="completed">Completed</option>
                <option value="rejected">Rejected</option>
            </select>

            <!-- Reset Button -->
            <button type="button" wire:click="resetFilters3" class="btn btn-outline-secondary">
                <i class="ri-refresh-line"></i>
            </button>

            <!-- Search Input -->
            <div class="input-group" style="max-width: 200px;">
                <span class="input-group-text"><i class="ri-search-line"></i></span>
                <input type="text" wire:model.debounce.500ms="search" class="form-control" placeholder="Search..." id="tableSearch3">
            </div>

            <!-- Export PDF Button -->
            <button wire:click="exportPdf3" wire:loading.attr="disabled" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </button>

        </div>
    </div>
</div>

   
    </div>

      <div class="card-body">
        @include('livewire.vehicle.replacement-table')
      </div>
    </div>
  </div>
@endif


   
       <div class="modal fade" id="vd_modal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Vehicle Details</h5>
                       
                    </div>
                    <div class="modal-body">
                        @if($selectedVehicle)
                    <div class="container">
                         <div class="row mb-2">
                            <div class="col-6 fw-bold">Registration Number</div>
                            <div class="col-6 text-end">{{ $selectedVehicle->registration_number }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 fw-bold">Make</div>
                            <div class="col-6 text-end">{{ $selectedVehicle->make }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 fw-bold">Model</div>
                            <div class="col-6 text-end">{{ $selectedVehicle->model }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 fw-bold">Year</div>
                            <div class="col-6 text-end">{{ $selectedVehicle->year }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 fw-bold">VIN</div>
                            <div class="col-6 text-end">{{ $selectedVehicle->vin }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 fw-bold">Location</div>
                            <div class="col-6 text-end">{{ $selectedVehicle->current_location }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 fw-bold">Status</div>
                            <div class="col-6 text-end text-capitalize">{{ $selectedVehicle->status }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 fw-bold">Fuel Type</div>
                            <div class="col-6 text-end">{{ $selectedVehicle->fuel_type }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 fw-bold">Mileage</div>
                            <div class="col-6 text-end">{{ $selectedVehicle->mileage }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 fw-bold">Notes</div>
                            <div class="col-6 text-end">{{ $selectedVehicle->notes }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 fw-bold">Created At</div>
                            <div class="col-6 text-end">{{ $selectedVehicle->created_at->format('d-m-Y') }}</div>
                        </div>
                        
@if($selectedVehicle && $selectedVehicle->documents->count())
    <h6 class="mt-3">Documents</h6>
    <ul class="list-group">
        @foreach($selectedVehicle->documents as $index => $doc)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold">{{ $index + 1 }}.</span>
                    {{ $doc->file_name }}
                </div>
                <div>
                    <!-- View -->
                    <a href="{{ asset('storage/app/public/' . $doc->file_path) }}" target="_blank" 
                       class="btn btn-sm btn-info me-1" title="View">
                        <i class="ri-eye-line"></i>
                    </a>

                    <!-- Download -->
                    <a href="{{ asset('storage/app/public/' . $doc->file_path) }}" download 
                       class="btn btn-sm btn-success" title="Download">
                        <i class="ri-download-line"></i>
                    </a>
                </div>
            </li>
        @endforeach
    </ul>
@else
    <p class="text-muted mt-2">No documents uploaded for this vehicle.</p>
@endif



                      
                    </div>
                        @endif
                </div>

                    <div class="modal-footer">
                        <button  type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="$set('showModal', false)">Close</button>
                    </div>
                </div>
            </div>
        </div>






</div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('#vehicleTabs .nav-link');

        tabButtons.forEach(btn => {
            btn.addEventListener('shown.bs.tab', function (e) {
                // Remove styles from all tabs
                tabButtons.forEach(b => b.classList.remove('bg-primary', 'text-white'));

                // Add to the newly activated tab
                e.target.classList.add('bg-primary', 'text-white');
            });
        });
    });
    
       function resetFilters() {
        document.getElementById('fuel_status').value = '';
        document.getElementById('fuel_status').dispatchEvent(new Event('input'));
        
    }
    
    function resetFilters2() {
        
         document.getElementById('main_status').value = '';
        document.getElementById('main_status').dispatchEvent(new Event('input'));

    }
    
    function resetFilters3() {
      
         document.getElementById('rep_status').value = '';
        document.getElementById('rep_status').dispatchEvent(new Event('input'));
    }

     window.addEventListener('reset-filter', () => {
        resetFilters();
        
    });
    
     window.addEventListener('reset-filter2', () => {
     
        resetFilters2();

    });
    
    
     window.addEventListener('reset-filter3', () => {
    
        resetFilters3();
    });
    
    
window.addEventListener('show-vDetail-modal', function () {
const modal = $('#vd_modal');
const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
modalInstance.show();
});
    


 function bindSearchInputs3() {
     
     
flatpickr("#dateRange3", {
    mode: "range",
    dateFormat: "Y-m-d",
    onClose: function (selectedDates, dateStr, instance) {
        if (selectedDates.length === 2) {
            const from = instance.formatDate(selectedDates[0], "Y-m-d");

            // Add 1 day to the "to" date
            const toDate = new Date(selectedDates[1]);
           // toDate.setDate(toDate.getDate() + 1);
            const to = instance.formatDate(toDate, "Y-m-d");

            window.Livewire.dispatch('setDateRange3', { from, to });
        }
    }
});


     
  flatpickr("#dateRange2", {
    mode: "range",
    dateFormat: "Y-m-d",
    onClose: function (selectedDates, dateStr, instance) {
        if (selectedDates.length === 2) {
            const from = instance.formatDate(selectedDates[0], "Y-m-d");

            // Add 1 day to the "to" date
            const toDate = new Date(selectedDates[1]);
            //toDate.setDate(toDate.getDate() + 1);
            const to = instance.formatDate(toDate, "Y-m-d");

            window.Livewire.dispatch('setDateRange2', { from, to });
        }
    }
});
   
     
     
     
 }




  // Initial bind on page load
    document.addEventListener("DOMContentLoaded", function () {
        bindSearchInputs3();
    });

    // Re-bind after Livewire DOM update
    window.addEventListener('bind-search-events', function () {
        //console.log("📣 Browser event received, rebinding search...");
        setTimeout(() => {
            bindSearchInputs3();
        }, 500); // slight delay ensures DOM is ready
    });
    
    
</script>


