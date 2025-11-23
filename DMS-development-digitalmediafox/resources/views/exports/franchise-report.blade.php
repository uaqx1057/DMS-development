<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Franchise Report</title>
    <style>
        body { font-family: sans-serif; font-size: 7px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Franchise Report</h1>
    
    @if($dateRange)
        <div class="date-range">
            Report Period: {{ $dateRange['start']->format('d/m/Y') }} to {{ $dateRange['end']->format('d/m/Y') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th>{{ $column['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $totalCashCollected = 0;
                $totalWalletRecharge = 0;
                $totalAmountTransfer = 0;
                $totalRemainingAmount = 0;
            @endphp

            @forelse($branches as $branch)
                <tr>
                    @foreach($columns as $column)
                        @php
                            $value = $branch->{$column['column']} ?? '-';
                            
                            if ($column['column'] === 'total_cash_collected') {
                                $totalCashCollected += (float)str_replace(',', '', $value);
                            } elseif ($column['column'] === 'total_wallet_recharge') {
                                $totalWalletRecharge += (float)str_replace(',', '', $value);
                            } elseif ($column['column'] === 'total_amount_transfer') {
                                $totalAmountTransfer += (float)str_replace(',', '', $value);
                            } elseif ($column['column'] === 'remaining_amount') {
                                $totalRemainingAmount += (float)str_replace(',', '', $value);
                            }
                        @endphp
                        <td @if(in_array($column['column'], ['total_cash_collected', 'total_wallet_recharge', 'total_amount_transfer', 'remaining_amount']))class="numeric"@endif>
                            {{ $value }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}" style="text-align: center;">
                        No records found
                    </td>
                </tr>
            @endforelse

            @if($branches->count() > 0)
                <tr class="total-row">
                    <td colspan="2" style="text-align:right">Total</td>
                    <td class="numeric">{{ number_format($totalCashCollected, 2) }}</td>
                    <td class="numeric">{{ number_format($totalWalletRecharge, 2) }}</td>
                    <td class="numeric">{{ number_format($totalAmountTransfer, 2) }}</td>
                    <td class="numeric">{{ number_format($totalRemainingAmount, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

</body>
</html>