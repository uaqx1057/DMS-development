<?php

namespace App\Repositories;

use App\Interfaces\CoordinatorReportInterface;
use App\Models\CoordinatorReport;
use App\Models\Field;
use App\Models\BusinessId;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CoordinatorReportRepository implements CoordinatorReportInterface
{
    public function all($perPage = 10, $currentPage = null, $filters = [])
    {
        $query = CoordinatorReport::with([
            'report_fields.field',
            'driver.branch',
            'businesses',
        ]);

        // Filter by driver_id
        if (!empty($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        // Filter by date_range
        if (!empty($filters['date_range'])) {
            $dates = explode(' to ', $filters['date_range']);
            $query->whereBetween('report_date', [
                $dates[0],
                $dates[1] ?? $dates[0], // Handle single date or range
            ]);
        } else {
            $query->whereDate('report_date', now()->format('Y-m-d'));
        }

        // Filter by business_id via report_fields
        if (!empty($filters['business_id'])) {
            $query->whereHas('report_fields', function ($q) use ($filters) {
                $q->where('business_id', $filters['business_id']);
            });
        }

        // Filter by branch_id via driver relation
        if (!empty($filters['branch_id'])) {
            $query->whereHas('driver.branch', function ($q) use ($filters) {
                $q->where('id', $filters['branch_id']);
            });
        }

        $coordinatorReports = $query->get();

        $fields = $coordinatorReports
            ->pluck('report_fields.*.field.name')
            ->flatten()
            ->unique()
            ->toArray();

        // 🆕 CREATE SEPARATE RECORDS FOR EACH BUSINESS VALUE
        $expandedResults = new Collection();
        
        foreach ($coordinatorReports as $report) {
            // Group report fields by business_id_value
            $groupedFields = $report->report_fields->groupBy('business_id_value');
            
            foreach ($groupedFields as $businessIdValue => $fieldsGroup) {
                $businessId = \App\Models\BusinessId::find($businessIdValue);
                
                if ($businessId) {
                    $reportModel = (new CoordinatorReport())->fill($report->toArray());
                    
                    $reportModel->setAttribute('id', $report->id);
                    $reportModel->setAttribute('driver_iqama', optional($report->driver)->iqaama_number ?? 'N/A');
                    $reportModel->setAttribute('driver_name', optional($report->driver)->name ?? 'N/A');
                    $reportModel->setAttribute('branch_name', optional($report->driver->branch)->name ?? 'N/A');
                    $reportModel->setAttribute('report_status', $report->status ?? 'Unknown');
                    
                    // 🆕 SET BUSINESS NAME WITH BOTH BUSINESS NAME AND VALUE
                    $businessTypeName = $businessId->business->name ?? 'Unknown Business';
                    $reportModel->setAttribute('business_name', "{$businessTypeName} ({$businessId->value})");
                    
                    // 🆕 CALCULATE FIELD VALUES ONLY FOR THIS BUSINESS VALUE
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
            
            // 🆕 IF NO BUSINESS FIELDS, STILL ADD THE REPORT WITH DEFAULT VALUES
            if ($report->report_fields->isEmpty()) {
                $reportModel = (new CoordinatorReport())->fill($report->toArray());
                
                $reportModel->setAttribute('id', $report->id);
                $reportModel->setAttribute('driver_iqama', optional($report->driver)->iqaama_number ?? 'N/A');
                $reportModel->setAttribute('driver_name', optional($report->driver)->name ?? 'N/A');
                $reportModel->setAttribute('branch_name', optional($report->driver->branch)->name ?? 'N/A');
                $reportModel->setAttribute('report_status', $report->status ?? 'Unknown');
                $reportModel->setAttribute('business_name', 'N/A');
                
                foreach ($fields as $field) {
                    $reportModel->setAttribute($field, 0);
                }
                
                $expandedResults->push($reportModel);
            }
        }

        $currentPage = $currentPage ?: LengthAwarePaginator::resolveCurrentPage();
        $paginatedItems = $this->paginateCollection($expandedResults, $perPage, $currentPage);

        return $paginatedItems;
    }



private function paginateCollection(Collection $items, $perPage, $currentPage)
{
    $total = $items->count();
    $offset = ($currentPage - 1) * $perPage;
    $currentItems = $items->slice($offset, $perPage);

    return new LengthAwarePaginator(
        $currentItems->values(),
        $total,
        $perPage,
        $currentPage,
        ['path' => LengthAwarePaginator::resolveCurrentPath()]
    );
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
