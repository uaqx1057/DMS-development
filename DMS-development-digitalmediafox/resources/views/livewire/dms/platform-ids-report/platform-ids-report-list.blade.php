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

    <!-- ✅ Add Platform IDs Report Modal Component -->
    <livewire:d-m-s.platform-ids-report.platform-ids-report-modal />

    <x-ui.message />
    
    <x-ui.row>
        {{-- Filter Section --}}
        <div class="row">
            <x-ui.col class="mb-3 col-lg-4 col-md-4">
                <x-form.label for="driver_id" name="Date Range"/>
                <input type="text" id="daterangepicker" class="form-control" placeholder="Select Date Range"/>
            </x-ui.col>
            
            <!-- Begin: Business Card -->
            <x-ui.col class="mb-3 col-lg-4 col-md-4">
                <x-form.label for="business_id" name="Platform"/>
                <x-form.select wire:model.lazy="business_id">
                    <x-form.option value="" name="--select Platform--"/>
                    @foreach ($businesses as $item)
                        @php $name = $item->name; @endphp
                        <x-form.option value="{{ $item->id }}" :name="$name"/>
                    @endforeach
                </x-form.select>
                <x-ui.alert error="business_id"/>
            </x-ui.col>
            <!-- End: Business Card -->
            
            <!-- Begin: Platform IDs -->
            <x-ui.col class="mb-3 col-lg-4 col-md-4">
                <x-form.label for="business_id_value" name="Platform IDs"/>
                <x-form.select wire:model="business_id_value" class="form-select js-example-basic-single">
                    <x-form.option value="" name="-- Select Platform ID --"/>
                    @foreach ($businessIds as $item)
                        <x-form.option value="{{ $item->id }}" :name="$item->value"/>
                    @endforeach
                </x-form.select>
                <x-ui.alert error="business_id_value"/>
            </x-ui.col>
            <!-- End: Platform IDs -->

            <!-- Begin: Branch Card (Hidden) -->
            <x-ui.col class="mb-3 col-lg-3 col-md-3 d-none">
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
                            <!-- Add statistics here if needed -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Platform Ids Report List" :import=false :export="$coordinatorReports->count() > 0"/>
                <x-ui.card-body>
                    <x-table
                        :columns="$columns"
                        :page="$page"
                        :perPage="$perPage"
                        :items="$coordinatorReports"
                        :sortColumn="$sortColumn"
                        :sortDirection="$sortDirection"
                        isModalEdit="true"
                        :showModal="true"
                        :isHtml="true"
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
                        console.log('Selected range:', dateStr);
                        @this.set('date_range', dateStr);
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

                modalEl.addEventListener('hidden.bs.modal', () => {
                    modal.dispose();
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');
                }, { once: true });
            });

            // 🔹 Cleanup on Livewire navigation
            document.addEventListener("livewire:navigated", () => {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            });
        </script>
    @endpush
</div>