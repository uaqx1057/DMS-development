<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Coordinator Report List</title>
    <style>
        body { font-family: sans-serif; font-size: 7px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Coordinator Report List</h1>
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

            @foreach ($coordinatorReports as $report)
                <tr>
                    <td>{{ $loop->iteration }}</td> {{-- Serial number --}}
                    @foreach ($columns as $index => $col)
                                @php
                                    $val = data_get($report, $col['column']);
                                    // Skip summing iqama/driver identifier column
                                    if ($col['column'] !== 'driver_iqama' && is_numeric($val)) {
                                        $sums[$index] += (float) $val;
                                    }
                                @endphp
                                <td>{{ $val }}</td>
                            @endforeach
                </tr>
            @endforeach

            {{-- Totals row --}}
            <tr>
                <td><strong>Total</strong></td>
                @foreach ($columns as $index => $col)
                    @php
                        $sum = $sums[$index] ?? 0;
                    @endphp
                    <td>
                        @if(is_numeric($sum) && $sum != 0)
                            <strong>{{ number_format($sum, 2) }}</strong>
                        @else
                            {{-- leave blank for non-numeric or zero totals --}}
                        @endif
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
</body>
</html>
