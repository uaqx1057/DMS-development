<div class="row">
    <div class="col">

        <div class="h-100">

            <div class="row">
                <x-ui.col class="mb-3 col-lg-3 col-md-3">
                    <input type="text" id="daterangepicker" class="form-control" placeholder="Select Date Range"/>
                </x-ui.col>
            </div>

            <div class="row">
                <div class="col-xl-4 col-md-6">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">

                                <div class="flex-shrink-0">
                                    <h5 class="mb-0 text-danger fs-14">
                                        <i class="align-middle ri-arrow-right-down-line fs-13"></i> Inactive
                                    </h5>
                                </div>
                            </div>
                            <div class="mt-4 d-flex align-items-end justify-content-between">
                                <div>
                                    <h4 class="mb-4 fs-22 fw-semibold ff-secondary"><span class="counter-value" data-target="36894">{{ $activeDrivers }}</span></h4>
                                    <a href="{{ route('drivers.index') }}" navigate:true class="text-decoration-underline">View all Drivers</a>
                                </div>
                                <div class="flex-shrink-0 avatar-sm">
                                    <span class="rounded avatar-title bg-danger-subtle fs-3">
                                        <i class="ri-takeaway-line text-danger"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->

                <div class="col-xl-4 col-md-6">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">

                                <div class="flex-shrink-0">
                                    <h5 class="mb-0 text-success fs-14">
                                        <i class="align-middle ri-arrow-right-down-line fs-13"></i> Active
                                    </h5>
                                </div>
                            </div>
                            <div class="mt-4 d-flex align-items-end justify-content-between">
                                <div>
                                    <h4 class="mb-4 fs-22 fw-semibold ff-secondary"><span class="counter-value" data-target="36894">{{ $inactiveDrivers }}</span></h4>
                                    <a href="{{ route('drivers.index') }}" navigate:true class="text-decoration-underline">View all Drivers</a>
                                </div>
                                <div class="flex-shrink-0 avatar-sm">
                                    <span class="rounded avatar-title bg-success-subtle fs-3">
                                        <i class="ri-takeaway-line text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->

                <div class="col-xl-4 col-md-6">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">

                                <div class="flex-shrink-0">
                                    <h5 class="mb-0 text-warning fs-14">
                                        <i class="align-middle ri-arrow-right-down-line fs-13"></i> Busy
                                    </h5>
                                </div>
                            </div>
                            <div class="mt-4 d-flex align-items-end justify-content-between">
                                <div>
                                    <h4 class="mb-4 fs-22 fw-semibold ff-secondary"><span class="counter-value" data-target="36894">{{ $busyDrivers }}</span></h4>
                                    <a href="{{ route('drivers.index') }}" navigate:true class="text-decoration-underline">View all Drivers</a>
                                </div>
                                <div class="flex-shrink-0 avatar-sm">
                                    <span class="rounded avatar-title bg-warning-subtle fs-3">
                                        <i class="ri-takeaway-line text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->

            </div> <!-- end row-->

            <div class="row">
                <div class="mb-3 col-12">
                    <h3>Branches Analysis</h3>
                </div>
                <div class="col-xl-12">
                    <div class="card crm-widget">
                        <div class="p-0 card-body">
                            <div class="row row-cols-md-3 row-cols-1">
                                @foreach ($branches as $branch)
                                <div class="col col-lg border-end">
                                    <div class="px-3 py-4" style="display: flex; flex-direction:column; gap:5px">
                                        <h5 class="text-muted text-uppercase fs-13">
                                            {{ $branch['branch_name'] }}
                                            <i class="align-middle ri-arrow-up-circle-line text-success fs-18 float-end"></i>
                                        </h5>
                                        <div class="d-flex align-items-center">
                                            <h2 class="mb-0">
                                                <span class="counter-value" data-target="197">Total Drivers: {{ $branch['total_drivers'] }}</span>
                                            </h2>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <h3 class="mb-0">
                                                <span class="counter-value" data-target="197">
                                                    Active: {{ $branch['active'] }},
                                                    Inactive: {{ $branch['inactive'] }},
                                                    Busy: {{ $branch['busy'] }}
                                                </span>
                                            </h3>
                                        </div>
                                    </div>
                                </div><!-- end col -->
                            @endforeach

                            </div><!-- end row -->
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div>
            </div>

            <div class="row">
                @foreach (['today' => 'Orders today', 'week' => 'Orders this Week', 'month' => 'Orders this Month'] as $period => $title)
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="fs-15 fw-semibold">{{ $title }}</h5>
                                <p class="text-muted">{{ $totals[$period] ?? 0 }}</p>
                                <div class="flex-wrap d-flex justify-content-evenly">
                                    <p class="mb-0 text-muted">
                                        <i class="align-middle mdi mdi-numeric-3-circle text-success fs-18 me-2"></i>Drop: {{ $statusCounts['Drop'][$period] ?? 0 }}
                                    </p>
                                    <p class="mb-0 text-muted">
                                        <i class="align-middle mdi mdi-numeric-0-circle text-info fs-18 me-2"></i>Pickup: {{ $statusCounts['Pickup'][$period] ?? 0 }}
                                    </p>
                                    <p class="mb-0 text-muted">
                                        <i class="align-middle mdi mdi-numeric-8-circle text-primary fs-18 me-2"></i>Cancel: {{ $statusCounts['Cancel'][$period] ?? 0 }}
                                    </p>
                                </div>
                            </div>
                            <div class="progress animated-progress rounded-bottom rounded-0" style="height: 6px;">
                                @php
                                    $total = $totals[$period] ?? 1; // Avoid division by zero
                                    $dropPercentage = round((($statusCounts['Drop'][$period] ?? 0) / $total) * 100);
                                    $pickupPercentage = round((($statusCounts['Pickup'][$period] ?? 0) / $total) * 100);
                                    $cancelPercentage = round((($statusCounts['Cancel'][$period] ?? 0) / $total) * 100);
                                @endphp
                                <div class="progress-bar bg-success rounded-0" role="progressbar" style="width: {{ $dropPercentage }}%" aria-valuenow="{{ $dropPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-info rounded-0" role="progressbar" style="width: {{ $pickupPercentage }}%" aria-valuenow="{{ $pickupPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-primary rounded-0" role="progressbar" style="width: {{ $cancelPercentage }}%" aria-valuenow="{{ $cancelPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div> <!-- end .h-100-->

    </div> <!-- end col -->
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
