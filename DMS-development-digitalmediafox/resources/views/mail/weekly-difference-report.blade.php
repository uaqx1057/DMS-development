<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Weekly Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 7px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h1 {
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>Weekly Report</h1>
    <p>Total collect by Supervisors = <strong>{{ $mailData['all_superviser_collected'] }}</strong></p>
    <h3>Supervisors Report</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Sr. No.</th> {{-- Serial number column --}}
                <th>Supervisor Name</th>
                <th>Date Range</th>
                <th>Branch</th>
                <th>Total Difference</th>
                <th>Total Paid</th>
                <th>Total Remaining</th>
            </tr>
        </thead>
        <tbody>
            @php
                $index = 0;
            @endphp
            @foreach ($mailData['total_superviser_collected'] as $supervisor)
                @if ($supervisor->total_receipt > 0 || $supervisor->total_paid > 0 || $supervisor->total_remaining > 0)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $supervisor->name }}</td>
                        <td>{{ $supervisor->date_range }}</td>
                        <td>{{ $supervisor->branch->name }}</td>
                        <td>{{ $supervisor->total_receipt }}</td>
                        <td>{{ $supervisor->total_paid }}</td>
                        <td>{{ $supervisor->total_remaining }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <br>
    <h3>Drivers Report</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Sr. No.</th> {{-- Serial number column --}}
                <th>Driver Name</th>
                <th>Iqaama Number</th>
                <th>Date Range</th>
                <th>Total Difference</th>
                <th>Total Paid</th>
                <th>Total Remaining</th>
            </tr>
        </thead>
        <tbody>
            @php
                $index = 0;
            @endphp
            @foreach ($mailData['total_drivers_collected'] as $driver)
                @if ($driver->total_receipt > 0 || $driver->total_paid > 0 || $driver->total_remaining > 0)
                    <tr>
                        <td>{{ $index + 1 }}</td> {{-- Serial number --}}
                        <td>{{ $driver->name }}</td>
                        <td>{{ $driver->iqaama_number }}</td>
                        <td>{{ $driver->date_range }}</td>
                        <td>{{ $driver->total_receipt }}</td>
                        <td>{{ $driver->total_paid }}</td>
                        <td>{{ $driver->total_remaining }}</td>
                    </tr>
                @endif
            @endforeach

        </tbody>
    </table>
    <br>
</body>

</html>
