<div>
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="text-white modal-header bg-primary">
                        <h5 class="modal-title">Coordinator Report Details</h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Field</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Report Date</strong></td>
                                    <td>{{ $reportData['report_date'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Driver ID</strong></td>
                                    <td>{{ $reportData['driver']['name'] .' ('.$reportData['driver']['iqaama_number'].')' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Current Status</strong></td>
                                    <td>
                                        <x-ui.col class="mb-4 col-lg-4 col-md-4">
                                            <x-form.label for="status" name="Status"/>
                                            <x-form.select class="form-select form-control" wire:model="status">
                                                <x-form.option value="Pending" name="Pending"/>
                                                <x-form.option value="Approved" name="Approved"/>
                                            </x-form.select>
                                            <x-ui.alert error="status"/>
                                        </x-ui.col>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <h5 class="mt-4">Business Details</h5>
                        @if(!empty($reportData['businesses']))
                            @foreach($reportData['businesses'] as $business)
                                <div class="mb-4">
                                    <h6><strong>Business Name:</strong> {{ $business['name'] }}</h6>

                                    @php
                                        $fieldData = collect($reportData['report_fields'])->firstWhere(function ($field) use ($business) {
                                            return $field['business_id'] === $business['id'] && $field['field_id'] === 5;
                                        });
                                        $files = $fieldData ? json_decode($fieldData['value'], true) : [];
                                    @endphp

                                    @if(!empty($files))
                                        <div class="row">
                                            @foreach($files as $file)
                                                <div class="mb-3 col-md-3">
                                                    <a href="{{ asset('storage/' . $file) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $file) }}" class="img-fluid img-thumbnail" alt="Document Image">
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p>No documents available.</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p>No business details found.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Close</button>
                        <button type="button" class="btn btn-success" wire:click="updateStatus">Update Status</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
