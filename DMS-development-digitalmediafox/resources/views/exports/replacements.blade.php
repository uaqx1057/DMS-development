<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vehicle Replacements Report</title>
    <style>
        body { font-family: sans-serif; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Vehicle Replacements Report</h2>
    <p style="text-align: right;">Date: {{ \Carbon\Carbon::now()->format('d M Y') }}</p>

    <table>
        <thead>
            <tr>
               
                <th>Vehicle</th>
                <th>Replacement Vehicle</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Replacement Period</th>
                <th>Notes</th>
                <th>Requested At</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach ($replacements as $r)
                <tr>
                    
                    <td>{{ $r->vehicle->registration_number ?? 'N/A' }}</td>
                     <td>{{ $r->replacement_vehicle->registration_number ?? 'N/A' }}</td>
                     <td>{{ \Illuminate\Support\Str::headline($r->reason) }}</td>
                     <td>{{ ucfirst($r->status) }}</td>
                    <td>{{ \Illuminate\Support\Str::headline($r->replacement_period) }}</td>
                    <td>{{ $r->notes }}</td>
                   <td>{{ $r->created_at->format('d-m-Y') }}</td>
                   
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
