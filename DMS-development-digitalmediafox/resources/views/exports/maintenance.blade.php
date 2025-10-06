<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Maintenance Report</title>
    <style>
        body { font-family: sans-serif; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Maintenance Report</h2>
    <p style="text-align: right;">Date: {{ \Illuminate\Support\Carbon::now()->format('d M Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>Vehicle</th>
                <th>Type</th>
                <th>Description</th>
                <th>Urgency</th>
                <th>Status</th>
                <th>Requested By</th>
                <th>Requested At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($maintenance as $m)
            <tr>
                <td>{{ $m->vehicle->registration_number ?? 'N/A' }}</td>
               <td>{{ \Illuminate\Support\Str::headline($m->maintenance_type) }}</td>
                <td>{{ $m->description }}</td>
                <td>{{ ucfirst($m->urgency) }}</td>
                <td>{{ ucfirst($m->status) }}</td>
                <td>{{ $m->request_by }}</td>
                <td>{{ $m->created_at->format('d-m-Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
