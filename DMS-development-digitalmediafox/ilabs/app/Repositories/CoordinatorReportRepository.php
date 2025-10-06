<?php

namespace App\Repositories;

use App\Interfaces\CoordinatorReportInterface;
use App\Models\CoordinatorReport;
use App\Models\Field;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
class CoordinatorReportRepository implements CoordinatorReportInterface
{
    public function all($perPage = 10, $currentPage = null, $filters = [])
{
    $query = CoordinatorReport::with('report_fields.field', 'driver.branch');

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
        // Default to current day if no date is provided
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

    $result = $coordinatorReports->map(function ($report) use ($fields) {
        $reportModel = (new CoordinatorReport())->fill($report->toArray());

        $reportModel->setAttribute('id', $report->id);
        $reportModel->setAttribute('driver_name', optional($report->driver)->name ?? 'N/A');
        $reportModel->setAttribute('branch_name', optional($report->driver->branch)->name ?? 'N/A');
        $reportModel->setAttribute('report_status', $report->status ?? 'Unknown');

        foreach ($fields as $field) {
            $totalFieldValue = $report->report_fields
                ->where('field.name', $field)
                ->pluck('value')
                ->map(fn($value) => is_numeric($value) ? (float) $value : 0)
                ->sum();

            $reportModel->setAttribute($field, $totalFieldValue);
        }

        return $reportModel;
    });

    $currentPage = $currentPage ?: LengthAwarePaginator::resolveCurrentPage();
    $paginatedItems = $this->paginateCollection($result->filter(), $perPage, $currentPage);

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
        // Storing Businesses
        $report->businesses()->attach($data['businesses']);
        // Create Field Values
        foreach ($data['formData'] as $businessId => $fields) {
            foreach ($fields as $fieldName => $value) {
                $field = Field::where('short_name', $fieldName)->first();

                if ($field->type != 'DOCUMENT') {
                    // For non-document fields, store the value directly
                    $report->report_fields()->create([
                        'business_id' => $businessId,
                        'field_id' => $field->id,
                        'value' => $value
                    ]);
                } else {
                    // Handle document field (multi-file upload)
                    if (isset($data['files'][$businessId][$fieldName]) && is_array($data['files'][$businessId][$fieldName])) {
                        // Initialize an array to hold file paths
                        $uploadedFilePaths = [];

                        // Iterate over each uploaded file
                        foreach ($data['files'][$businessId][$fieldName] as $file) {
                            // Upload the file and store its path
                            $filePath = $file->store('uploads/documents', 'public');  // Adjust the path and disk as needed
                            $uploadedFilePaths[] = $filePath;
                        }

                        // Save the JSON-encoded file paths
                        $report->report_fields()->create([
                            'business_id' => $businessId,
                            'field_id' => $field->id,
                            'value' => json_encode($uploadedFilePaths)
                        ]);
                    }
                }
            }
        }

    }

    public function update($data)
    {
        // Find the report
        $report = CoordinatorReport::find($data['id']);

        // Sync businesses
        $report->businesses()->sync($data['businesses']);

        // Iterate over formData and handle field updates or inserts
        foreach ($data['formData'] as $businessId => $fields) {
            foreach ($fields as $fieldName => $value) {
                $field = Field::where('short_name', $fieldName)->first();

                // Check if the field value already exists for this report
                $existingFieldValue = $report->report_fields()
                    ->where('business_id', $businessId)
                    ->where('field_id', $field->id)
                    ->first();

                if ($field->type != 'DOCUMENT') {
                    // For non-document fields
                    if ($existingFieldValue) {
                        // Update the existing value
                        $existingFieldValue->update(['value' => $value]);
                    } else {
                        // Create a new field value if it doesn't exist
                        $report->report_fields()->create([
                            'business_id' => $businessId,
                            'field_id' => $field->id,
                            'value' => $value
                        ]);
                    }
                } else {
                    // Handle document field (multi-file upload)
                    if (isset($data['files'][$businessId][$fieldName]) && is_array($data['files'][$businessId][$fieldName])) {
                        // If it's a document and the field already exists, update the document
                        if ($existingFieldValue) {
                            // Initialize an array to hold file paths
                            $uploadedFilePaths = json_decode($existingFieldValue->value, true) ?? [];

                            // Iterate over each uploaded file
                            foreach ($data['files'][$businessId][$fieldName] as $file) {
                                // Upload the file and store its path
                                $filePath = $file->store('uploads/documents', 'public');  // Adjust the path and disk as needed
                                $uploadedFilePaths[] = $filePath;
                            }

                            // Update the field with the new document paths
                            $existingFieldValue->update([
                                'value' => json_encode($uploadedFilePaths)
                            ]);
                        } else {
                            // Create a new document field entry if no existing field value is found
                            $uploadedFilePaths = [];

                            foreach ($data['files'][$businessId][$fieldName] as $file) {
                                // Upload the file and store its path
                                $filePath = $file->store('uploads/documents', 'public');  // Adjust the path and disk as needed
                                $uploadedFilePaths[] = $filePath;
                            }

                            // Save the JSON-encoded file paths
                            $report->report_fields()->create([
                                'business_id' => $businessId,
                                'field_id' => $field->id,
                                'value' => json_encode($uploadedFilePaths)
                            ]);
                        }
                    }
                    // If no new document was uploaded and the field exists, do not change the value
                }
            }
        }

        // Update report data
        $report->update([
            'driver_id' => $data['driver_id'],
            'report_date' => $data['report_date'],
            'status' => $data['status']
        ]);

        // Handle status changes (e.g., Approved status)
        // if ($data['status'] === 'Approved') {
            // Generating Revenue logic
        // }
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
