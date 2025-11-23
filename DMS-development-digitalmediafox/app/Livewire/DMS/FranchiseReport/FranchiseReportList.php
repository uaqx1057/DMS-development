<?php

namespace App\Livewire\DMS\FranchiseReport;

use App\Models\Branch;
use App\Traits\DataTableTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;

class FranchiseReportList extends Component
{
    use DataTableTrait;

    public $branch_id, $date_range;
    protected $queryString = ['branch_id', 'date_range', 'page'];

    private string $main_menu = 'Report';
    private string $menu = 'Franchise Report List';

    public function mount()
    {
        if (session()->has('page')) {
            $this->page = session('page');
        }
    }

    public function boot()
    {
        if (session()->has('page')) {
            $this->page = session('page');
        }
    }

    private function getDateRange()
    {
        if (!empty($this->date_range)) {
            $dates = explode(' to ', $this->date_range);
            if (count($dates) === 2) {
                // Use createFromFormat without timezone conversion
                $startDate = Carbon::createFromFormat('Y-m-d', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', trim($dates[1]))->endOfDay();
                
                return [
                    'start' => $startDate,
                    'end' => $endDate,
                ];
            }
        }
        return null;
    }

    private function getMinMaxDates()
    {
        $coordinatorMinMax = DB::table('coordinator_reports')
            ->selectRaw('MIN(report_date) as min_date, MAX(report_date) as max_date')
            ->first();

        $transferMinMax = DB::table('amount_transfers')
            ->selectRaw('MIN(receipt_date) as min_date, MAX(receipt_date) as max_date')
            ->first();

        $minDate = null;
        $maxDate = null;

        if ($coordinatorMinMax->min_date || $transferMinMax->min_date) {
            $dates = array_filter([
                $coordinatorMinMax->min_date,
                $transferMinMax->min_date,
            ]);
            $minDate = !empty($dates) ? min($dates) : null;

            $dates = array_filter([
                $coordinatorMinMax->max_date,
                $transferMinMax->max_date,
            ]);
            $maxDate = !empty($dates) ? max($dates) : null;
        }

        return [
            'min' => $minDate,
            'max' => $maxDate,
        ];
    }

    public function clearFilters()
    {
        $this->date_range = '';
        $this->branch_id = '';
    }

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $add_permission = false;
        $edit_permission = false;

        $columns = [
            ['label' => 'Date Range', 'column' => 'date_range_display', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Branch', 'column' => 'name', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Cash Collect', 'column' => 'total_cash_collected', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Wallet Recharge', 'column' => 'total_wallet_recharge', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Amount Transfer', 'column' => 'total_amount_transfer', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Remaining Amount', 'column' => 'remaining_amount', 'isData' => true, 'hasRelation' => false],
        ];

        $dateRange = $this->getDateRange();

        // Debug: Check what dates are being processed
        if ($dateRange) {
            \Log::info('Date Range Filter:', [
                'input' => $this->date_range,
                'start' => $dateRange['start']->format('Y-m-d H:i:s'),
                'end' => $dateRange['end']->format('Y-m-d H:i:s')
            ]);
        }

        // Build base query
        $query = Branch::query();

        // Apply branch filter
        if (!empty($this->branch_id)) {
            $query->where('id', $this->branch_id);
        } elseif (auth()->user()->role_id != 1) {
            $query->where('id', auth()->user()->branch_id);
        }

        // Apply date range filtering for reports
        if ($dateRange) {
            $query->where(function ($q) use ($dateRange) {
                $q->whereHas('coordinatorReports', function ($subQ) use ($dateRange) {
                    $subQ->whereBetween('report_date', [$dateRange['start'], $dateRange['end']]);
                })
                ->orWhereHas('amountTransfer', function ($subQ) use ($dateRange) {
                    $subQ->whereBetween('receipt_date', [$dateRange['start'], $dateRange['end']]);
                });
            });
        }

        // Get paginated results
        $reports = $query
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);

        // Manual calculations for each branch
        $reports->getCollection()->transform(function ($report) use ($dateRange) {
            
            // Calculate wallet recharge with date filter
            $walletRechargeQuery = $report->walletRecharges()->where('field_id', 22);
            if ($dateRange) {
                $walletRechargeQuery->whereHas('coordinatorReport', function ($q) use ($dateRange) {
                    $q->whereBetween('report_date', [$dateRange['start'], $dateRange['end']]);
                });
            }
            $totalWalletRecharge = $walletRechargeQuery->sum('value');

            // Calculate cash collected with date filter
            $cashCollectedQuery = $report->cashCollected()->where('field_id', 7);
            if ($dateRange) {
                $cashCollectedQuery->whereHas('coordinatorReport', function ($q) use ($dateRange) {
                    $q->whereBetween('report_date', [$dateRange['start'], $dateRange['end']]);
                });
            }
            $totalCashCollected = $cashCollectedQuery->sum('value');

            // Calculate amount transfer with date filter
            $amountTransferQuery = $report->amountTransfer();
            if ($dateRange) {
                $amountTransferQuery->whereBetween('receipt_date', [$dateRange['start'], $dateRange['end']]);
            }
            $totalAmountTransfer = $amountTransferQuery->sum('amount');

            // Set date range display - show selected range
            if ($dateRange) {
                $report->date_range_display = $dateRange['start']->format('d/m/Y') . ' to ' . $dateRange['end']->format('d/m/Y');
            } else {
                // Get actual dates from records if no filter
                $coordinatorDates = $report->coordinatorReports->pluck('report_date')->filter()->map(function ($date) {
                    return Carbon::parse($date);
                })->toArray();

                $transferDates = $report->amountTransfer->pluck('receipt_date')->filter()->map(function ($date) {
                    return Carbon::parse($date);
                })->toArray();

                $allDates = array_merge($coordinatorDates, $transferDates);

                if (!empty($allDates)) {
                    $minDate = min($allDates);
                    $maxDate = max($allDates);
                    $report->date_range_display = $minDate->format('d/m/Y') . ' to ' . $maxDate->format('d/m/Y');
                } else {
                    $report->date_range_display = '-';
                }
            }

            // Format currency values
            $report->total_amount_transfer = number_format($totalAmountTransfer, 2, '.', '');
            $report->total_wallet_recharge = number_format($totalWalletRecharge, 2, '.', '');
            $report->total_cash_collected = number_format($totalCashCollected, 2, '.', '');

            // Calculate remaining amount
            $report->remaining_amount = number_format(
                $totalCashCollected - $totalWalletRecharge - $totalAmountTransfer,
                2,
                '.',
                ''
            );

            return $report;
        });

        // Get branches for dropdown
        if (auth()->user()->role_id != 1) {
            $branches = Branch::where('id', auth()->user()->branch_id)->get();
        } else {
            $branches = Branch::all();
        }

        return view('livewire.dms.franchise-report.franchise-report-list', compact(
            'reports',
            'main_menu',
            'menu',
            'add_permission',
            'edit_permission',
            'columns',
            'branches',
        ));
    }

    public function exportCsv()
    {
        $dateRange = $this->getDateRange();

        // Build base query
        $query = Branch::query();

        // Apply branch filter
        if (!empty($this->branch_id)) {
            $query->where('id', $this->branch_id);
        } elseif (auth()->user()->role_id != 1) {
            $query->where('id', auth()->user()->branch_id);
        }

        // Apply date range filtering
        if ($dateRange) {
            $query->where(function ($q) use ($dateRange) {
                $q->whereHas('coordinatorReports', function ($subQ) use ($dateRange) {
                    $subQ->whereBetween('report_date', [$dateRange['start'], $dateRange['end']]);
                })
                ->orWhereHas('amountTransfer', function ($subQ) use ($dateRange) {
                    $subQ->whereBetween('receipt_date', [$dateRange['start'], $dateRange['end']]);
                });
            });
        }

        $branches = $query->get();

        // Prepare headers for CSV
        $headers = [
            'Date Range',
            'Branch',
            'Cash Collect',
            'Wallet Recharge',
            'Amount Transfer',
            'Remaining Amount',
        ];

        // Create CSV content
        $csvContent = fopen('php://temp', 'r+');
        fputcsv($csvContent, $headers);

        // Prepare column sums for totals row (index-based)
        $columnSums = array_fill(0, count($headers), 0.0);

        foreach ($branches as $branch) {
            // Calculate wallet recharge with date filter
            $walletRechargeQuery = $branch->walletRecharges()->where('field_id', 22);
            if ($dateRange) {
                $walletRechargeQuery->whereHas('coordinatorReport', function ($q) use ($dateRange) {
                    $q->whereBetween('report_date', [$dateRange['start'], $dateRange['end']]);
                });
            }
            $totalWalletRecharge = $walletRechargeQuery->sum('value');

            // Calculate cash collected with date filter
            $cashCollectedQuery = $branch->cashCollected()->where('field_id', 7);
            if ($dateRange) {
                $cashCollectedQuery->whereHas('coordinatorReport', function ($q) use ($dateRange) {
                    $q->whereBetween('report_date', [$dateRange['start'], $dateRange['end']]);
                });
            }
            $totalCashCollected = $cashCollectedQuery->sum('value');

            // Calculate amount transfer with date filter
            $amountTransferQuery = $branch->amountTransfer();
            if ($dateRange) {
                $amountTransferQuery->whereBetween('receipt_date', [$dateRange['start'], $dateRange['end']]);
            }
            $totalAmountTransfer = $amountTransferQuery->sum('amount');

            // Set date range display
            if ($dateRange) {
                $dateRangeDisplay = $dateRange['start']->format('d/m/Y') . ' to ' . $dateRange['end']->format('d/m/Y');
            } else {
                $coordinatorDates = $branch->coordinatorReports->pluck('report_date')->filter()->map(function ($date) {
                    return Carbon::parse($date);
                })->toArray();

                $transferDates = $branch->amountTransfer->pluck('receipt_date')->filter()->map(function ($date) {
                    return Carbon::parse($date);
                })->toArray();

                $allDates = array_merge($coordinatorDates, $transferDates);

                if (!empty($allDates)) {
                    $minDate = min($allDates);
                    $maxDate = max($allDates);
                    $dateRangeDisplay = $minDate->format('d/m/Y') . ' to ' . $maxDate->format('d/m/Y');
                } else {
                    $dateRangeDisplay = '-';
                }
            }

            // Calculate remaining amount
            $remainingAmount = $totalCashCollected - $totalWalletRecharge - $totalAmountTransfer;

            $row = [
                $dateRangeDisplay,
                $branch->name,
                $totalCashCollected,
                $totalWalletRecharge,
                $totalAmountTransfer,
                $remainingAmount,
            ];

            // Write row and accumulate numeric sums per column index
            foreach ($row as $index => $cell) {
                // Skip summing date range and branch name columns
                if (isset($headers[$index]) && in_array($headers[$index], ['Date Range', 'Branch'])) {
                    continue;
                }

                if (is_numeric($cell)) {
                    $columnSums[$index] += (float) $cell;
                    // format numeric cell for CSV
                    $row[$index] = number_format((float) $cell, 2);
                }
            }

            fputcsv($csvContent, $row);
        }

        // Append totals row
        $totalsRow = array_fill(0, count($headers), '');
        $totalsRow[0] = 'Total';
        foreach ($columnSums as $index => $sum) {
            if ($index === 0 || $index === 1) continue; // skip first two columns
            if ($sum != 0) {
                $totalsRow[$index] = number_format($sum, 2);
            }
        }

        fputcsv($csvContent, $totalsRow);

        rewind($csvContent);
        $csvData = stream_get_contents($csvContent);
        fclose($csvContent);

        $filename = 'franchise-report-list-' . now()->format('Y-m-d') . '.csv';
        
        return response()->streamDownload(
            fn () => print($csvData),
            $filename,
            ['Content-Type' => 'text/csv']
        );
    }

    public function exportPdf()
    {
        $dateRange = $this->getDateRange();

        // Build base query
        $query = Branch::query();

        // Apply branch filter
        if (!empty($this->branch_id)) {
            $query->where('id', $this->branch_id);
        } elseif (auth()->user()->role_id != 1) {
            $query->where('id', auth()->user()->branch_id);
        }

        // Apply date range filtering
        if ($dateRange) {
            $query->where(function ($q) use ($dateRange) {
                $q->whereHas('coordinatorReports', function ($subQ) use ($dateRange) {
                    $subQ->whereBetween('report_date', [$dateRange['start'], $dateRange['end']]);
                })
                ->orWhereHas('amountTransfer', function ($subQ) use ($dateRange) {
                    $subQ->whereBetween('receipt_date', [$dateRange['start'], $dateRange['end']]);
                });
            });
        }

        $branches = $query->get();

        $columns = [
            ['label' => 'Date Range', 'column' => 'date_range_display'],
            ['label' => 'Branch', 'column' => 'name'],
            ['label' => 'Cash Collect', 'column' => 'total_cash_collected'],
            ['label' => 'Wallet Recharge', 'column' => 'total_wallet_recharge'],
            ['label' => 'Amount Transfer', 'column' => 'total_amount_transfer'],
            ['label' => 'Remaining Amount', 'column' => 'remaining_amount'],
        ];

        // Transform branches with calculations
        $transformedBranches = $branches->map(function ($branch) use ($dateRange) {
            // Calculate wallet recharge with date filter
            $walletRechargeQuery = $branch->walletRecharges()->where('field_id', 22);
            if ($dateRange) {
                $walletRechargeQuery->whereHas('coordinatorReport', function ($q) use ($dateRange) {
                    $q->whereBetween('report_date', [$dateRange['start'], $dateRange['end']]);
                });
            }
            $totalWalletRecharge = $walletRechargeQuery->sum('value');

            // Calculate cash collected with date filter
            $cashCollectedQuery = $branch->cashCollected()->where('field_id', 7);
            if ($dateRange) {
                $cashCollectedQuery->whereHas('coordinatorReport', function ($q) use ($dateRange) {
                    $q->whereBetween('report_date', [$dateRange['start'], $dateRange['end']]);
                });
            }
            $totalCashCollected = $cashCollectedQuery->sum('value');

            // Calculate amount transfer with date filter
            $amountTransferQuery = $branch->amountTransfer();
            if ($dateRange) {
                $amountTransferQuery->whereBetween('receipt_date', [$dateRange['start'], $dateRange['end']]);
            }
            $totalAmountTransfer = $amountTransferQuery->sum('amount');

            // Set date range display
            if ($dateRange) {
                $branch->date_range_display = $dateRange['start']->format('d/m/Y') . ' to ' . $dateRange['end']->format('d/m/Y');
            } else {
                $coordinatorDates = $branch->coordinatorReports->pluck('report_date')->filter()->map(function ($date) {
                    return Carbon::parse($date);
                })->toArray();

                $transferDates = $branch->amountTransfer->pluck('receipt_date')->filter()->map(function ($date) {
                    return Carbon::parse($date);
                })->toArray();

                $allDates = array_merge($coordinatorDates, $transferDates);

                if (!empty($allDates)) {
                    $minDate = min($allDates);
                    $maxDate = max($allDates);
                    $branch->date_range_display = $minDate->format('d/m/Y') . ' to ' . $maxDate->format('d/m/Y');
                } else {
                    $branch->date_range_display = '-';
                }
            }

            // Format currency values
            $branch->total_amount_transfer = number_format($totalAmountTransfer, 2, '.', '');
            $branch->total_wallet_recharge = number_format($totalWalletRecharge, 2, '.', '');
            $branch->total_cash_collected = number_format($totalCashCollected, 2, '.', '');

            // Calculate remaining amount
            $branch->remaining_amount = number_format(
                $totalCashCollected - $totalWalletRecharge - $totalAmountTransfer,
                2,
                '.',
                ''
            );

            return $branch;
        });

        // Generate PDF from a Blade view
        $pdf = Pdf::loadView('exports.franchise-report', [
            'branches' => $transformedBranches,
            'columns' => $columns,
            'dateRange' => $dateRange,
        ]);

        $filename = 'franchise-report-list-' . now()->format('Y-m-d') . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }
}