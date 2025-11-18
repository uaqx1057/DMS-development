<div>
    <div class="row bg-white mb-5 px-2 py-3">
        <!-- Image -->
        <div class="col-md-2 mb-3 text-center">
            @if (!empty($driverData['image']))
                <img src="{{ Storage::url('app/public/' . $driverData['image']) }}" class="img-thumbnail rounded-circle"
                    style="object-fit: cover; max-width:100%; width: 150px; height: 150px;">
            @else
                <div class="d-flex align-items-center justify-content-center rounded-circle mx-auto text-white fw-bold"
                    style="width: 150px; height: 150px; background-color: #722C81; font-size: 50px;">
                    {{ strtoupper(substr($driverData['name'] ?? 'D', 0, 1)) }}
                </div>
            @endif

        </div>
        <div class="col-md-10">
            <table class="table table-bordered table-striped align-middle">
                <tbody>
                    @php
                        // Only show fields that have data (excluding remarks & platform_ids for separate display)
                        $fields = collect($driverData)->except([
                            'remarks',
                            'id',
                            'platform_ids',
                            'assigned_ids_by_business',
                            'image',
                        ]);

                        // Define field labels for readability
                        $labels = [
                            'driver_id' => 'Driver ID',
                            'name' => 'Name',
                            'nationality' => 'Nationality',
                            'language' => 'Preferred Language',
                            'branch' => 'Branch',
                            'driver_type' => 'Driver Type',
                            'dob' => 'Date of Birth',
                            'iqaama_number' => 'Iqaama Number',
                            'iqaama_expiry' => 'Iqaama Expiry',
                            'absher_number' => 'Absher Number',
                            'sponsorship' => 'Sponsorship',
                            'sponsorship_id' => 'Sponsorship ID',
                            'license_expiry' => 'License Expiry',
                            'insurance_policy_number' => 'Insurance Policy Number',
                            'insurance_expiry' => 'Insurance Expiry',
                            'vehicle_monthly_cost' => 'Vehicle Monthly Cost',
                            'mobile_data' => 'Mobile Data',
                            'fuel' => 'Fuel',
                            'gprs' => 'GPRS',
                            'government_levy_fee' => 'Government Levy Fee',
                            'accommodation' => 'Accommodation',
                            'email' => 'Email',
                            'mobile' => 'Mobile',
                            'created_at' => 'Created At',
                        ];

                        // Filter only fields that actually have data (non-null, not "-", not empty)
                        $filteredFields = $fields->filter(fn($value) => !in_array($value, [null, '-', '']));
                        $chunks = $filteredFields->chunk(2); // two fields per row
                    @endphp

                    @foreach ($chunks as $chunk)
                        <tr>
                            @foreach ($chunk as $key => $value)
                                <th>{{ $labels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</th>
                                <td>{{ $value }}</td>
                            @endforeach
                            {{-- If only one field in last row, fill empty columns --}}
                            @if ($chunk->count() === 1)
                                <th></th>
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Remarks (full width if has data) --}}
            @if (!empty($driverData['remarks']) && $driverData['remarks'] !== '-')
                <div class="mt-3">
                    <h6 class="fw-bold">Remarks</h6>
                    <p>{{ $driverData['remarks'] }}</p>
                </div>
            @endif

            {{-- Platform IDs (full width if has data) --}}
            @if (!empty($driverData['platform_ids']) && $driverData['platform_ids'] !== '-')
                <div class="mt-3">
                    <h6 class="fw-bold">Platform IDs</h6>
                    <ol class="list-group list-group-flush">
                        @foreach (explode(' | ', $driverData['platform_ids']) as $platform)
                            <li class="list-group-item ps-0"><b
                                    class="pe-2">{{ $loop->iteration }}:</b>{{ $platform }}</li>
                        @endforeach
                    </ol>
                </div>
            @endif

        </div>
    </div>
    @if (auth()->user()->role_id == 1)
    <div>
        <!-- Import Modal -->
        <div wire:ignore.self class="modal fade" id="importModal" tabindex="-1" x-data="{ uploading: false }"
            x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false"
            x-on:livewire-upload-error="uploading = false">

            <div class="modal-dialog">
                <form wire:submit.prevent="import">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Import CSV</h5>
                            <button type="button" class="btn-close m-2" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <label>Report Date:</label>
                            <input type="date" wire:model="report_date" class="form-control"
                                max="{{ now()->format('Y-m-d') }}">
                            @error('report_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                            <br>

                            <!-- Begin: Business Card -->
                            <div class="mb-3">
                                <label for="business_id">Platform Name</label>
                                <select wire:model="report_platform_name" class="form-control">
                                    <option>--select Platform--</option>
                                    @foreach ($businesses as $item)
                                        <option value="{{ $item->name }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('report_platform_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                            </div>
                            <!-- End: Business Card -->

                            <input type="file" wire:model="csv_file" accept=".csv" class="form-control">

                            {{-- Show loader while file uploading --}}
                            <!-- <div x-show="uploading" class="mt-2 text-info small">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        Uploading file...
                    </div> -->

                            <div class="d-flex align-items-center mt-2">
                                @error('csv_file')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <span class="ms-2 text-muted small">Upload only .csv file format</span>
                            </div>
                        </div>

                        <div class="modal-footer">
                            {{-- Import button --}}
                            <button type="submit" class="btn btn-success" :disabled="uploading"
                                wire:loading.attr="disabled" wire:target="import"
                                x-bind:class="{ 'disabled': uploading }">

                                {{-- Normal state --}}
                                <span x-show="!uploading" wire:loading.remove wire:target="import">
                                    Import
                                </span>

                                {{-- While file uploading --}}
                                <span x-show="uploading">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Uploading file...
                                </span>

                                {{-- While import submitting --}}
                                <span wire:loading wire:target="import">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Importing...
                                </span>
                            </button>

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
                    <x-form.label for="driver_id" name="Date Range" />
                    <input type="text" id="daterangepicker" class="form-control" placeholder="Select Date Range" />
                </x-ui.col>
                <!-- Begin: Driver Card -->
                <x-ui.col class="mb-3 col-lg-3 col-md-3 d-none">
                    <x-form.label for="driver_id" name="Drivers" />
                    <x-form.select class="form-select" wire:model="driver_id" class="js-example-basic-single">
                        <x-form.option value="" name="--select driver--" />
                        @foreach ($drivers as $item)
                            @php $name = $item->name . ' ( ' . $item->iqaama_number . ' )'; @endphp
                            <x-form.option value="{{ $item->id }}" :name="$name" />
                        @endforeach
                    </x-form.select>
                    <x-ui.alert error="driver_id" />
                </x-ui.col>
                <!-- End: Driver Card -->
                <!-- Begin: Business Card -->
                 @if(count($allPlatformIds)>1)
                <x-ui.col class="mb-3 col-lg-3 col-md-3">
                    <x-form.label for="business_id_value" name="Platform" />
                    <x-form.select wire:model.lazy="business_id_value">
                        <x-form.option value="" name="--select Platform--" />
                        @foreach ($allPlatformIds as $businessIdRecord)
                            @php
                                $isCurrentAssignment = in_array($businessIdRecord->id, $currentAssignedBusinessIds);
                                $label = $businessIdRecord->business->name . ' (' . $businessIdRecord->value . ')';
                                $label .= !$isCurrentAssignment ? ' [Old]' : '';
                            @endphp
                            <x-form.option value="{{ $businessIdRecord->id }}" :name="$label" />
                        @endforeach
                    </x-form.select>
                    <x-ui.alert error="business_id_value" />
                </x-ui.col>
                @endif

                <!-- End: Business Card -->
                <!-- Begin: Branch Card -->
                @if ($totalBranches > 1)
                <x-ui.col class="mb-3 col-lg-3 col-md-3">
                    <x-form.label for="branch_id" name="Branches" />
                    <x-form.select class="form-select" wire:model.lazy="branch_id">
                        <x-form.option value="" name="--select branch--" />
                        @foreach ($branches as $branch)
                            @php
                                if($driverData['branch'] == $branch->name){
                                    $displayBranchName = $branch->name;
                                } else{
                                    $displayBranchName = $branch->name . ' ( old )';
                                }
                            @endphp
                            <x-form.option value="{{ $branch->id }}" :name="$displayBranchName" />
                        @endforeach
                    </x-form.select>
                    <x-ui.alert error="branch_id" />
                </x-ui.col>
                @endif
                <!-- End: Branch Card -->
            </div>
            
            {{-- Stats Section --}}
            <div class="row">
                <div class="col-xl-12">
                    <div class="card crm-widget">
                        <div class="p-0 card-body">
                            <div class="row row-cols-md-3 row-cols-1">
                                <x-ui.report-widget-card text="Pending Reports" icon="ri-exchange-dollar-line"
                                    value="{{ $pendingReportsCount }}" />
                                <x-ui.report-widget-card text="Approved Reports" icon="ri-space-ship-line"
                                    value="{{ $completedReportsCount }}" />
                                <x-ui.report-widget-card text="Total Orders" icon="ri-pulse-line"
                                    value="{{ $totalOrdersCount }}" />
                            </div><!-- end row -->
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
            </div><!-- end row -->


            <x-ui.col class="col-lg-12">
                <x-ui.card>


                    <x-ui.card-header title="" :add="false" :href="route('coordinator-report.create')" :addMenu="[
                        [
                            'label' => 'Add Daily Report',
                            'url' => route('coordinator-report.create'),
                            'icon' => 'ri-file-list-line',
                        ],
                        [
                            'label' => 'Add Previous Penalties',
                            'url' => route('penalty.create'),
                            'icon' => 'ri-error-warning-line',
                        ],
                    ]" :import=true
                        :export="$coordinatorReports->count() > 0">
                        @if ($coordinatorReports->count() > 0)
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
                        <x-table :columns="$columns" :page="$page" : :perPage="$perPage" :items="$coordinatorReports"
                            :sortColumn="$sortColumn" :sortDirection="$sortDirection" isModalEdit="true"
                            routeEdit="coordinator-report.edit" :edit_permission="$edit_permission" :showModal="true" />
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
                    }, {
                        once: true
                    });
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
    @endif
</div>
