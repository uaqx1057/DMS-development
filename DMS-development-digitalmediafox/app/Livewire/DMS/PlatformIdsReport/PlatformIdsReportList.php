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
        $coordinatorReports = $this->platformIdReportService->all($this->perPage, $this->page, $filters);
        $fields = $this->fieldService->all()->where('is_default', 1)->where('name', '!=', 'Upload Driver Documents')->pluck('name')->toArray();
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

        $businesses = $this->businessService->all();
        
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
            ['label' => 'View Report', 'column' => 'view', 'isData' => false, 'hasRelation' => false],
        ];

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
}