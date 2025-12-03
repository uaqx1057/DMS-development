<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title> Recharge Log</title>
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
            text-align: center;
        }
    </style>
</head>

<body>
    <h1> Recharge Log</h1>
    <p style="text-align: right;">Date: {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
    <table border="1">
        <thead>
            <tr>
                <th>Sr. No.</th> {{-- Serial number column --}}
                @foreach ($columns as $col)
                    <th>{{ $col['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{-- Initialize sums array for numeric totals --}}
            @php
                $sums = array_fill(0, count($columns), 0.0);
            @endphp

            @foreach ($requestRecharges as $request)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $request->driver_with_iqama }}</td>
                    <td>{{ $request->driver_branch }}</td>
                    <td>{{ $request->mobile }}</td>
                    <td>{{ $request->opearator }}</td>
                    <td>{{ ucfirst($request->rechargeStatus)  }}</td>
                    <td>{!! $request->report !!}</td>
                    <td>{{ $request->recharge->amount ?? '-' }}</td>

                </tr>
            @endforeach

        </tbody>
    </table>
</body>

</html>
