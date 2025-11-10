<div>
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="text-white modal-header bg-primary">
                        <h5 class="modal-title">Driver Details</h5>
                        <button type="button" class="btn-close m-1" wire:click="closeModal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <!-- Image -->
                            <div class="col-md-2 mb-3 text-center">
                                    @if(!empty($driverData['image']))
                                        <img src="{{ Storage::url('app/public/' . $driverData['image']) }}"
                                            class="img-thumbnail rounded-circle"
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
                                            $fields = collect($driverData)->except(['remarks', 'platform_ids', 'assigned_ids_by_business', 'image']);

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

                                        @foreach($chunks as $chunk)
                                            <tr>
                                                @foreach($chunk as $key => $value)
                                                    <th>{{ $labels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</th>
                                                    <td>{{ $value }}</td>
                                                @endforeach
                                                {{-- If only one field in last row, fill empty columns --}}
                                                @if($chunk->count() === 1)
                                                    <th></th><td></td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{-- Remarks (full width if has data) --}}
                                @if(!empty($driverData['remarks']) && $driverData['remarks'] !== '-')
                                    <div class="mt-3">
                                        <h6 class="fw-bold">Remarks</h6>
                                        <p>{{ $driverData['remarks'] }}</p>
                                    </div>
                                @endif

                                {{-- Platform IDs (full width if has data) --}}
                                @if(!empty($driverData['platform_ids']) && $driverData['platform_ids'] !== '-')
                                    <div class="mt-3">
                                        <h6 class="fw-bold">Platform IDs</h6>
                                        <ol class="list-group list-group-flush">
                                            @foreach(explode(' | ', $driverData['platform_ids']) as $platform)
                                                <li class="list-group-item ps-0"><b class="pe-2">{{$loop->iteration}}:</b>{{ $platform }}</li>
                                            @endforeach
                                        </ol>
                                    </div>
                                @endif
                      
                            </div>
                        </div>
                            {{-- Coordinator Report for this driver --}}
                        <div class="m-4">
                            <h5>Coordinator Reports</h5>
                            @livewire('dms.coordinator-report.coordinator-report-list', ['driver_id' => $driverData['id']], key('coordinator-report-'.$driverData['id']))
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
