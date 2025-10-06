<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vehicle Report</title>
    <style>
        body { font-family: sans-serif; font-size: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Vehicle Report</h2>
    <p style="text-align: right;">Date: {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>Reg No</th>
                <th>Make</th>
                <th>Model</th>
                <th>Year</th>
                <th>VIN</th>
                <th>Location</th>
                <th>Status</th>
                <th>Driver</th>
                <th>Fuel</th>
                <th>Mileage</th>
                <th>Notes</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vehicles as $v)
                <tr>
                    <td>{{ $v->registration_number }}</td>
                    <td>{{ $v->make }}</td>
                    <td>{{ $v->model }}</td>
                    <td>{{ $v->year }}</td>
                    <td>{{ $v->vin }}</td>
                    <td>{{ $v->current_location }}</td>
                    <td>{{ ucfirst($v->status) }}</td>
                    <td>
                    @if ($v->status === 'assigned' && $v->assignedDriver && $v->assignedDriver->driver)
                    {{ $v->assignedDriver->driver->name }}
                    @else
                    
                    @endif
                    </td>
                    <td>{{ $v->fuel_type }}</td>
                    <td>{{ $v->mileage }}</td>
                    <td>{{ $v->notes }}</td>
                    <td>{{ $v->created_at->format('d-m-Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
