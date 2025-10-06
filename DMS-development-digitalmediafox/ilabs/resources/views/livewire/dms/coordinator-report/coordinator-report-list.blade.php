<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />

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
                <x-ui.card-header title="Coordinator Report List" :href="route('coordinator-report.create')" :add="$add_permission"/>
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
        </script>
    @endpush

</div>
