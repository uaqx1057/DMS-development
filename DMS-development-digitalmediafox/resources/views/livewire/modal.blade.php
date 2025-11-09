<div>
    @if($showModal)
        <div id="updateStatusModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="text-white modal-header bg-primary">
                        <h5 class="modal-title">Coordinator Report Details</h5>
                        <button type="button" class="btn-close m-1" wire:click="closeModal" aria-label="Close"></button>
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
                                    <td>{{ isset($reportData['driver']) ? ($reportData['driver']['name'] .' ('.$reportData['driver']['iqaama_number'].')') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Branch</strong></td>
                                    <td>{{ isset($reportData['branch']) ? $reportData['branch']['name'] : 'N/A' }}</td>
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

                        <h3 class="mt-4">Wallet Details</h3>
                        @if(!empty($reportData['wallet']))
                            <div class="row mt-2">
                                <div class="mb-3 col-md-3">
                                    <a href="{{ asset('storage/app/public/' . $reportData['wallet']) }}" target="_blank">
                                        <img src="{{ asset('storage/app/public/' . $reportData['wallet']) }}" class="img-fluid img-thumbnail" alt="Document Image">
                                    </a>
                                </div>
                            </div>
                        @else
                            <p>No documents available.</p>
                        @endif

                        <h3 class="mt-4">Business Details</h3>

@php
    $fieldsCollection = collect($reportData['report_fields'] ?? []);
    $groupedByBusinessValue = $fieldsCollection->groupBy('business_id_value');
@endphp

@if(!empty($reportData['businesses']))
    @foreach($reportData['businesses'] as $business)
        <div class="mb-4 card">
            <div class="card-header">
                <h5 class="mb-0">{{ $business['name'] }} ({{ $business['id'] }})</h5>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th>Field</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // All fields enabled for this business
                            $enabledFields = $business['fields'] ?? [];

                            // All report field values for this business (grouped by field_id)
                            $reportFieldsForBusiness = $fieldsCollection->where('business_id', $business['id'])->keyBy('field_id');
                        @endphp

                        @foreach($enabledFields as $enabledField)
                            @php
                                $fieldId = $enabledField['id'];
                                $reportField = $reportFieldsForBusiness->get($fieldId);
                                $value = $reportField['value'] ?? 0;
                                $fieldType = $enabledField['type'] ?? ($reportField['field']['type'] ?? 'text');
                            @endphp

                            <tr>
                                <td><strong>{{ $enabledField['name'] }}</strong></td>
                                <td>
                                    @if($fieldType === 'DOCUMENT')
                                        @php $files = json_decode($value, true) ?? []; @endphp
                                        @if(!empty($files))
                                            <div class="row">
                                                @foreach($files as $file)
                                                    <div class="mb-2 col-md-3">
                                                        <a href="{{ asset('storage/app/public/' . $file) }}" target="_blank">
                                                            <img src="{{ asset('storage/app/public/' . $file) }}" class="img-fluid img-thumbnail" alt="Document Image">
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="mb-0">No documents available</p>
                                        @endif
                                    @elseif($fieldType === 'number')
                                        ₹{{ number_format((float)$value, 2) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
