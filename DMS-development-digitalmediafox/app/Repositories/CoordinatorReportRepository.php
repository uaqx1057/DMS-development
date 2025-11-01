<?php

namespace App\Repositories;

use App\Interfaces\CoordinatorReportInterface;
use App\Models\CoordinatorReport;
use App\Models\Field;
use App\Models\BusinessId;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class CoordinatorReportRepository implements CoordinatorReportInterface
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
        } else {
            $query->whereDate('report_date', now()->format('Y-m-d'));
        }

        // 🔹 Filter by branch_id directly from coordinator_reports table
        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        // ✅ Run query first to get base reports
        $coordinatorReports = $query->get();

        // Collect all field names for dynamic columns
        // Get Field ID for Total Orders
        $totalOrdersField = Field::where('name', 'Total Orders')->first();
        
        $fields = $coordinatorReports
            ->pluck('report_fields.*.field.name')
            ->flatten()
            ->unique()
            ->toArray();

        // 🧩 Build expanded records per business_id_value
        $expandedResults = new Collection();

        foreach ($coordinatorReports as $report) {
            $groupedFields = $report->report_fields->groupBy('business_id_value');

            foreach ($groupedFields as $businessIdValue => $fieldsGroup) {
                // 🔹 CRITICAL FIX: Apply business_id filter at expansion level
                if (!empty($filters['business_id'])) {
                    $firstField = $fieldsGroup->first();
                    if (!$firstField || $firstField->business_id != $filters['business_id']) {
                        continue;
                    }
                }

                // 🔹 CRITICAL FIX: Apply business_id_value filter at expansion level
                if (!empty($filters['business_id_value']) && $businessIdValue != $filters['business_id_value']) {
                    continue;
                }

                $businessId = BusinessId::find($businessIdValue);

                if ($businessId) {
                    $reportModel = (new CoordinatorReport())->fill($report->toArray());
                    $reportModel->setAttribute('id', $report->id);
                    $reportModel->setAttribute('driver_iqama', optional($report->driver)->iqaama_number ?? 'N/A');
                    $reportModel->setAttribute('driver_name', optional($report->driver)->name ?? 'N/A');
                    $reportModel->setAttribute('branch_name', optional($report->branch)->name ?? 'N/A');
                    $reportModel->setAttribute('report_status', $report->status ?? 'Unknown');

                    $businessTypeName = $businessId->business->name ?? 'Unknown Business';
                    $reportModel->setAttribute('business_name', "{$businessTypeName} ({$businessId->value})");

                    foreach ($fields as $field) {
                        $totalFieldValue = $fieldsGroup
                            ->where('field.name', $field)
                            ->pluck('value')
                            ->map(fn($value) => is_numeric($value) ? (float) $value : 0)
                            ->sum();
                        $reportModel->setAttribute($field, $totalFieldValue);
                    }

                    $expandedResults->push($reportModel);
                }
            }

            // Add default record if no fields found AND no business filters are applied
            if ($report->report_fields->isEmpty() && empty($filters['business_id']) && empty($filters['business_id_value'])) {
                $reportModel = (new CoordinatorReport())->fill($report->toArray());
                $reportModel->setAttribute('id', $report->id);
                $reportModel->setAttribute('driver_iqama', optional($report->driver)->iqaama_number ?? 'N/A');
                $reportModel->setAttribute('driver_name', optional($report->driver)->name ?? 'N/A');
                $reportModel->setAttribute('branch_name', optional($report->branch)->name ?? 'N/A');
                $reportModel->setAttribute('report_status', $report->status ?? 'Unknown');
                $reportModel->setAttribute('business_name', 'N/A');
                foreach ($fields as $field) {
                    $reportModel->setAttribute($field, 0);
                }
                $expandedResults->push($reportModel);
            }
        }
        if (is_null($perPage)) {
            return $expandedResults;
        }
        $currentPage = $currentPage ?: LengthAwarePaginator::resolveCurrentPage();
        return $this->paginateCollection($expandedResults, $perPage, $currentPage, $filters);
    }



    private function paginateCollection(Collection $items, $perPage, $currentPage, $filters = [], $path = null)
    {
        $path = $path ?? url('/dms/coordinator-report');

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
        if (!isset($data['branch_id']) && isset($data['driver_id'])) {
            $driver = \App\Models\Driver::find($data['driver_id']);
            if ($driver && $driver->branch_id) {
                $data['branch_id'] = $driver->branch_id;
            }
        }
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
                                // For non-document fields, store the value directly with business_id_value
                                $report->report_fields()->create([
                                    'business_id' => $businessId->business_id, // Business type ID
                                    'business_id_value' => $businessIdValue, // Specific business ID (business_ids.id)
                                    'field_id' => $field->id,
                                    'value' => $value
                                ]);
                                
                                \Log::info("Stored field for business_id_value: {$businessIdValue}", [
                                    'business_type_id' => $businessId->business_id,
                                    'business_id_value' => $businessIdValue,
                                    'field_id' => $field->id,
                                    'field_name' => $fieldName,
                                    'value' => $value
                                ]);
                            } else {
                                // Handle document field (multi-file upload)
                                if (isset($data['files'][$businessIdValue][$fieldName]) && is_array($data['files'][$businessIdValue][$fieldName])) {
                                    $uploadedFilePaths = [];

                                    foreach ($data['files'][$businessIdValue][$fieldName] as $file) {
                                        $filePath = $file->store('uploads/documents', 'public');
                                        $uploadedFilePaths[] = $filePath;
                                    }

                                    $report->report_fields()->create([
                                        'business_id' => $businessId->business_id, // Business type ID
                                        'business_id_value' => $businessIdValue, // Specific business ID (business_ids.id)
                                        'field_id' => $field->id,
                                        'value' => json_encode($uploadedFilePaths)
                                    ]);
                                    
                                    \Log::info("Stored document field for business_id_value: {$businessIdValue}", [
                                        'business_type_id' => $businessId->business_id,
                                        'business_id_value' => $businessIdValue,
                                        'field_id' => $field->id,
                                        'field_name' => $fieldName,
                                        'files_count' => count($uploadedFilePaths)
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // Log final counts
        $fieldCount = $report->report_fields()->count();
        \Log::info("Coordinator Report created successfully", [
            'report_id' => $report->id,
            'selected_business_ids_count' => count($data['selectedBusinessIds'] ?? []),
            'total_field_values_created' => $fieldCount
        ]);
    }

    /**
     * Extract business type IDs from business IDs
     */
    private function getBusinessTypeIdsFromBusinessIds($businessIds)
    {
        return BusinessId::whereIn('id', $businessIds)
            ->pluck('business_id')
            ->unique()
            ->toArray();
    }

    /**
     * Get selected business IDs for a specific business type
     */
    private function getSelectedBusinessIdsForType($allBusinessIds, $businessTypeId)
    {
        return BusinessId::whereIn('id', $allBusinessIds)
            ->where('business_id', $businessTypeId)
            ->pluck('id')
            ->toArray();
    }


    public function update($data)
    {
        // Find the report
        $report = CoordinatorReport::find($data['id']);
        if (!isset($data['branch_id']) && isset($data['driver_id'])) {
            $driver = \App\Models\Driver::find($data['driver_id']);
            if ($driver && $driver->branch_id) {
                $data['branch_id'] = $driver->branch_id;
            }
        }
        // Store business type IDs (for the businesses relationship)
        if (!empty($data['selectedBusinessIds'])) {
            $businessTypeIds = BusinessId::whereIn('id', $data['selectedBusinessIds'])
                ->pluck('business_id')
                ->unique()
                ->toArray();
                
            $report->businesses()->sync($businessTypeIds);
        }

        // First, delete all existing field values
        $report->report_fields()->delete();

        // Create Field Values for each selected business ID
        if (!empty($data['formData'])) {
            foreach ($data['formData'] as $businessIdValue => $fields) {
                $businessId = BusinessId::find($businessIdValue);
                
                if ($businessId) {
                    foreach ($fields as $fieldName => $value) {
                        $field = Field::where('short_name', $fieldName)->first();

                        if ($field) {
                            if ($field->type != 'DOCUMENT') {
                                // For non-document fields, store the value directly with business_id_value
                                $report->report_fields()->create([
                                    'business_id' => $businessId->business_id, // Business type ID
                                    'business_id_value' => $businessIdValue, // Specific business ID (business_ids.id)
                                    'field_id' => $field->id,
                                    'value' => $value
                                ]);
                            } else {
                                // Handle document field (multi-file upload)
                                if (isset($data['files'][$businessIdValue][$fieldName]) && is_array($data['files'][$businessIdValue][$fieldName])) {
                                    $uploadedFilePaths = [];

                                    foreach ($data['files'][$businessIdValue][$fieldName] as $file) {
                                        $filePath = $file->store('uploads/documents', 'public');
                                        $uploadedFilePaths[] = $filePath;
                                    }

                                    $report->report_fields()->create([
                                        'business_id' => $businessId->business_id, // Business type ID
                                        'business_id_value' => $businessIdValue, // Specific business ID (business_ids.id)
                                        'field_id' => $field->id,
                                        'value' => json_encode($uploadedFilePaths)
                                    ]);
                                } else {
                                    // If no new files uploaded, keep the existing value
                                    $report->report_fields()->create([
                                        'business_id' => $businessId->business_id, // Business type ID
                                        'business_id_value' => $businessIdValue, // Specific business ID (business_ids.id)
                                        'field_id' => $field->id,
                                        'value' => $value // This should be the existing file paths
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }

        // Update report data
        $report->update([
            'driver_id' => $data['driver_id'],
            'branch_id' => $data['branch_id'] ?? $report->branch_id,
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

}
