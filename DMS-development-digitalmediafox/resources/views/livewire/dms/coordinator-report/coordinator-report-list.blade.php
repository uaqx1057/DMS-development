<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />
<!-- Import Modal -->
<div wire:ignore.self class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <form wire:submit.prevent="import">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="file" wire:model="csv_file" accept=".csv" class="form-control">
                    
                    <div class="d-flex align-items-center">
                    @error('csv_file') <span class="text-danger">{{ $message }}</span> @enderror
                     <span class="ms-2 text-muted small">Upload only .csv file format</span>
                    
                    </div>
                    
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Import</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

    <x-ui.message />
    <livewire:modal />
    <x-ui.row>
        {{-- Filter Section --}}
        <div class="row">
            <x-ui.col class="mb-3 col-lg-3 col-md-3">
                <x-form.label for="driver_id" name="Date Range"/>
                <input type="text" id="daterangepicker" class="form-control" placeholder="Select Date Range"/>
            </x-ui.col>
             <!-- Begin: Driver Card -->
             <x-ui.col class="mb-3 col-lg-3 col-md-3">
                <x-form.label for="driver_id" name="Drivers"/>
                <x-form.select class="form-select" wire:model="driver_id" class="js-example-basic-single">
                    <x-form.option value="" name="--select driver--"/>
                    @foreach ($drivers as $item)
                        @php $name = $item->name . ' ( ' . $item->iqaama_number . ' )'; @endphp
                        <x-form.option value="{{ $item->id }}" :name="$name"/>
                    @endforeach
                </x-form.select>
                <x-ui.alert error="driver_id"/>
            </x-ui.col>
            <!-- End: Driver Card -->
            <!-- Begin: Business Card -->
            <x-ui.col class="mb-3 col-lg-3 col-md-3">
                <x-form.label for="business_id" name="Businesss"/>
                <x-form.select wire:model.lazy="business_id">
                    <x-form.option value="" name="--select Business--"/>
                    @foreach ($businesses as $item)
                        @php $name = $item->name; @endphp
                        <x-form.option value="{{ $item->id }}" :name="$name"/>
                    @endforeach
                </x-form.select>
                <x-ui.alert error="business_id"/>
            </x-ui.col>
            <!-- End: Business Card -->
            <!-- Begin: Branch Card -->
            <x-ui.col class="mb-3 col-lg-3 col-md-3">
                <x-form.label for="branch_id" name="Branches"/>
                <x-form.select class="form-select" wire:model.lazy="branch_id">
                    <x-form.option value="" name="--select branch--"/>
                    @foreach ($branches as $branch)
                        <x-form.option value="{{ $branch->id }}" :name="$branch->name"/>
                    @endforeach
                </x-form.select>
                <x-ui.alert error="branch_id"/>
            </x-ui.col>
            <!-- End: Branch Card -->
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card crm-widget">
                    <div class="p-0 card-body">
                        <div class="row row-cols-md-3 row-cols-1">

                        </div><!-- end row -->
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
        {{-- Stats Section --}}
        <div class="row">
            <div class="col-xl-12">
                <div class="card crm-widget">
                    <div class="p-0 card-body">
                        <div class="row row-cols-md-3 row-cols-1">
                            <x-ui.report-widget-card text="Pending Reports" icon="ri-exchange-dollar-line" value="{{ $pendingReportsCount }}"/>
                            <x-ui.report-widget-card text="Approved Reports" icon="ri-space-ship-line" value="{{ $completedReportsCount }}"/>
                            <x-ui.report-widget-card text="Total Orders" icon="ri-pulse-line" value="{{ $totalOrdersCount }}"/>
                        </div><!-- end row -->
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
        
        
        <x-ui.col class="col-lg-12">
            <x-ui.card>


                <x-ui.card-header title="Coordinator Report List" :href="route('coordinator-report.create')" :add="$add_permission" :import=true :export="$coordinatorReports->count() > 0">
                    @if($coordinatorReports->count() > 0)
                        <div class="d-flex gap-2">
                            <button wire:click="exportCsv" class="btn btn-secondary btn-sm">
                                <i class="ri-file-excel-2-line align-bottom me-1"></i> Export CSV
                            </button>
                            <button wire:click="exportPdf" class="btn btn-danger btn-sm">
                                <i class="ri-file-pdf-line align-bottom me-1"></i> Export PDF
                            </button>
                        </div>
                    @endif
                </x-ui.card-header>
                <x-ui.card-body>
                    <x-table
                    :columns="$columns"
                    :page="$page" :
                    :perPage="$perPage"
                    :items="$coordinatorReports"
                    :sortColumn="$sortColumn"
                    :sortDirection="$sortDirection"
                    isModalEdit="true"
                    routeEdit="coordinator-report.edit"
                    :edit_permission="$edit_permission"
                    :showModal="true"
                />
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
        
    </x-ui.row>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            $(document).ready(function() {
                $('.js-example-basic-single').select2({
            allowClear: true
            }).on('change', function(e) {
                @this.set($(this).attr('wire:model'), e.target.value);
            });

                // Initialize Flatpickr for date range
                $('#daterangepicker').flatpickr({
                    mode: 'range',
                    dateFormat: "Y-m-d",
                    onClose: function(selectedDates, dateStr, instance) {
                        // You can handle date selection here if needed
                        console.log('Selected range:', dateStr);
                        @this.set('date_range', dateStr); // Wire model for date range
                    }
                });
            });
            



    // 🔹 Listen for Livewire event to close modal
    window.addEventListener('import-close-modal', () => {
        const modalEl = document.getElementById('importModal');
        if (!modalEl) return;

        let modal = bootstrap.Modal.getInstance(modalEl);
        if (!modal) {
            modal = new bootstrap.Modal(modalEl);
        }

        modal.hide();

        // Dispose instance + cleanup after hidden
        modalEl.addEventListener('hidden.bs.modal', () => {
            modal.dispose();
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }, { once: true });
    });

    // 🔹 Cleanup on Livewire navigation (when leaving/coming back)
    document.addEventListener("livewire:navigated", () => {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });


        </script>
    @endpush

</div>
