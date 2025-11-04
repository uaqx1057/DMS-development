<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Driver List</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Driver List</h1>
    <p style="text-align: right;">Date: {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>Sr. No.</th>
                <th>Driver ID</th>
                <th>Name</th>
                <th>Iqaama Number</th>
                <th>Iqaama Expiry</th>
                <th>Driver Type</th>
                <th>Branch</th>
                <th>Platform Name(IDs)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($drivers as $driver)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $driver->driver_id }}</td>
                <td>{{ $driver->name }}</td>
                <td>{{ $driver->iqaama_number }}</td>
                <td>{{ $driver->iqaama_expiry }}</td>
                <td>{{ $driver->driver_type?->name ?? '-' }}</td>
                <td>{{ $driver->branch?->name ?? '-' }}</td>
                <td>{{ $driver->platform_ids }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
