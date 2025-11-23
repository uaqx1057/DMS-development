<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />

    <x-ui.message />
    <x-ui.row>
        <div class="row">
           <!-- Begin: Date Filter Card -->
            <x-ui.col class="mb-3 col-lg-3 col-md-3">
                <x-form.label for="daterangepicker" name="Date Range"/>
                <input type="text" id="daterangepicker" class="form-control" placeholder="Select Date Range" 
                       wire:model="date_range" wire:change="$refresh"/>
                @if($date_range)
                    <small class="text-muted d-block mt-2">
                        Selected: {{ $date_range }}
                    </small>
                @endif
            </x-ui.col>
            <!-- End: Date Filter Card -->
            
            <!-- Begin: Branch Card --> 
            @if (auth()->user()->role_id == 1)
                <x-ui.col class="mb-3 col-lg-3 col-md-3">
                    <x-form.label for="branch_id" name="Branches"/>
                    <x-form.select class="form-select" wire:model.lazy="branch_id" wire:change="$refresh">
                        <x-form.option value="" name="--select branch--"/>
                        @foreach ($branches as $branch)
                            <x-form.option value="{{ $branch->id }}" :name="$branch->name"/>
                        @endforeach
                    </x-form.select>
                    <x-ui.alert error="branch_id"/>
                </x-ui.col>
            @endif
            <!-- End: Branch Card -->
        </div>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Franchise Report" :href="route('superviser-difference.create')" :add="$add_permission" :export="$reports->count() > 0">
                    @if($reports->count() > 0)
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
                    @if($reports->count() > 0)
                        <x-table
                            :columns="$columns"
                            :page="$page"
                            :perPage="$perPage"
                            :items="$reports"
                            :sortColumn="$sortColumn"
                            :sortDirection="$sortDirection"
                            isModalEdit="true"
                            routeEdit="booklet.edit"
                            :edit_permission="$edit_permission"
                            :delete_booklet="true"
                        />
                    @else
                        <div class="text-center py-4">
                            <i class="ri-information-line ri-2x text-muted"></i>
                            <p class="text-muted mt-2">No records found for the selected filters.</p>
                            @if($date_range || $branch_id)
                                <button wire:click="clearFilters" class="btn btn-primary btn-sm">
                                    Clear Filters
                                </button>
                            @endif
                        </div>
                    @endif
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>
    
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('livewire:initialized', function () {
                const picker = flatpickr('#daterangepicker', {
                    mode: 'range',
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    clickOpens: true,
                    // Set timezone to UTC to avoid date shifting
                    time_24hr: true,
                    onReady: function(selectedDates, dateStr, instance) {
                        // Ensure dates are handled in local time without timezone conversion
                        instance.config.time_24hr = true;
                    },
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length === 2) {
                            // Format dates as YYYY-MM-DD without timezone conversion
                            const startDate = this.formatDate(selectedDates[0], "Y-m-d");
                            const endDate = this.formatDate(selectedDates[1], "Y-m-d");
                            const formattedRange = startDate + ' to ' + endDate;
                            
                            console.log('Selected Dates:', {
                                raw: selectedDates,
                                formatted: formattedRange,
                                start: startDate,
                                end: endDate
                            });
                            
                            @this.set('date_range', formattedRange);
                        } else if (selectedDates.length === 0) {
                            @this.set('date_range', '');
                        }
                    }
                });
                
                // If date_range already has value, set it on picker
                @if($date_range)
                    const currentRange = '{{ $date_range }}';
                    if (currentRange) {
                        const dates = currentRange.split(' to ');
                        if (dates.length === 2) {
                            // Parse dates without timezone issues
                            const startDate = new Date(dates[0] + 'T00:00:00');
                            const endDate = new Date(dates[1] + 'T00:00:00');
                            
                            console.log('Setting dates:', {
                                start: startDate,
                                end: endDate
                            });
                            
                            picker.setDate([startDate, endDate]);
                        }
                    }
                @endif
            });

            // Clear filters function
            function clearFilters() {
                @this.set('date_range', '');
                @this.set('branch_id', '');
                const picker = document.getElementById('daterangepicker')._flatpickr;
                if (picker) {
                    picker.clear();
                }
            }
        </script>
    @endpush
</div>