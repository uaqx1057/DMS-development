<?php

namespace App\Livewire\DMS\CoordinatorReport;

use App\Services\BranchService;
use App\Services\BusinessService;
use App\Services\CoordinatorReportService;
use App\Services\DriverService;
use App\Services\FieldService;
use App\Traits\DataTableTrait;
use Livewire\Component;

class CoordinatorReportList extends Component
{
    use DataTableTrait;
    protected CoordinatorReportService $coordinatorReportService;
    protected BusinessService $businessService;
    protected DriverService $driverService;
    protected BranchService $branchService;
    protected FieldService $fieldService;
    private string $main_menu  = 'Coordinator Report';
    private string $menu  = 'Coordinator Report List';

    public $driver_id, $business_id, $branch_id, $date_range;

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
        if (in_array($propertyName, ['driver_id', 'business_id', 'branch_id', 'date_range'])) {
            $this->reset('page');// Reset pagination to first page when filter changes
        }
    }

    public function render()
    {
        $filters = [
            'driver_id' => $this->driver_id,
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
        $drivers = $this->driverService->all();
        $businesses = $this->businessService->all();
        $branches = $this->branchService->all();
        $columns = [
            ['label' => 'Driver Name', 'column' => 'driver_name', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Branch Name', 'column' => 'branch_name', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Total Orders', 'column' => 'Total Orders', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Bonus', 'column' => 'Bonus', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Tip', 'column' => 'Tip', 'isData' => true,'hasRelation'=> false],
            ['label' => 'Other Tip', 'column' => 'Other Tip', 'isData' => false,'hasRelation'=> true],
            ['label' => 'Status', 'column' => 'report_status', 'isData' => true,'hasRelation'=> false],
            ['label' => 'View Report', 'column' => 'view', 'isData' => false,'hasRelation'=> false],
            ['label' => 'Action', 'column' => 'action', 'isData' => false,'hasRelation'=> false],
        ];

        $pendingReportsCount = $coordinatorReports->filter(fn($report) => $report->report_status === 'Pending')->count();
        $completedReportsCount = $coordinatorReports->filter(fn($report) => $report->report_status === 'Approved')->count();
        $totalOrdersCount = $coordinatorReports->sum('total_orders'); // Assumes total_orders is in the data

        return view('livewire.dms.coordinator-report.coordinator-report-list', compact('main_menu', 'menu', 'coordinatorReports', 'fields', 'add_permission', 'edit_permission', 'columns', 'drivers', 'pendingReportsCount', 'completedReportsCount', 'totalOrdersCount', 'businesses', 'branches'));
    }
}
