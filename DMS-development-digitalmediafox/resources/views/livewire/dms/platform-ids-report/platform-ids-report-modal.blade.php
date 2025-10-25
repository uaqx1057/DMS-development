<div>
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-xl" style="max-width: 95%;">
                <div class="modal-content">
                    <div class="text-white modal-header bg-primary">
                        <h5 class="modal-title">
                            Platform Reports: {{ $businessName }} ({{ $platformId }})
                            <span class="badge bg-light text-dark ms-2">{{ count($reports) }} Report(s)</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        @if(!empty($reports))
                            @foreach($reports as $index => $report)
                                <div class="mb-4 border rounded shadow-sm">
                                    <!-- Report Header -->
                                    <div class="p-3 bg-light border-bottom">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h6 class="mb-0">
                                                    <strong>Report #{{ $index + 1 }}</strong>
                                                </h6>
                                            </div>
                                            <div class="text-end col-md-4">
                                                <button 
                                                    wire:click="exportReportPdf({{ $report['id'] }})" 
                                                    class="btn btn-sm btn-success">
                                                    <i class="fas fa-file-pdf"></i> Export PDF
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Report Details -->
                                    <div class="p-3">
                                        <div class="row">
                                            <!-- Left Column - Basic Info -->
                                            <div class="mb-3 col-md-6">
                                                <table class="table table-sm table-bordered">
                                                    <tbody>
                                                        <tr>
                                                            <td class="bg-light" style="width: 40%;"><strong>Report Date</strong></td>
                                                            <td>{{ \Carbon\Carbon::parse($report['report_date'])->format('d-m-Y') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bg-light"><strong>Driver</strong></td>
                                                            <td>{{ $report['driver']['name'] }} ({{ $report['driver']['iqaama_number'] }})</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bg-light"><strong>Branch</strong></td>
                                                            <td>{{ $report['branch']['name'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bg-light"><strong>Status</strong></td>
                                                            <td>
                                                                <span class="badge {{ $report['status'] == 'Approved' ? 'bg-success' : 'bg-warning' }}">
                                                                    {{ $report['status'] }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Right Column - Field Values -->
                                            <div class="mb-3 col-md-6">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Field</th>
                                                            <th>Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($report['field_values'] as $fieldName => $fieldData)
                                                            @if($fieldData['type'] !== 'DOCUMENT')
                                                                <tr>
                                                                    <td><strong>{{ $fieldName }}</strong></td>
                                                                    <td>{{ $fieldData['value'] }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Document Section -->
                                        @php
                                            $documentFields = array_filter($report['field_values'], fn($field) => $field['type'] === 'DOCUMENT');
                                        @endphp

                                        @if(!empty($documentFields))
                                            <div class="pt-3 mt-3 border-top">
                                                <h6><strong>Uploaded Documents</strong></h6>
                                                <div class="row">
                                                    @foreach($documentFields as $fieldName => $fieldData)
                                                        @php
                                                            $files = json_decode($fieldData['value'], true) ?? [];
                                                        @endphp
                                                        @if(!empty($files))
                                                            @foreach($files as $file)
                                                                <div class="mb-3 col-md-2">
                                                                    <a href="{{ asset('storage/' . $file) }}" target="_blank">
                                                                        <img src="{{ asset('storage/' . $file) }}" 
                                                                             class="img-fluid img-thumbnail" 
                                                                             alt="{{ $fieldName }}"
                                                                             style="max-height: 150px; object-fit: cover;">
                                                                    </a>
                                                                    <p class="mt-1 mb-0 text-center small text-muted">{{ $fieldName }}</p>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No reports found for this platform ID.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>