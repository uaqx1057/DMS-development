<?php

namespace App\Livewire\DMS\PlatformIdsReport;

use App\Services\BranchService;
use App\Services\BusinessService;
use App\Services\PlatformIdReportService;
use App\Services\DriverService;
use App\Services\FieldService;
use App\Traits\DataTableTrait;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\WithFileUploads;
use App\Models\Driver;
use App\Models\Business;
use App\Models\Branch;
use App\Models\CoordinatorReport;
use App\Models\BusinessCoordinatorReport;
use App\Models\CoordinatorReportFieldValue;
use App\Models\BusinessId;
use App\Models\Field;

class PlatformIdsReportList extends Component
{
    use DataTableTrait;
    use WithFileUploads;

    protected PlatformIdReportService $platformIdReportService;
    protected BusinessService $businessService;
    protected DriverService $driverService;
    protected BranchService $branchService;
    protected FieldService $fieldService;
    
    private string $main_menu = 'Platform Ids Report';
    private string $menu = 'Platform Ids Report List';
    
    protected $listeners = ['statusUpdated' => 'refreshReportList'];
    
    public $csv_file;
    public $business_id_value, $driver_id, $business_id, $branch_id, $date_range;

    public function mount(PlatformIdReportService $platformIdReportService, BusinessService $businessService, FieldService $fieldService, DriverService $driverService, BranchService $branchService)
    {
        $this->platformIdReportService = $platformIdReportService;
        $this->businessService = $businessService;
        $this->fieldService = $fieldService;
        $this->driverService = $driverService;
        $this->branchService = $branchService;
    }

    public function boot(PlatformIdReportService $platformIdReportService, BusinessService $businessService, FieldService $fieldService, DriverService $driverService, BranchService $branchService)
    {
        $this->platformIdReportService = $platformIdReportService;
        $this->businessService = $businessService;
        $this->fieldService = $fieldService;
        $this->driverService = $driverService;
        $this->branchService = $branchService;
    }

    /**
     * Open modal with all reports for a specific business_id_value
     */
    public function openReportModal($businessIdValue)
    {
        // Get all reports for this business_id_value
        $reports = $this->platformIdReportService->getReportsByBusinessIdValue($businessIdValue);
        
        // Dispatch event to modal component
        $this->dispatch('openPlatformIdModal', [
            'business_id_value' => $businessIdValue,
            'reports' => $reports->toArray()
        ]);
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['business_id_value','driver_id', 'business_id', 'branch_id', 'date_range'])) {
            $this->reset('page');
        }
    }

    public function customFormat($column, $value)
    {
        if ($column['column'] === 'report_date' || $column['column'] === 'date_range') {
            if (is_string($value) && str_contains($value, ' to ')) {
                return $value;
            }
            return $value ? \Carbon\Carbon::parse($value)->format('d-m-Y') : '';
        }

        if (!empty($column['format'])) {
            switch ($column['format']) {
                case 'date':
                    return $value ? \Carbon\Carbon::parse($value)->format('d-m-Y') : '';
                case 'datetime':
                    return $value ? \Carbon\Carbon::parse($value)->format('d-m-Y H:i') : '';
                case 'currency':
                    return '₹' . number_format($value, 2);
            }
        }

        return $value;
    }

    public function render()
    {
        if(auth()->user()->role_id != 1){
            $this->branch_id = auth()->user()->branch_id;
        }
        
        $filters = [
            'driver_id' => $this->driver_id,
            'business_id_value' => $this->business_id_value,
            'date_range' => $this->date_range,
            'business_id' => $this->business_id,
            'branch_id' => $this->branch_id,
        ];
        
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $coordinatorReports = $this->platformIdReportService->all($this->perPage, null, $filters);
        // Get all businesses and their fields
        $businesses = $this->businessService->all();
        $fields = collect();
        foreach ($businesses as $business) {
            $fields = $fields->merge($business->fields->pluck('name'));
        }
        $fields = $fields->unique()->values()->toArray();
        $add_permission = CheckPermission(config('const.ADD'), config('const.COORDINATORREPORT'));
        $edit_permission = CheckPermission(config('const.EDIT'), config('const.COORDINATORREPORT'));

        if (!empty($this->business_id)) {
            $businessIds = BusinessId::where('business_id', $this->business_id)
                ->where('is_active', 1)
                ->get();
        } else {
            $businessIds = BusinessId::where('is_active', 1)->get();
        }

        $branches = auth()->user()->role_id != 1
            ? Branch::where('id', $this->branch_id)->get()
            : $this->branchService->all();

        // Build columns dynamically
        $columns = [
            ['label' => 'Date Range', 'column' => 'date_range', 'isData' => true, 'hasRelation' => false, 'format' => 'date'],
            ['label' => 'Platform', 'column' => 'business_name', 'isData' => true, 'hasRelation'=> false],
            ['label' => 'Drivers', 'column' => 'assigned_drivers', 'isData' => true, 'hasRelation'=> false, 'cssClass' => 'whitespace-pre-line'],
            ['label' => 'Branches', 'column' => 'branches', 'isData' => true, 'hasRelation'=> false, 'isHtml' => true],
        ];
        // Add all dynamic business fields except 'Upload Driver Documents'
        foreach ($fields as $fieldName) {
            if ($fieldName !== 'Upload Driver Documents') {
                $columns[] = [
                    'label' => $fieldName,
                    'column' => $fieldName,
                    'isData' => true,
                    'hasRelation' => false
                ];
            }
        }
        // Add status and view columns
        $columns = array_merge($columns, [
            ['label' => 'Status', 'column' => 'report_status', 'isData' => true, 'hasRelation'=> false, 'isHtml' => true],
            ['label' => 'View Report', 'column' => 'view', 'isData' => false, 'hasRelation' => false],
        ]);

        $pendingReportsCount = $coordinatorReports->filter(fn($report) => $report->report_status === 'Pending')->count();
        $completedReportsCount = $coordinatorReports->filter(fn($report) => $report->report_status === 'Approved')->count();
        $totalOrdersCount = $coordinatorReports->sum('total_orders');

        return view('livewire.dms.platform-ids-report.platform-ids-report-list', compact('main_menu', 'menu', 'coordinatorReports', 'fields', 'add_permission', 'edit_permission', 'columns', 'pendingReportsCount', 'completedReportsCount', 'totalOrdersCount', 'businesses', 'branches','businessIds'));
    }
    
    public function updatedBusinessId()
    {
        $this->reset('driver_id');
    }

    public function exportPdf()
    {
        $filters = [
            'business_id_value' => $this->business_id_value,
            'date_range' => $this->date_range,
            'business_id' => $this->business_id,
        ];

        $coordinatorReports = $this->platformIdReportService->all($this->perPage, $this->page, $filters);

        $fields = $this->fieldService->all()
            ->where('is_default', 1)
            ->where('name', '!=', 'Upload Driver Documents')
            ->pluck('name')
            ->toArray();

        $columns = [
            ['label' => 'Date Range', 'column' => 'date_range', 'isData' => true, 'hasRelation' => false, 'format' => 'date'],
            ['label' => 'Platform', 'column' => 'business_name', 'isData' => true, 'hasRelation'=> false],
            ['label' => 'Drivers', 'column' => 'assigned_drivers', 'isData' => true, 'hasRelation'=> false, 'cssClass' => 'whitespace-pre-line'],
            ['label' => 'Branches', 'column' => 'branches', 'isData' => true, 'hasRelation'=> false, 'isHtml' => true],
            ['label' => 'Total Orders', 'column' => 'Total Orders', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Total Bonus', 'column' => 'Bonus', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Total Tips', 'column' => 'Tip', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Total Other Tips', 'column' => 'Other Tip', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Status', 'column' => 'report_status', 'isData' => true,'hasRelation'=> false, 'isHtml' => true],
        ];

        // Prepare detailed per-business reports so the view can render per-driver breakdowns
        $details = [];
        foreach ($coordinatorReports as $report) {
            $biv = data_get($report, 'business_id_value');
            if ($biv) {
                $details[$biv] = $this->platformIdReportService->getReportsByBusinessIdValue($biv)->toArray();
            }
        }

        $pdf = Pdf::loadView('exports.platform-ids-report', [
            'coordinatorReports' => $coordinatorReports,
            'columns' => $columns,
            'fields' => $fields,
            'details' => $details,
        ]);

        $filename = 'platform-ids-report-list-' . now()->format('Y-m-d') . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }

    public function exportCsv()
    {
        $filters = [
            'business_id_value' => $this->business_id_value,
            'date_range' => $this->date_range,
            'business_id' => $this->business_id,
        ];

        $coordinatorReports = $this->platformIdReportService->all($this->perPage, $this->page, $filters);
        $details = [];
        foreach ($coordinatorReports as $report) {
            $biv = data_get($report, 'business_id_value');
            if ($biv) {
                $details[$biv] = $this->platformIdReportService->getReportsByBusinessIdValue($biv)->toArray();
            }
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=platform-ids-report-list-' . now()->format('Y-m-d') . '.csv',
        ];

        $callback = function() use ($coordinatorReports, $details) {
            $file = fopen('php://output', 'w');
            
            // Write summary header
            fputcsv($file, [
            'Sr. No.',
            'Date Range',
            'Platform',
            'Drivers',
            'Branches',
                'Total Orders',
                'Total Bonus',
                'Total Tips',
                'Total Other Tips',
                'Status'
            ]);

            // Write summary rows
        foreach ($coordinatorReports as $index => $report) {
                fputcsv($file, [
                $index + 1,
                $this->customFormat(['column' => 'date_range', 'format' => 'date'], $report->date_range),
                $report->business_name,
                strip_tags($report->assigned_drivers),
                strip_tags($report->branches),
                    $report->{'Total Orders'},
                    $report->{'Bonus'},
                    $report->{'Tip'},
                    $report->{'Other Tip'},
                    strip_tags($report->report_status)
                ]);

                // Add a blank line before details
                fputcsv($file, []);

                // Write details header
                fputcsv($file, [
                    'No.',
                    'Date',
                    'Iqama Number',
                    'Driver Name',
                    'Business Name',
                    'Branch Name',
                    'Total Orders',
                    'Bonus',
                    'Tip',
                    'Other Tip',
                    'Status'
                ]);

                // Get and process details for this report
                $biv = data_get($report, 'business_id_value');
                $driverReports = data_get($details, $biv) ?: [];

                if (!empty($driverReports)) {
                    $groups = collect($driverReports)->groupBy(function($r) {
                        return data_get($r, 'driver.id') ?? ('unknown_' . (data_get($r,'driver.name') ?? uniqid()));
                    });

                    foreach ($groups as $driverId => $group) {
                        $driverTotal = [
                            'orders' => 0,
                            'bonus' => 0,
                            'tips' => 0,
                            'otherTips' => 0
                        ];

                        foreach ($group as $index => $dr) {
                            // Write detail row
                            $orders = data_get($dr, 'field_values.Total Orders.value', 0);
                            $bonus = data_get($dr, 'field_values.Bonus.value', 0);
                            $tips = data_get($dr, 'field_values.Tip.value', 0);
                            $otherTips = data_get($dr, 'field_values.Other Tip.value', 0);

                            fputcsv($file, [
                                $index + 1,
                                data_get($dr, 'report_date') ? \Carbon\Carbon::parse($dr['report_date'])->format('d-m-Y') : 'N/A',
                                data_get($dr, 'driver.iqaama_number', 'N/A'),
                                data_get($dr, 'driver.name', 'N/A'),
                                data_get($dr, 'business_name', 'N/A'),
                                data_get($dr, 'branch.name', 'N/A'),
                                $orders,
                                $bonus,
                                $tips,
                                $otherTips,
                                $dr['status'] ?? 'N/A'
                            ]);

                            $driverTotal['orders'] += $orders;
                            $driverTotal['bonus'] += $bonus;
                            $driverTotal['tips'] += $tips;
                            $driverTotal['otherTips'] += $otherTips;
                        }

                        // Write driver total
                        $dates = collect($group)->pluck('report_date')->filter()->sort()->values();
                        $from = $dates->first();
                        $to = $dates->last();
                        $dateRange = ($from ? \Carbon\Carbon::parse($from)->format('d-m-Y') : 'N/A') . ' - ' . 
                                   ($to ? \Carbon\Carbon::parse($to)->format('d-m-Y') : 'N/A');

                        fputcsv($file, []);
                        fputcsv($file, [
                            '',
                            "Date Range: $dateRange",
                            '',
                            '',
                            '',
                            'Totals:',
                            "{$driverTotal['orders']}",
                            "{$driverTotal['bonus']}",
                            "{$driverTotal['tips']}",
                            "{$driverTotal['otherTips']}",
                            ''
                        ]);
                        
                        fputcsv($file, []);
                    }
                }

                fputcsv($file, []);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}