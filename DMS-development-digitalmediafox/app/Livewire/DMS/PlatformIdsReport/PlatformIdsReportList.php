<?php

namespace App\Livewire\DMS\PlatformIdsReport;

use App\Services\BranchService;
use App\Services\BusinessService;
use App\Services\CoordinatorReportService;
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
use App\Models\Field; // if you have dynamic field IDs

class PlatformIdsReportList extends Component
{
    use DataTableTrait;
    protected CoordinatorReportService $coordinatorReportService;
    protected BusinessService $businessService;
    protected DriverService $driverService;
    protected BranchService $branchService;
    protected FieldService $fieldService;
    private string $main_menu  = 'Platform Ids Report';
    private string $menu  = 'Platform Ids Report List';
    
    protected $listeners = ['statusUpdated' => 'refreshReportList'];
    
    use WithFileUploads;

    public $csv_file;


    public $business_id_value, $driver_id, $business_id, $branch_id, $date_range;

    public function mount(CoordinatorReportService $coordinatorReportService, BusinessService $businessService, FieldService $fieldService, DriverService $driverService, BranchService $branchService)
    {
        $perPage = $this->perPage;
        $this->coordinatorReportService = $coordinatorReportService;
        $this->businessService = $businessService;
        $this->fieldService = $fieldService;
        $this->driverService = $driverService;
        $this->branchService = $branchService;
    }

    public function boot(CoordinatorReportService $coordinatorReportService, BusinessService $businessService, FieldService $fieldService, DriverService $driverService, BranchService $branchService)
    {
        $perPage = $this->perPage;
        $this->coordinatorReportService = $coordinatorReportService;
        $this->businessService = $businessService;
        $this->fieldService = $fieldService;
        $this->driverService = $driverService;
        $this->branchService = $branchService;
    }

    public function openReportModal($reportId)
    {
        $report = $this->coordinatorReportService->find($reportId); // Fetch the report data
        $this->dispatch('openModal', $report->toArray());
    }


    public function updated($propertyName)
    {
        // Trigger filtering logic whenever a filter value changes
        if (in_array($propertyName, ['business_id_value','driver_id', 'business_id', 'branch_id', 'date_range'])) {
            $this->reset('page');// Reset pagination to first page when filter changes
        }
    }

        public function customFormat($column, $value)
        {
            if (!empty($column['format'])) {
                switch ($column['format']) {
                    case 'date':
                        return $value ? \Carbon\Carbon::parse($value)->format('d-m-Y') : '';
                    case 'datetime':
                        return $value ? \Carbon\Carbon::parse($value)->format('d-m-Y H:i') : '';
                    case 'currency':
                        return '₹' . number_format($value, 2);
                    // other formats...
                }
            }
        
            return e($value); // default
        }



    public function render()
    {
        if(auth()->user()->role_id!=1){
            $this->branch_id=auth()->user()->branch_id;
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
        $coordinatorReports = $this->coordinatorReportService->all($this->perPage, $this->page, $filters);
        $fields = $this->fieldService->all()->where('is_default', 1)->where('name', '!=', 'Upload Driver Documents')->pluck('name')->toArray();
        $add_permission = CheckPermission(config('const.ADD'), config('const.COORDINATORREPORT'));
        $edit_permission = CheckPermission(config('const.EDIT'), config('const.COORDINATORREPORT'));
        
       // $drivers = $this->driverService->all();
       
       
        
        if (auth()->user()->role_id != 1) {
            $this->branch_id = auth()->user()->branch_id;
        }

        // 🔹 Fetch all or filtered Platform IDs
        if (!empty($this->business_id)) {
            // If a business is selected → show only its IDs
            $businessIds = BusinessId::where('business_id', $this->business_id)
                ->where('is_active', 1)
                ->get();
        } else {
            // Otherwise → show all active IDs
            $businessIds = BusinessId::where('is_active', 1)->get();
        }

        $branches = auth()->user()->role_id != 1
            ? Branch::where('id', $this->branch_id)->get()
            : $this->branchService->all();

        $businesses = $this->businessService->all();
        
        $columns = [
           ['label' => 'Date', 'column' => 'report_date', 'isData' => true, 'hasRelation' => false, 'format' => 'date'],
            ['label' => 'Iqama Number', 'column' => 'driver_iqama', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Driver Name', 'column' => 'driver_name', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Business  Name', 'column' => 'business_name', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Branch Name', 'column' => 'branch_name', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Total Orders', 'column' => 'Total Orders', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Bonus', 'column' => 'Bonus', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Tip', 'column' => 'Tip', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Other Tip', 'column' => 'Other Tip', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Status', 'column' => 'report_status', 'isData' => true,'hasRelation'=> false],
            ['label' => 'View Report', 'column' => 'view', 'isData' => false, 'hasRelation' => false],
        ];

        $pendingReportsCount = $coordinatorReports->filter(fn($report) => $report->report_status === 'Pending')->count();
        $completedReportsCount = $coordinatorReports->filter(fn($report) => $report->report_status === 'Approved')->count();
        $totalOrdersCount = $coordinatorReports->sum('total_orders'); // Assumes total_orders is in the data

        return view('livewire.dms.platform-ids-report.platform-ids-report-list', compact('main_menu', 'menu', 'coordinatorReports', 'fields', 'add_permission', 'edit_permission', 'columns', 'pendingReportsCount', 'completedReportsCount', 'totalOrdersCount', 'businesses', 'branches','businessIds'));
    }
    
    
    public function updatedBusinessId()
    {
        $this->reset('driver_id');
    }



public function exportPdf()
{
    $filters = [
        'business_id_value'   => $this->business_id_value,
        'date_range'  => $this->date_range,
        'business_id' => $this->business_id,
        // 'branch_id'   => $this->branch_id,
    ];

    // Get all filtered reports without pagination (handle perPage = 0 safely)
    $coordinatorReports = $this->coordinatorReportService->all($this->perPage, $this->page, $filters);

    $fields = $this->fieldService->all()
        ->where('is_default', 1)
        ->where('name', '!=', 'Upload Driver Documents')
        ->pluck('name')
        ->toArray();

    $columns = [
        ['label' => 'Date', 'column' => 'report_date'],
        ['label' => 'Iqama Number', 'column' => 'driver_iqama'],
        ['label' => 'Driver Name', 'column' => 'driver_name'],
        ['label' => 'Business Name', 'column' => 'business_name'],
        ['label' => 'Branch Name', 'column' => 'branch_name'],
        ['label' => 'Total Orders', 'column' => 'Total Orders'],
        ['label' => 'Bonus', 'column' => 'Bonus'],
        ['label' => 'Tip', 'column' => 'Tip'],
        ['label' => 'Other Tip', 'column' => 'Other Tip'],
        ['label' => 'Status', 'column' => 'report_status'],
    ];

    // Generate PDF from a Blade view
    $pdf = Pdf::loadView('exports.coordinator-report', [
        'coordinatorReports' => $coordinatorReports,
        'columns'            => $columns,
        'fields'             => $fields
    ]);

    $filename = 'coordinator-report-list-' . now()->format('Y-m-d') . '.pdf';

    return response()->streamDownload(
        fn () => print($pdf->output()),
        $filename
    );
}



}
