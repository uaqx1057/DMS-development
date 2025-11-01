<?php

namespace App\Repositories;

use App\Interfaces\CoordinatorReportInterface;
use App\Models\CoordinatorReport;
use App\Models\Field;
use App\Models\BusinessId;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class PlatformIdReportRepository implements CoordinatorReportInterface
{
    public function all($perPage = 10, $currentPage = null, $filters = [])
    {
        $query = CoordinatorReport::with([
            'report_fields.field',
            'driver',
            'branch',
            'businesses',
        ]);

        // 🔹 Filter by driver_id
        if (!empty($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        // 🔹 Filter by date_range
        if (!empty($filters['date_range'])) {
            $dates = explode(' to ', $filters['date_range']);
            $query->whereBetween('report_date', [
                $dates[0],
                $dates[1] ?? $dates[0],
            ]);
        }
        // Remove the default date filter so all records show when no date is selected

        // 🔹 Filter by branch_id directly from coordinator_reports table
        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        // ✅ Run query first to get base reports
        $coordinatorReports = $query->get();

        // Collect all field names for dynamic columns
        $fields = $coordinatorReports
            ->pluck('report_fields.*.field.name')
            ->flatten()
            ->unique()
            ->toArray();

        // 🧩 Build grouped records by business_id_value
        $groupedData = [];

        foreach ($coordinatorReports as $report) {
            $groupedFields = $report->report_fields->groupBy('business_id_value');

            foreach ($groupedFields as $businessIdValue => $fieldsGroup) {
                // 🔹 Apply business_id filter
                if (!empty($filters['business_id'])) {
                    $firstField = $fieldsGroup->first();
                    if (!$firstField || $firstField->business_id != $filters['business_id']) {
                        continue;
                    }
                }

                // 🔹 Apply business_id_value filter
                if (!empty($filters['business_id_value']) && $businessIdValue != $filters['business_id_value']) {
                    continue;
                }

                $businessId = BusinessId::find($businessIdValue);

                if ($businessId) {
                    $businessKey = $businessId->business_id . '_' . $businessIdValue;
                    
                    // Initialize grouped data if not exists
                    if (!isset($groupedData[$businessKey])) {
                        $groupedData[$businessKey] = [
                            'business_id_value' => $businessIdValue,
                            'business_id' => $businessId->business_id,
                            'business_name' => $businessId->business->name ?? 'Unknown Business',
                            'platform_id' => $businessId->value,
                            'report_ids' => [],
                            'drivers' => [],
                            'branches' => [],
                            'date_range' => [],
                            'fields_total' => [],
                            'statuses' => []
                        ];
                    }

                    // Collect report IDs
                    if (!in_array($report->id, $groupedData[$businessKey]['report_ids'])) {
                        $groupedData[$businessKey]['report_ids'][] = $report->id;
                    }

                    // Collect unique drivers
                    if ($report->driver) {
                        $driverKey = $report->driver->id;
                        if (!isset($groupedData[$businessKey]['drivers'][$driverKey])) {
                            $groupedData[$businessKey]['drivers'][$driverKey] = [
                                'name' => $report->driver->name,
                                'iqama' => $report->driver->iqaama_number ?? 'N/A'
                            ];
                        }
                    }

                    // Collect unique branches
                    if ($report->branch) {
                        $branchKey = $report->branch->id;
                        if (!isset($groupedData[$businessKey]['branches'][$branchKey])) {
                            $groupedData[$businessKey]['branches'][$branchKey] = $report->branch->name;
                        }
                    }

                    // Track date range
                    $reportDate = $report->report_date;
                    if (!in_array($reportDate, $groupedData[$businessKey]['date_range'])) {
                        $groupedData[$businessKey]['date_range'][] = $reportDate;
                    }

                    // Sum up field values
                    foreach ($fields as $field) {
                        if (!isset($groupedData[$businessKey]['fields_total'][$field])) {
                            $groupedData[$businessKey]['fields_total'][$field] = 0;
                        }
                        
                        $fieldValue = $fieldsGroup
                            ->where('field.name', $field)
                            ->pluck('value')
                            ->map(fn($value) => is_numeric($value) ? (float) $value : 0)
                            ->sum();
                        
                        $groupedData[$businessKey]['fields_total'][$field] += $fieldValue;
                    }

                    // Collect statuses
                    if ($report->status && !in_array($report->status, $groupedData[$businessKey]['statuses'])) {
                        $groupedData[$businessKey]['statuses'][] = $report->status;
                    }
                }
            }
        }

        // 🔹 Convert grouped data to Collection of model-like objects
        $expandedResults = new Collection();
        $rowNumber = 1;

        foreach ($groupedData as $key => $group) {
            $reportModel = new CoordinatorReport();
            
            // Set attributes
            $reportModel->setAttribute('row_number', $rowNumber++);
            $reportModel->setAttribute('report_ids', implode(',', $group['report_ids']));
            $reportModel->setAttribute('business_id_value', $group['business_id_value']);
            
            // Date range
            sort($group['date_range']);
            $dateRangeStr = count($group['date_range']) > 1 
                ? date('d-m-Y', strtotime($group['date_range'][0])) . ' to ' . date('d-m-Y', strtotime(end($group['date_range'])))
                : date('d-m-Y', strtotime($group['date_range'][0]));
            $reportModel->setAttribute('date_range', $dateRangeStr);
            
            // Business name with platform ID
            $reportModel->setAttribute('business_name', "{$group['business_name']} ({$group['platform_id']})");
            
            // Assigned drivers (name + iqama)
            $driversStr = collect($group['drivers'])
                ->map(fn($driver) => "{$driver['name']} ({$driver['iqama']})")
                ->implode('<br> ');
            $reportModel->setAttribute('assigned_drivers', $driversStr ?: 'N/A');
            
            // Branches
            $branchesStr = implode('<br> ', $group['branches']) ?: 'N/A';
            $reportModel->setAttribute('branches', $branchesStr);
            
            // Field totals
            foreach ($fields as $field) {
                $reportModel->setAttribute($field, $group['fields_total'][$field] ?? 0);
            }
            
            // Status (show all unique statuses or most common)
            $statusStr = implode('<br> ', array_unique($group['statuses'])) ?: 'Unknown';
            $reportModel->setAttribute('report_status', $statusStr);
            
            $expandedResults->push($reportModel);
        }
        if (is_null($perPage)) {
            return $expandedResults;
        }

        $currentPage = $currentPage ?: LengthAwarePaginator::resolveCurrentPage();
        return $this->paginateCollection($expandedResults, $perPage, $currentPage,$filters);
    }

    private function paginateCollection(Collection $items, $perPage, $currentPage, $filters = [], $path = null)
    {
        $path = $path ?? url('/dms/platform-ids-report');

        $paginator = new LengthAwarePaginator(
            $items->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $items->count(),
            $perPage,
            $currentPage,
            [
                'path' => $path,
                'pageName' => 'page',
            ]
        );

        // ✅ Append Livewire filters manually
        if (!empty($filters)) {
            $paginator->appends($filters);
        }

        return $paginator;
    }

    public function create($data){
        // Create Coordinator Report
        $report = CoordinatorReport::create($data);
        
        // Store business type IDs (for the businesses relationship)
        if (!empty($data['selectedBusinessIds'])) {
            $businessTypeIds = BusinessId::whereIn('id', $data['selectedBusinessIds'])
                ->pluck('business_id')
                ->unique()
                ->toArray();
                
            $report->businesses()->attach($businessTypeIds);
        }

        // Create Field Values for each selected business ID
        if (!empty($data['formData'])) {
            foreach ($data['formData'] as $businessIdValue => $fields) {
                $businessId = BusinessId::find($businessIdValue);
                
                if ($businessId) {
                    foreach ($fields as $fieldName => $value) {
                        $field = Field::where('short_name', $fieldName)->first();

                        if ($field) {
                            if ($field->type != 'DOCUMENT') {
                                $report->report_fields()->create([
                                    'business_id' => $businessId->business_id,
                                    'business_id_value' => $businessIdValue,
                                    'field_id' => $field->id,
                                    'value' => $value
                                ]);
                            } else {
                                if (isset($data['files'][$businessIdValue][$fieldName]) && is_array($data['files'][$businessIdValue][$fieldName])) {
                                    $uploadedFilePaths = [];

                                    foreach ($data['files'][$businessIdValue][$fieldName] as $file) {
                                        $filePath = $file->store('uploads/documents', 'public');
                                        $uploadedFilePaths[] = $filePath;
                                    }

                                    $report->report_fields()->create([
                                        'business_id' => $businessId->business_id,
                                        'business_id_value' => $businessIdValue,
                                        'field_id' => $field->id,
                                        'value' => json_encode($uploadedFilePaths)
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function update($data)
    {
        $report = CoordinatorReport::find($data['id']);

        if (!empty($data['selectedBusinessIds'])) {
            $businessTypeIds = BusinessId::whereIn('id', $data['selectedBusinessIds'])
                ->pluck('business_id')
                ->unique()
                ->toArray();
                
            $report->businesses()->sync($businessTypeIds);
        }

        $report->report_fields()->delete();

        if (!empty($data['formData'])) {
            foreach ($data['formData'] as $businessIdValue => $fields) {
                $businessId = BusinessId::find($businessIdValue);
                
                if ($businessId) {
                    foreach ($fields as $fieldName => $value) {
                        $field = Field::where('short_name', $fieldName)->first();

                        if ($field) {
                            if ($field->type != 'DOCUMENT') {
                                $report->report_fields()->create([
                                    'business_id' => $businessId->business_id,
                                    'business_id_value' => $businessIdValue,
                                    'field_id' => $field->id,
                                    'value' => $value
                                ]);
                            } else {
                                if (isset($data['files'][$businessIdValue][$fieldName]) && is_array($data['files'][$businessIdValue][$fieldName])) {
                                    $uploadedFilePaths = [];

                                    foreach ($data['files'][$businessIdValue][$fieldName] as $file) {
                                        $filePath = $file->store('uploads/documents', 'public');
                                        $uploadedFilePaths[] = $filePath;
                                    }

                                    $report->report_fields()->create([
                                        'business_id' => $businessId->business_id,
                                        'business_id_value' => $businessIdValue,
                                        'field_id' => $field->id,
                                        'value' => json_encode($uploadedFilePaths)
                                    ]);
                                } else {
                                    $report->report_fields()->create([
                                        'business_id' => $businessId->business_id,
                                        'business_id_value' => $businessIdValue,
                                        'field_id' => $field->id,
                                        'value' => $value
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }

        $report->update([
            'driver_id' => $data['driver_id'],
            'report_date' => $data['report_date'],
            'status' => $data['status']
        ]);
    }

    public function find($id){
        return CoordinatorReport::with('businesses', 'report_fields', 'driver')->findOrFail($id);
    }

    public function checkDuplicateReport($data){
        return CoordinatorReport::where($data)->first();
    }

    public function checkDuplicateReportUpdate($data, $id){
        return CoordinatorReport::where($data)->where('id', '!=', $id)->first();
    }
    public function getReportsByBusinessIdValue($businessIdValue)
    {
        // Get the business ID record
        $businessId = BusinessId::find($businessIdValue);
        
        if (!$businessId) {
            return collect([]);
        }

        // Get all coordinator reports that have fields with this business_id_value
        $reports = CoordinatorReport::with([
            'report_fields.field',
            'driver',
            'branch',
            'businesses',
        ])
        ->whereHas('report_fields', function ($query) use ($businessIdValue) {
            $query->where('business_id_value', $businessIdValue);
        })
        ->get();

        // Transform the reports to include only fields for this business_id_value
        $transformedReports = $reports->map(function ($report) use ($businessIdValue, $businessId) {
            // Filter report fields for this business_id_value only
            $filteredFields = $report->report_fields->filter(function ($field) use ($businessIdValue) {
                return $field->business_id_value == $businessIdValue;
            });

            // Create field values array
            $fieldValues = [];
            foreach ($filteredFields as $field) {
                $fieldValues[$field->field->name] = [
                    'value' => $field->value,
                    'type' => $field->field->type,
                    'field_id' => $field->field_id
                ];
            }

            return [
                'id' => $report->id,
                'report_date' => $report->report_date,
                'status' => $report->status,
                'driver' => [
                    'id' => $report->driver->id,
                    'name' => $report->driver->name,
                    'iqaama_number' => $report->driver->iqaama_number ?? 'N/A',
                ],
                'branch' => [
                    'id' => $report->branch->id ?? null,
                    'name' => $report->branch->name ?? 'N/A',
                ],
                'business_id_value' => $businessIdValue,
                'business_name' => $businessId->business->name ?? 'Unknown',
                'platform_id' => $businessId->value,
                'field_values' => $fieldValues,
            ];
        });

        return $transformedReports;
    }

}