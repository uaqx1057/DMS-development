<div>
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Fuel Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">Fuel</a></li>
                        <li class="breadcrumb-item active">Fuel Management</li>
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
      class="nav-link @if($activeTab === 'fuel_request') active bg-primary text-white @endif"
      wire:click="switchTab('fuel_request')"
      role="tab"
      aria-selected="{{ $activeTab === 'fuel_request' ? 'true' : 'false' }}">
      Fuel Request
    </button>
  </li>

  <li class="nav-item" role="presentation">
    <button type="button"
      class="nav-link @if($activeTab === 'fuel_expenses') active bg-primary text-white @endif"
      wire:click="switchTab('fuel_expenses')"
      role="tab"
      aria-selected="{{ $activeTab === 'fuel_expenses' ? 'true' : 'false' }}">
      Fuel Expenses
    </button>
  </li>

  <li class="nav-item" role="presentation">
    <button type="button"
      class="nav-link @if($activeTab === 'fuel_reports') active bg-primary text-white @endif"
      wire:click="switchTab('fuel_reports')"
      role="tab"
      aria-selected="{{ $activeTab === 'fuel_reports' ? 'true' : 'false' }}">
      Reports
    </button>
  </li>
</ul>



<!-- Tab Content -->
<div class="tab-content" id="vehicleTabsContent">
  
<!-- Vehicle Tab -->
@if($activeTab === 'fuel_request')
 <div class="tab-pane fade show active">
    <div class="card shadow-sm">
    
    <div class="card-header">
            <div class="row">
                
                <div class="col-lg-2 col-sm-12">
                    <h5 class="card-title py-2">Fuel Request</h5>
                </div>
                 <div class="col-lg-6 col-sm-12">
                    <div class="d-flex gap-2">
                    <!-- Fuel Type Filter -->
                    <select id="fuel_type" wire:change="filterByFuelType($event.target.value)" class="form-select w-auto bg-light ">
                    <option value="">Filter by Fuel Type</option>
                    <option value="petrol">Petrol</option>
                    <option value="diesel">Diesel</option>
                    <option value="cng">CNG</option>
                    <option value="electric">Electric</option>
                    </select>

                    <!-- Status Filter (custom handler) -->
                    <select id="fuel_status" wire:change="filterByStatus($event.target.value)" class="form-select w-auto bg-light">
                    <option value="">Filter by Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    </select>
                    <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary">
                    <i class="ri-refresh-line"></i></button>

                    </div>
                </div>
                 <div class="col-lg-4 col-sm-12 text-end">
                    <button  wire:click="create" class="btn btn-success">
                    <i class="ri-add-line"></i> Request Fuel
                    </button>
                    <button  wire:click="exportCsv" class="btn btn-secondary">
                    <i class="ri-file-download-line"></i> Export
                    </button>
                </div>


            </div>
   
   
    </div>

      <div class="card-body">
        @include('livewire.fuel.request')
      </div>
    </div>
  </div>
@endif

<!-- Maintenance Tab -->
@if($activeTab === 'fuel_expenses')
  <div class="tab-pane fade show active">
    <div class="card shadow-sm">
<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h5 class="card-title mb-0 me-auto">Fuel Expenses</h5>

    <button  wire:click="createExpense" class="btn btn-success">
        <i class="ri-add-line"></i> Record Expense
    </button>
    <button  wire:click="exportExpensesCsv" class="btn btn-secondary">
        <i class="ri-file-download-line"></i> Export
    </button>
    </div>

      <div class="card-body">
        @include('livewire.fuel.expenses')
      </div>
    </div>
  </div>
@endif

<!-- Replacement Tab -->
@if($activeTab === 'fuel_reports')
  <div class="tab-pane fade show active">
    <div class="card shadow-sm">
    
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h5 class="card-title mb-0">Fuel Reports</h5>

    <div class="d-flex gap-2">
        <button wire:click="callGenerateModal" class="btn btn-success">
            <i class="ri-file-chart-line"></i> Generate Report
        </button>

        @if($byVehicle && $byVehicle->count())
            <button onclick="window.dispatchEvent(new Event('ExportReportCSV'))" class="btn btn-primary">
                <i class="ri-file-download-line"></i> Export CSV
            </button>
        @endif
    </div>
</div>


      <div class="card-body">
        @include('livewire.fuel.reports')
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



      function resetFilters() {
        document.getElementById('fuel_type').value = '';
        document.getElementById('fuel_status').value = '';

        // If you're using Livewire, also trigger input event so Livewire detects changes:
        document.getElementById('fuel_type').dispatchEvent(new Event('input'));
        document.getElementById('fuel_status').dispatchEvent(new Event('input'));
    }

     window.addEventListener('reset-filter', () => {
        resetFilters();
    });
</script>
@push('scripts')
<script>
  function closeAlert()
  {
      setTimeout(function () {
      $('#success-alert').hide();
      }, 3000);
  }


    window.addEventListener('close-expense-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('fuelExpenseModal'));
        if (modal) modal.hide();
        closeAlert();
    });

    window.addEventListener('show-add-expense-modal', function () {
            const modal = $('#fuelExpenseModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });

  window.addEventListener('download-expense-csv', function () {
        window.location.href = "{{ route('fuel.expense') }}";
    });  

  window.addEventListener('show-expense-details-modal', function () {
            const modal = $('#expenseDetailsModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });


    window.addEventListener('open-generate-modal', function () {
            const modal = $('#fuelReportModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modal[0]);
            modalInstance.show();
        });


  window.addEventListener('close-generate-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('fuelReportModal'));
        if (modal) modal.hide();
        
    });

window.addEventListener('ExportReportCSV', function () {
    const downloadLink = document.createElement('a');
    downloadLink.href = "{{ route('fuel.report.export') }}";
    downloadLink.setAttribute('download', '');
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
});


</script>
@endpush