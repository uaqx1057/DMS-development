<?php

namespace App\Livewire\DMS\Drivers;

use App\Models\Driver;
use Livewire\Component;
use App\Services\BranchService;
use App\Services\BusinessService;
use App\Services\CoordinatorReportService;
use App\Services\DriverService;
use App\Services\FieldService;
use App\Traits\DataTableTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\WithFileUploads;
use App\Models\Business;
use App\Models\Branch;
use App\Models\CoordinatorReport;
use App\Models\BusinessCoordinatorReport;
use App\Models\CoordinatorReportFieldValue;
use Livewire\WithPagination;
use App\Models\ImportLog;
use Illuminate\Support\Facades\Auth;

use App\Models\Field; // if you have dynamic field IDs

class DriverCoordinateReport extends Component
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
    public $report_date;

    public $report_platform_name;


    public $driver_id, $business_id, $branch_id, $date_range;
    public $business_id_value;
    protected $queryString = ['driver_id', 'business_id','business_id_value', 'branch_id', 'date_range', 'page'];

    public function mount($id,CoordinatorReportService $coordinatorReportService, BusinessService $businessService, FieldService $fieldService, DriverService $driverService, BranchService $branchService)
    {
        $this->driver_id = $id;
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
        if (in_array($propertyName, ['driver_id', 'business_id', 'branch_id', 'date_range','business_id_value'])) {
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
        $driver = Driver::with(['driver_type', 'branch', 'businesses'])->findOrFail($this->driver_id);

        // Build platform info
        $platforms = [];
        $assignedIdsByBusiness = [];

        foreach ($driver->businesses as $business) {
            $assignedIds = $driver->businessIds()
                ->where('business_id', $business->id)
                ->wherePivot('transferred_at', null)
                ->pluck('value')
                ->toArray();

            if ($assignedIds) {
                $platforms[] = $business->name . ' (' . implode(', ', $assignedIds) . ')';
                $assignedIdsByBusiness[$business->name] = $assignedIds;
            }
        }

        // Format driver data (full details)
        $driverData = [
            'id' => $driver->id ?? null,
            'image' => $driver->image ?? null,
            'driver_id' => $driver->driver_id ?? '-',
            'name' => $driver->name ?? '-',
            'nationality' => $driver->nationality ?? '-',
            'language' => $driver->language ?? '-',
            'branch' => $driver->branch?->name ?? '-',
            'driver_type' => $driver->driver_type?->name ?? '-',
            'dob' => $driver->dob ? date('d-m-Y', strtotime($driver->dob)) : '-',
            'iqaama_number' => $driver->iqaama_number ?? '-',
            'iqaama_expiry' => $driver->iqaama_expiry ? date('d-m-Y', strtotime($driver->iqaama_expiry)) : '-',
            'absher_number' => $driver->absher_number ?? '-',
            'sponsorship' => $driver->sponsorship ?? '-',
            'sponsorship_id' => $driver->sponsorship_id ?? '-',
            'license_expiry' => $driver->license_expiry ? date('d-m-Y', strtotime($driver->license_expiry)) : '-',
            'insurance_policy_number' => $driver->insurance_policy_number ?? '-',
            'insurance_expiry' => $driver->insurance_expiry ? date('d-m-Y', strtotime($driver->insurance_expiry)) : '-',
            'vehicle_monthly_cost' => $driver->vehicle_monthly_cost ?? '-',
            'mobile_data' => $driver->mobile_data ?? '-',
            'fuel' => $driver->fuel ?? '-',
            'gprs' => $driver->gprs ?? '-',
            'government_levy_fee' => $driver->government_levy_fee ?? '-',
            'accommodation' => $driver->accommodation ?? '-',
            'email' => $driver->email ?? '-',
            'mobile' => $driver->mobile ?? '-',
            'remarks' => $driver->remarks ?? '-',
            'created_at' => $driver->created_at ? $driver->created_at->format('d-m-Y H:i') : '-',
            'platform_ids' => $platforms ? implode(' | ', $platforms) : '-',
            'assigned_ids_by_business' => $assignedIdsByBusiness,
        ];

        // Get Driver Coordinate Reports 
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
            'business_id_value' => $this->business_id_value,
            'branch_id' => $this->branch_id,
        ];
        
        $coordinatorReports = $this->coordinatorReportService->all($this->perPage,  $this->page, $filters);
        $branchIds = CoordinatorReport::where('driver_id', $this->driver_id)->pluck('branch_id')->unique()->values()->toArray();
        $totalBranches = count($branchIds);

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
        }else {
            $drivers = Driver::all();
        }

        $branches = Branch::whereIn('id',$branchIds)->get();
        
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
        // $businesses = $coordinatorReports->pluck('business_name')->unique()->values()->toArray();
        // logger($businesses);
        return view('livewire.dms.drivers.driver-coordinate-report', compact('driverData','main_menu', 'menu', 'coordinatorReports', 'fields', 'add_permission', 'edit_permission', 'columns', 'drivers', 'pendingReportsCount', 'completedReportsCount', 'totalOrdersCount', 'businesses', 'branches','totalBranches'));
    }

    public function exportCsv()
    {
        $filters = [
            'driver_id'   => $this->driver_id,
            'date_range'  => $this->date_range,
            'business_id' => $this->business_id,
            'branch_id'   => $this->branch_id,
            'business_id_value' => $this->business_id_value,
        ];

        // Get reports with a large page size to effectively get all records
        $coordinatorReports = $this->coordinatorReportService->all(null, null, $filters);

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
            'business_id_value' => $this->business_id_value,
            'branch_id'   => $this->branch_id,
        ];

        // Get all filtered reports without pagination (handle perPage = 0 safely)
        $coordinatorReports = $this->coordinatorReportService->all(null, null, $filters);

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
            'report_date' => 'required|date',
            'report_platform_name' => 'required',
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $user = Auth::user();
        $file = $this->csv_file;
        $path = $file->getRealPath();
        $originalName = $file->getClientOriginalName();
        $storedName = time() . '_' . $originalName;

        $rows = array_map('str_getcsv', file($path));
        $header = array_map(fn($h) => strtolower(trim($h)), $rows[0]);
        unset($rows[0]);

        $reportDate = \Carbon\Carbon::parse($this->report_date)->format('Y-m-d');

        // Track imported rows count
        $importedCount = 0;

        $allFields = Field::pluck('id', 'short_name')->toArray();
        $allFieldsByName = Field::pluck('id', 'name')->toArray();

        foreach ($rows as $row) {
            $rowData = array_combine($header, $row);
            $iqamaNumber  = trim($rowData['iqama'] ?? '');
            $businessName = trim(string: $this->report_platform_name ?? '');
            $businessValue = trim($rowData['platform id'] ?? '');

            if (!$iqamaNumber || !$businessName || !$businessValue) continue;

            $driver = Driver::where('iqaama_number', $iqamaNumber)->first();
            $business = Business::where('name', $businessName)->first();
            if (!$business || !$driver) continue;

            $businessIdRecord = \App\Models\BusinessId::where([
                ['value', $businessValue],
                ['business_id', $business->id]
            ])->first();
            if (!$businessIdRecord) continue;

            $branchId = $driver->branch_id;
            $status = trim($rowData['status'] ?? 'Pending');

            $existingReport = CoordinatorReport::where('driver_id', $driver->id)
                ->whereDate('report_date', $reportDate)
                ->whereHas('businesses', fn($q) => $q->where('business_id', $business->id))
                ->first();

            if (!$existingReport) {
                $report = CoordinatorReport::create([
                    'driver_id' => $driver->id,
                    'branch_id' => $branchId,
                    'report_date' => $reportDate,
                    'status' => $status,
                ]);

                BusinessCoordinatorReport::firstOrCreate([
                    'business_id' => $business->id,
                    'coordinator_report_id' => $report->id,
                ]);
            } else {
                $report = $existingReport;
            }

            $skip = ['driver_name', 'iqama', 'platform_name', 'platform_id', 'branch', 'status', 'date'];
            foreach ($rowData as $key => $value) {
                if (in_array($key, $skip)) continue;

                $value = trim($value);
                if ($value === '') continue;

                $normalizedKey = strtolower(str_replace([' ', '-', '/'], '_', trim($key)));
                $fieldId = $allFields[$normalizedKey] ?? $allFieldsByName[$normalizedKey] ?? null;
                if (!$fieldId) continue;

                CoordinatorReportFieldValue::updateOrCreate(
                    [
                        'coordinator_report_id' => $report->id,
                        'business_id'           => $business->id,
                        'business_id_value'     => $businessIdRecord->id,
                        'field_id'              => $fieldId,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            }

            $importedCount++;
        }

        // Create Import Log
        ImportLog::create([
            'user_id' => $user?->id,
            'file_name' => $storedName,
            'original_name' => $originalName,
            'report_date' => $reportDate,
            'model' => CoordinatorReport::class,
            'rows_imported' => $importedCount,
            'meta' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);

        $this->reset('csv_file', 'report_date');
        $this->dispatch('import-close-modal');
        session()->flash('success', 'CSV Imported Successfully. ' . $importedCount . ' rows imported.');
    }

    public function refreshReportList()
    {
        session()->flash('success', 'Status updated successfully!');
    }
}
