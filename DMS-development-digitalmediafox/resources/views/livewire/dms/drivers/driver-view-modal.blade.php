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
                                        <tr>
                                            <th>Driver ID</th><td>{{ $driverData['driver_id'] ?? '-' }}</td>
                                            <th>Name</th><td>{{ $driverData['name'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nationality</th><td>{{ $driverData['nationality'] ?? '-' }}</td>
                                            <th>Preferred Language</th><td>{{ $driverData['language'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Branch</th><td>{{ $driverData['branch'] ?? '-' }}</td>
                                            <th>Driver Type</th><td>{{ $driverData['driver_type'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date of Birth</th><td>{{ $driverData['dob'] ?? '-' }}</td>
                                            <th>Iqaama Number</th><td>{{ $driverData['iqaama_number'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Iqaama Expiry</th><td>{{ $driverData['iqaama_expiry'] ?? '-' }}</td>
                                            <th>Absher Number</th><td>{{ $driverData['absher_number'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sponsorship</th><td>{{ $driverData['sponsorship'] ?? '-' }}</td>
                                            <th>Sponsorship ID</th><td>{{ $driverData['sponsorship_id'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>License Expiry</th><td>{{ $driverData['license_expiry'] ?? '-' }}</td>
                                            <th>Insurance Policy Number</th><td>{{ $driverData['insurance_policy_number'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Insurance Expiry</th><td>{{ $driverData['insurance_expiry'] ?? '-' }}</td>
                                            <th>Vehicle Monthly Cost</th><td>{{ $driverData['vehicle_monthly_cost'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile Data</th><td>{{ $driverData['mobile_data'] ?? '-' }}</td>
                                            <th>Fuel</th><td>{{ $driverData['fuel'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>GPRS</th><td>{{ $driverData['gprs'] ?? '-' }}</td>
                                            <th>Government Levy Fee</th><td>{{ $driverData['government_levy_fee'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Accommodation</th><td>{{ $driverData['accommodation'] ?? '-' }}</td>
                                            <th>Email</th><td>{{ $driverData['email'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile</th><td>{{ $driverData['mobile'] ?? '-' }}</td>
                                            <th>Remarks</th><td>{{ $driverData['remarks'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th><td>{{ $driverData['created_at'] ?? '-' }}</td>
                                            <th>Platform IDs</th><td>{{ $driverData['platform_ids'] ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

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
