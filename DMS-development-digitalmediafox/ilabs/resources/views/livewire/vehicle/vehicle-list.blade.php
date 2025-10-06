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
      wire:click="$set('activeTab', 'vehicles')"
      role="tab"
      aria-selected="{{ $activeTab === 'vehicles' ? 'true' : 'false' }}">
      Vehicles
    </button>
  </li>

  <li class="nav-item" role="presentation">
    <button type="button"
      class="nav-link @if($activeTab === 'maintenance') active bg-primary text-white @endif"
      wire:click="$set('activeTab', 'maintenance')"
      role="tab"
      aria-selected="{{ $activeTab === 'maintenance' ? 'true' : 'false' }}">
      Maintenance Requests
    </button>
  </li>

  <li class="nav-item" role="presentation">
    <button type="button"
      class="nav-link @if($activeTab === 'replacement') active bg-primary text-white @endif"
      wire:click="$set('activeTab', 'replacement')"
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
      <div class="card-header">
        <h5 class="card-title mb-0">Maintenance Requests</h5>
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
      <div class="card-header">
        <h5 class="card-title mb-0">Replacement Requests</h5>
      </div>
      <div class="card-body">
        @include('livewire.vehicle.replacement-table')
      </div>
    </div>
  </div>
@endif


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
</script>
