<div>
    <x-layouts.breadcrumb
        :main_menu="$main_menu"
        :menu="$menu"
    />

    <x-ui.message />

    <div class="row">
        <div class="col-12">
            <h1 class="mb-3" style="font-size: 18px">Business Report</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card crm-widget">
                <div class="p-0 card-body">
                    <div class="row row-cols-md-3 row-cols-1">
                        <x-ui.report-widget-card text="Total Revenue" icon="ri-exchange-dollar-line" value="{{ $revenueStats->total_revenue }}"/>
                        <x-ui.report-widget-card text="Total Cost" icon="ri-space-ship-line" value="{{ $revenueStats->total_cost }}"/>
                        <x-ui.report-widget-card text="Gross Profit" icon="ri-pulse-line" value="{{ $revenueStats->gross_profit }}"/>
                        <x-ui.report-widget-card text="Total Orders" icon="ri-trophy-line" value="{{ $revenueStats->total_orders }}"/>
                    </div><!-- end row -->
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->

    <div class="row">
        <div class="col-12">
            <h1 class="mb-3" style="font-size: 18px">Order Report</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card crm-widget">
                <div class="p-0 card-body">
                    <div class="row row-cols-md-3 row-cols-1">
                        @foreach ($businesses as $business)
                            <x-ui.report-widget-card text="{{ $business->name }}" icon="ri-exchange-dollar-line" value="{{ $business->total_orders }}"/>
                        @endforeach
                    </div><!-- end row -->
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->

    <x-ui.row>
        <x-ui.col class="col-lg-12">
            <x-ui.card>
                <x-ui.card-header title="Revenue List"/>
                <x-ui.card-body>
                    <x-ui.table>
                        <x-ui.thead>
                            @php
                                $table_headings = [
                                    'Id',
                                    'Name',
                                    'Branch',
                                    'Contract',
                                    'Total Orders',
                                    'Total Revenue',
                                    'Total Cost',
                                    'Profit/Loss'
                                ];
                            @endphp
                            @foreach ($table_headings as $heading)
                                <x-ui.th :label="$heading" wire:key="{{ $loop->iteration }}"/>
                            @endforeach
                        </x-ui.thead>
                        <x-ui.tbody>
                            @foreach ($revenueReports as $item)
                                <x-ui.tr wire:key="{{ $loop->iteration }}">
                                    <x-ui.td>{{ $loop->iteration }}</x-ui.td>
                                    <x-ui.td>{{ $item->name }}</x-ui.td>
                                    <x-ui.td>{{ $item->branch }}</x-ui.td>
                                    <x-ui.td>{{ $item->contract }}</x-ui.td>
                                    <x-ui.td>{{ $item->total_orders }}</x-ui.td>
                                    <x-ui.td>{{ $item->total_revenue }}</x-ui.td>
                                    <x-ui.td>{{ $item->total_cost }}</x-ui.td>
                                    <x-ui.td>{{ $item->profit_loss }}</x-ui.td>
                                </x-ui.tr>
                            @endforeach
                        </x-ui.tbody>
                    </x-ui.table>
                </x-ui.card-body>
            </x-ui.card>
        </x-ui.col>
    </x-ui.row>
</div>
