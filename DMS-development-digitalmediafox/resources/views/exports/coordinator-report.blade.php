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
            @foreach ($coordinatorReports as $report)
                <tr>
                    <td>{{ $loop->iteration }}</td> {{-- Serial number --}}
                    @foreach ($columns as $col)
                        <td>{{ data_get($report, $col['column']) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
