<div>
    @if($showRejectModal)
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Reject Request</h5>
                </div>

                <div class="modal-body">
                    <label class="form-label">Reason for rejection <span class="text-danger">*</span></label>
                    
                    <textarea class="form-control" 
                            wire:model.defer="reject_reason"
                            rows="4"
                            placeholder="Write reason here..."></textarea>

                    @error('reject_reason')
                        <span class="text-danger mt-2">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="$set('showRejectModal', false)">Close</button>

                    <button class="btn btn-success" wire:click="submitReject" wire:loading.attr="disabled">
                        <!-- Spinner shows only while submitReject is running -->
                        <span wire:loading wire:target="submitReject" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Submit Rejection
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />

    <x-ui.message />
    <x-ui.row>
        <div class="row">
            <!-- Begin: Date Range --> 
            <x-ui.col class="mb-3 col-lg-3 col-md-3">
                <x-form.label for="daterangepicker" name="Date Range"/>
                <input type="text" id="daterangepicker" class="form-control" placeholder="Select Date Range">
            </x-ui.col>
            <!-- End: Date Range --> 

            <!-- Begin: Branch Card --> 
            <x-ui.col class="mb-3 col-lg-3 col-md-3">
                <x-form.label for="status" name="Status"/>
                <x-form.select class="form-select" wire:model.live="status">
                    <x-form.option value="" name="--select status--" />
                    <x-form.option value="Pending" name="Pending"/>
                    <x-form.option value="Accepted" name="Accepted"/>
                    <x-form.option value="Rejected" name="Rejected"/>
                </x-form.select>
                <x-ui.alert error="status"/>
            </x-ui.col>
            <!-- End: Branch Card -->
        </div>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Request Recharge List" :href="route('request-recharge.create')" :add="$add_permission" :export="$requestRecharges->count() > 0">
                    @if($requestRecharges->count() > 0)
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
                        :page="$page"
                        :perPage="$perPage"
                        :items="$requestRecharges"
                        :sortColumn="$sortColumn"
                        :sortDirection="$sortDirection"
                        isModalEdit="true"
                        routeEdit="booklet.edit"
                        :edit_permission="$edit_permission"
                        :rechargePermission="true"
                    />
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>

    @if($showRejectModal)
    <script>
        document.body.style.overflow = 'hidden';
    </script>
    @else
    <script>
        document.body.style.overflow = 'auto';
    </script>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('livewire:init', () => {

            flatpickr("#daterangepicker", {
                mode: "range",
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const start = instance.formatDate(selectedDates[0], "Y-m-d");
                        const end   = instance.formatDate(selectedDates[1], "Y-m-d");
                        
                        Livewire.dispatch("dateRangeSelected", { start, end });
                    } else if (selectedDates.length === 0) {
                        // Clear the date range when input is emptied
                        Livewire.dispatch("dateRangeSelected", { start: null, end: null });
                    }
                }
            });

        });
    </script>
    @endpush
</div>
