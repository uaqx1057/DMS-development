<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Platform Report - {{ $report['driver']['name'] }}</title>
    <style>
        body { 
            font-family: sans-serif; 
            font-size: 10px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .info-table td:first-child {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 30%;
        }
        .fields-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .fields-table th,
        .fields-table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }
        .fields-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Platform Report</h1>
        <p><strong>{{ $businessName }}</strong> - Platform ID: {{ $platformId }}</p>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}</p>
    </div>

    <!-- Report Information -->
    <div class="info-section">
        <h3>Report Details</h3>
        <table class="info-table">
            <tr>
                <td>Report Date</td>
                <td>{{ \Carbon\Carbon::parse($report['report_date'])->format('d M Y') }}</td>
            </tr>
            <tr>
                <td>Driver Name</td>
                <td>{{ $report['driver']['name'] }}</td>
            </tr>
            <tr>
                <td>Driver Iqaama Number</td>
                <td>{{ $report['driver']['iqaama_number'] }}</td>
            </tr>
            <tr>
                <td>Branch</td>
                <td>{{ $report['branch']['name'] }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <span class="status-badge {{ $report['status'] == 'Approved' ? 'status-approved' : 'status-pending' }}">
                        {{ $report['status'] }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Field Values -->
    <div class="info-section">
        <h3>Field Values</h3>
        <table class="fields-table">
            <thead>
                <tr>
                    <th>Field Name</th>
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

    <!-- Documents Section -->
    @php
        $documentFields = array_filter($report['field_values'], fn($field) => $field['type'] === 'DOCUMENT');
    @endphp

    @if(!empty($documentFields))
        <div class="info-section">
            <h3>Uploaded Documents</h3>
            <p><em>Note: Document images are not included in PDF. Please view them in the system.</em></p>
            @foreach($documentFields as $fieldName => $fieldData)
                @php
                    $files = json_decode($fieldData['value'], true) ?? [];
                @endphp
                @if(!empty($files))
                    <p><strong>{{ $fieldName }}:</strong> {{ count($files) }} file(s) uploaded</p>
                @endif
            @endforeach
        </div>
    @endif

    <!-- Footer -->
    <div style="margin-top: 40px; text-align: center; font-size: 8px; color: #666;">
        <p>This is a system-generated report. No signature required.</p>
    </div>
</body>
</html>