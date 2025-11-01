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
    protected $queryString = ['business_id_value','driver_id', 'business_id', 'branch_id', 'date_range', 'page'];

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
        $coordinatorReports = $this->platformIdReportService->all($this->perPage, $this->page, $filters);
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

        $coordinatorReports = $this->platformIdReportService->all(null, null, $filters);

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

        // Get all reports
        $coordinatorReports = $this->platformIdReportService->all(null,null, $filters);

        // ✅ Fetch all dynamic field names (same as in render)
        $businesses = $this->businessService->all();
        $fields = collect();
        foreach ($businesses as $business) {
            $fields = $fields->merge($business->fields->pluck('name'));
        }
        $fields = $fields->unique()->values()->toArray();

        // Get detailed reports per business_id_value
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

        $callback = function() use ($coordinatorReports, $details, $fields) {
            $file = fopen('php://output', 'w');

            // 🔹 Build summary header dynamically
            $summaryHeader = [
                'Sr. No.',
                'Date Range',
                'Platform',
                'Drivers',
                'Branches',
            ];

            // Add dynamic field columns
            foreach ($fields as $fieldName) {
                if ($fieldName !== 'Upload Driver Documents') {
                    $summaryHeader[] = $fieldName;
                }
            }

            // Add Status column at end
            $summaryHeader[] = 'Status';

            fputcsv($file, $summaryHeader);

            // 🔹 Write summary rows
            foreach ($coordinatorReports as $index => $report) {
                $row = [
                    $index + 1,
                    $this->customFormat(['column' => 'date_range', 'format' => 'date'], $report->date_range),
                    $report->business_name,
                    strip_tags($report->assigned_drivers),
                    strip_tags($report->branches),
                ];

                // Add dynamic field values
                foreach ($fields as $fieldName) {
                    if ($fieldName !== 'Upload Driver Documents') {
                        $row[] = $report->{$fieldName} ?? 0;
                    }
                }

                // Add Status
                $row[] = strip_tags($report->report_status);

                fputcsv($file, $row);

                // Blank line before details
                fputcsv($file, []);

                // 🔹 Detail header (per driver)
                $detailHeader = [
                    'No.',
                    'Date',
                    'Iqama Number',
                    'Driver Name',
                    'Business Name',
                    'Branch Name',
                ];

                // Add dynamic field columns to detail section too
                foreach ($fields as $fieldName) {
                    if ($fieldName !== 'Upload Driver Documents') {
                        $detailHeader[] = $fieldName;
                    }
                }

                $detailHeader[] = 'Status';
                fputcsv($file, $detailHeader);

                // Get driver-level detail data
                $biv = data_get($report, 'business_id_value');
                $driverReports = data_get($details, $biv) ?: [];

                if (!empty($driverReports)) {
                    $groups = collect($driverReports)->groupBy(function($r) {
                        return data_get($r, 'driver.id') ?? ('unknown_' . (data_get($r,'driver.name') ?? uniqid()));
                    });

                    foreach ($groups as $driverId => $group) {
                        $driverTotals = array_fill_keys($fields, 0);

                        foreach ($group as $i => $dr) {
                            $detailRow = [
                                $i + 1,
                                data_get($dr, 'report_date') ? \Carbon\Carbon::parse($dr['report_date'])->format('d-m-Y') : 'N/A',
                                data_get($dr, 'driver.iqaama_number', 'N/A'),
                                data_get($dr, 'driver.name', 'N/A'),
                                data_get($dr, 'business_name', 'N/A'),
                                data_get($dr, 'branch.name', 'N/A'),
                            ];

                            foreach ($fields as $fieldName) {
                                if ($fieldName !== 'Upload Driver Documents') {
                                    $val = data_get($dr, "field_values.$fieldName.value", 0);
                                    $num = is_numeric($val) ? (float) $val : 0;
                                    $detailRow[] = $num;
                                    $driverTotals[$fieldName] += $num;
                                }
                            }

                            $detailRow[] = data_get($dr, 'status', 'N/A');
                            fputcsv($file, $detailRow);
                        }

                        // Totals row
                        $dates = collect($group)->pluck('report_date')->filter()->sort()->values();
                        $from = $dates->first();
                        $to = $dates->last();
                        $dateRange = ($from ? \Carbon\Carbon::parse($from)->format('d-m-Y') : 'N/A') . ' - ' .
                                    ($to ? \Carbon\Carbon::parse($to)->format('d-m-Y') : 'N/A');

                        $totalRow = [
                            '',
                            "Date Range: $dateRange",
                            '',
                            '',
                            '',
                            'Totals:',
                        ];

                        foreach ($fields as $fieldName) {
                            if ($fieldName !== 'Upload Driver Documents') {
                                $totalRow[] = $driverTotals[$fieldName] ?? 0;
                            }
                        }

                        $totalRow[] = '';
                        fputcsv($file, []);
                        fputcsv($file, $totalRow);
                        fputcsv($file, []);
                    }
                }

                fputcsv($file, []); // spacing between main reports
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}