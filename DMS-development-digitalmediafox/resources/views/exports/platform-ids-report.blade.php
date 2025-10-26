<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Platform Ids Report List</title>
    <style>
        body { font-family: sans-serif; font-size: 7px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Platform Ids Report List</h1>
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
                        <td>{!! data_get($report, $col['column']) !!}</td>
                    @endforeach
                </tr>
                {{-- Per-driver breakdown rows (if available) --}}
                @php
                    $detailsMap = $details ?? [];
                    $biv = data_get($report, 'business_id_value');
                    $driverReports = data_get($detailsMap, $biv) ?: [];
                    
                    // Calculate grand totals for this group
                    $totalOrders = 0;
                    $totalBonus = 0;
                    $totalTips = 0;
                    $totalOtherTips = 0;
                @endphp

                @if(!empty($driverReports))
                    @php
                        $groups = collect($driverReports)->groupBy(function($r){
                            return data_get($r, 'driver.id') ?? ('unknown_' . (data_get($r,'driver.name') ?? uniqid()));
                        });
                    @endphp

                    @foreach($groups as $driverId => $group)
                        @php
                            $group = collect($group);
                            $first = $group->first();
                            $dates = $group->pluck('report_date')->filter()->sort()->values();
                            $from = $dates->first() ?? null;
                            $to = $dates->last() ?? null;
                            
                            // Calculate this driver's totals
                            $driverOrders = 0;
                            $driverBonus = 0;
                            $driverTips = 0;
                            $driverOtherTips = 0;
                            
                            foreach($group as $dr) {
                                foreach($dr['field_values'] ?? [] as $field => $value) {
                                    $val = is_numeric($value['value'] ?? null) ? (float)$value['value'] : 0;
                                    if($field === 'Total Orders') { $driverOrders += $val; $totalOrders += $val; }
                                    elseif($field === 'Bonus') { $driverBonus += $val; $totalBonus += $val; }
                                    elseif($field === 'Tip') { $driverTips += $val; $totalTips += $val; }
                                    elseif($field === 'Other Tip') { $driverOtherTips += $val; $totalOtherTips += $val; }
                                }
                            }
                        @endphp

                        <tr>
                            <td></td>
                            <td colspan="{{ count($columns) }}" style="padding: 0;">
                                <div style="margin: 10px;">
                                    <strong>Report Date Range:</strong> {{ $from ? \Carbon\Carbon::parse($from)->format('d-m-Y') : 'N/A' }} - {{ $to ? \Carbon\Carbon::parse($to)->format('d-m-Y') : 'N/A' }}<br>
                                    <strong>Driver:</strong> {{ data_get($first, 'driver.name') ?? 'N/A' }}<br>
                                    <strong>Branch:</strong> {{ data_get($first, 'branch.name') ?? 'N/A' }}<br>
                                    <strong>Status:</strong> {{ $group->pluck('status')->filter()->unique()->implode(', ') ?: 'N/A' }}
                                </div>
                                
                                <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
                                    <thead>
                                        <tr>
                                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">No.</th>
                                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Date</th>
                                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Iqama Number</th>
                                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Driver Name</th>
                                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Business Name</th>
                                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Branch Name</th>
                                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Total Orders</th>
                                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Bonus</th>
                                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Tip</th>
                                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Other Tip</th>
                                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group as $index => $dr)
                                            @php
                                                $orders = data_get($dr, 'field_values.Total Orders.value', 0);
                                                $bonus = data_get($dr, 'field_values.Bonus.value', 0);
                                                $tips = data_get($dr, 'field_values.Tip.value', 0);
                                                $otherTips = data_get($dr, 'field_values.Other Tip.value', 0);
                                            @endphp
                                            <tr>
                                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">{{ $index + 1 }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">{{ \Carbon\Carbon::parse($dr['report_date'])->format('d-m-Y') }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">{{ data_get($dr, 'driver.iqaama_number', 'N/A') }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">{{ data_get($dr, 'driver.name', 'N/A') }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">{{ data_get($dr, 'business_name', 'N/A') }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">{{ data_get($dr, 'branch.name', 'N/A') }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">{{ $orders }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">{{ $bonus }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">{{ $tips }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">{{ $otherTips }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">{{ $dr['status'] ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                        <tr style="background-color: #f8f9fa;">
                                            <td colspan="6" style="border: 1px solid #dee2e6; padding: 8px; text-align: right;"><strong>Totals:</strong></td>
                                            <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;"><strong>{{ $driverOrders }}</strong></td>
                                            <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;"><strong>{{ $driverBonus }}</strong></td>
                                            <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;"><strong>{{ $driverTips }}</strong></td>
                                            <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;"><strong>{{ $driverOtherTips }}</strong></td>
                                            <td style="border: 1px solid #dee2e6; padding: 8px;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                    
                    {{-- Grand totals box --}}
                    <tr>
                        <td></td>
                        <td colspan="{{ count($columns) }}" style="padding: 15px 10px;">
                            <strong style="display: block; margin-bottom: 5px; text-align: center;">Grand Totals</strong>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr style="background-color: #f8f9fa;">
                                    <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Total Orders</th>
                                    <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Total Bonus</th>
                                    <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Total Tips</th>
                                    <th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Total Other Tips</th>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">{{ $totalOrders }}</td>
                                    <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">{{ $totalBonus }}</td>
                                    <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">{{ $totalTips }}</td>
                                    <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">{{ $totalOtherTips }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
