<?php

namespace App\Livewire\DMS\CoordinatorReport;

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
use Livewire\WithPagination;

use App\Models\Field; // if you have dynamic field IDs

class CoordinatorReportList extends Component
{
    use DataTableTrait,WithPagination;
    protected CoordinatorReportService $coordinatorReportService;
    protected BusinessService $businessService;
    protected DriverService $driverService;
    protected BranchService $branchService;
    protected FieldService $fieldService;
    private string $main_menu  = 'Coordinator Report';
    private string $menu  = 'Coordinator Report List';
    
    protected $listeners = ['statusUpdated' => 'refreshReportList'];
    
    use WithFileUploads;

    public $csv_file;


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
        $this->dispatch('openModal', $reportId);
    }


    public function updated($propertyName)
    {
        // Trigger filtering logic whenever a filter value changes
        if (in_array($propertyName, ['driver_id', 'business_id', 'branch_id', 'date_range'])) {
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
            $this->branch_id = auth()->user()->branch_id;
        }
        
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        
        // Get businesses first since we need them for the fields
        $businesses = $this->businessService->all();
        
        $filters = [
            'driver_id' => $this->driver_id,
            'date_range' => $this->date_range,
            'business_id' => $this->business_id,
            'branch_id' => $this->branch_id,
        ];
        
        $coordinatorReports = $this->coordinatorReportService->all($this->perPage, null, $filters);

        // Get fields that are assigned to any business
        $fields = collect();
        foreach ($businesses as $business) {
            $fields = $fields->merge($business->fields->pluck('name'));
        }
        $fields = $fields->unique()->values()->toArray();
        $add_permission = CheckPermission(config('const.ADD'), config('const.COORDINATORREPORT'));
        $edit_permission = CheckPermission(config('const.EDIT'), config('const.COORDINATORREPORT'));
        
       // $drivers = $this->driverService->all();
       
       
        
        if (auth()->user()->role_id!=1) {
        $drivers = Driver::where('branch_id',$this->branch_id)->get();
        $branches = Branch::where('id',$this->branch_id)->get();
        }else {
            $drivers = Driver::all();
            $branches = $this->branchService->all();
        }
        
        // $businesses already defined above
        
        $columns = [
            ['label' => 'Date', 'column' => 'report_date', 'isData' => true, 'hasRelation' => false, 'format' => 'date'],
            ['label' => 'Iqama Number', 'column' => 'driver_iqama', 'isData' => true, 'hasRelation'=> false],
            ['label' => 'Driver Name', 'column' => 'driver_name', 'isData' => true, 'hasRelation'=> false],
            ['label' => 'Business Name', 'column' => 'business_name', 'isData' => true, 'hasRelation'=> false],
            ['label' => 'Branch Name', 'column' => 'branch_name', 'isData' => true, 'hasRelation'=> false]
        ];

        // Get only the fields that are assigned to businesses
        $businessFields = collect();
        foreach ($businesses as $business) {
            $businessFields = $businessFields->merge($business->fields);
        }
        
        // Remove duplicates and sort by name
        $businessFields = $businessFields->unique('id')->sortBy('name');
        
        // Add only the fields that are assigned to businesses
        foreach ($businessFields as $field) {
            if ($field->name !== 'Upload Driver Documents') {
                $columns[] = [
                    'label' => $field->name,
                    'column' => $field->name,
                    'isData' => true,
                    'hasRelation' => false,
                    'format' => $field->type === 'number' ? 'currency' : null
                ];
            }
        }

        // Add status and action columns at the end
        $columns = array_merge($columns, [
            ['label' => 'Status', 'column' => 'report_status', 'isData' => true, 'hasRelation'=> false],
            ['label' => 'View Report', 'column' => 'view', 'isData' => false, 'hasRelation' => false],
            ['label' => 'Action', 'column' => 'action', 'isData' => false, 'hasRelation'=> false],
        ]);

        $pendingReportsCount = $coordinatorReports->filter(fn($report) => $report->report_status === 'Pending')->count();
        $completedReportsCount = $coordinatorReports->filter(fn($report) => $report->report_status === 'Approved')->count();
        
        // Calculate total orders by summing the "Total Orders" field value from all reports
        $totalOrdersCount = $coordinatorReports->sum(function($report) {
            // Get the Total Orders field value from the expanded attributes
            return floatval($report->{'Total Orders'} ?? 0);
        });

        return view('livewire.dms.coordinator-report.coordinator-report-list', compact('main_menu', 'menu', 'coordinatorReports', 'fields', 'add_permission', 'edit_permission', 'columns', 'drivers', 'pendingReportsCount', 'completedReportsCount', 'totalOrdersCount', 'businesses', 'branches'));
    }
    
    
    


public function exportCsv()
{
    $filters = [
        'driver_id'   => $this->driver_id,
        'date_range'  => $this->date_range,
        'business_id' => $this->business_id,
        'branch_id'   => $this->branch_id,
    ];

    // Get reports with a large page size to effectively get all records
    $coordinatorReports = $this->coordinatorReportService->all(999999, 1, $filters);

    // Get businesses and their fields
    $businesses = $this->businessService->all();
    $businessFields = collect();
    foreach ($businesses as $business) {
        $businessFields = $businessFields->merge($business->fields);
    }
    $businessFields = $businessFields->unique('id')->sortBy('name');

    // Prepare headers for CSV
    $headers = [
        'Report Date',
        'Iqama Number',
        'Driver Name',
        'Business Name',
        'Branch Name',
    ];

    // Add business fields to headers
    foreach ($businessFields as $field) {
        if ($field->name !== 'Upload Driver Documents') {
            $headers[] = $field->name;
        }
    }

    // Create CSV content
    $csvContent = fopen('php://temp', 'r+');
    fputcsv($csvContent, $headers);
    // Prepare column sums for totals row (index-based)
    $columnSums = array_fill(0, count($headers), 0.0);

    foreach ($coordinatorReports as $report) {
        $row = [
            $report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('d-m-Y') : '',
            $report->driver_iqama,
            $report->driver_name,
            $report->business_name,
            $report->branch_name,
        ];

        // Add field values
        foreach ($businessFields as $field) {
            if ($field->name !== 'Upload Driver Documents') {
                $value = $report->{$field->name};
                // Keep raw for numeric detection
                $row[] = $value ?? '';
            }
        }

        // Write row and accumulate numeric sums per column index
        foreach ($row as $index => $cell) {
            // Skip summing Iqama Number column (don't include in totals)
            if (isset($headers[$index]) && $headers[$index] === 'Iqama Number') {
                // leave value as-is (no summing/formatting)
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

    // Append totals row: place 'Total' in first column and numeric sums in respective columns
    $totalsRow = array_fill(0, count($headers), '');
    $totalsRow[0] = 'Total';
    foreach ($columnSums as $index => $sum) {
        if ($index === 0) continue; // skip first column label
        if ($sum != 0) {
            $totalsRow[$index] = number_format($sum, 2);
        }
    }

    fputcsv($csvContent, $totalsRow);

    rewind($csvContent);
    $csvData = stream_get_contents($csvContent);
    fclose($csvContent);

    $filename = 'coordinator-report-list-' . now()->format('Y-m-d') . '.csv';
    
    return response()->streamDownload(
        fn () => print($csvData),
        $filename,
        ['Content-Type' => 'text/csv']
    );
}

public function exportPdf()
{
    $filters = [
        'driver_id'   => $this->driver_id,
        'date_range'  => $this->date_range,
        'business_id' => $this->business_id,
        'branch_id'   => $this->branch_id,
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


public function import()
    {
        $this->validate([
            'csv_file' => 'required|file|mimes:csv',
        ]);

        $path = $this->csv_file->getRealPath();
        $rows = array_map('str_getcsv', file($path));
        $header = array_map('strtolower', array_map('trim', $rows[0]));
        unset($rows[0]); // remove header

        foreach ($rows as $row) {
            $rowData = array_combine($header, $row);

            // Get driver by iqaama_number
            $driver = Driver::where('iqaama_number', $rowData['iqaama_number'])->first();
            if (!$driver) continue;

            // Get business by name
            $business = Business::where('name', $rowData['business_name'])->first();
            if (!$business) continue;

            // Create/find CoordinatorReport
            $report = CoordinatorReport::firstOrCreate(
                [
                    'driver_id'   => $driver->id,
                    'report_date' => $rowData['report_date'],
                ],
                [
                    'status' => $rowData['status'] ?? 'pending',
                    'wallet' => null
                ]
            );

            // Link business
            BusinessCoordinatorReport::firstOrCreate([
                'business_id' => $business->id,
                'coordinator_report_id' => $report->id,
            ]);

            // Get dynamic field IDs
            $fields = [
                'total_orders' => 1,
                'bonus'        => 2,
                'tip'          => 3,
                'other_tip'    => 4,
            ];

            foreach ($fields as $fieldKey => $fieldId) {
                CoordinatorReportFieldValue::updateOrCreate(
                    [
                        'coordinator_report_id' => $report->id,
                        'business_id'           => $business->id,
                        'field_id'              => $fieldId,
                    ],
                    [
                        'value' => $rowData[$fieldKey] ?? 0
                    ]
                );
            }
        }

        $this->reset('csv_file');
        $this->dispatch('import-close-modal');
        session()->flash('success', 'CSV Imported Successfully.');
    }

    
    
    public function refreshReportList()
    {
        session()->flash('success', 'Status updated successfully!');
    }

}
