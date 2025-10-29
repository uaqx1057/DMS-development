<?php

namespace App\Livewire\DMS\PlatformIdsReport;

use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class PlatformIdsReportModal extends Component
{
    public $showModal = false;
    public $businessIdValue;
    public $reports = [];
    public $businessName;
    public $platformId;

    protected $listeners = ['openPlatformIdModal' => 'show'];

    public function show($data)
    {
        $this->businessIdValue = $data['business_id_value'];
        $this->reports = $data['reports'];
        
        // Get business name and platform ID from first report
        if (!empty($this->reports)) {
            $this->businessName = $this->reports[0]['business_name'] ?? 'Unknown';
            $this->platformId = $this->reports[0]['platform_id'] ?? 'N/A';
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['reports', 'businessIdValue', 'businessName', 'platformId']);
    }

    /**
     * Export individual report as PDF
     */
    public function exportReportPdf($reportId)
    {
        // Find the specific report
        $report = collect($this->reports)->firstWhere('id', $reportId);
        
        if (!$report) {
            session()->flash('error', 'Report not found');
            return;
        }

        // Generate PDF
        $pdf = Pdf::loadView('exports.single-platform-report', [
            'report' => $report,
            'businessName' => $this->businessName,
            'platformId' => $this->platformId,
        ]);

        $filename = 'platform-report-' . $report['driver']['name'] . '-' . $report['report_date'] . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }

    /**
     * Export individual report as CSV
     */
    public function exportReportCsv($reportId)
    {
        // Find the specific report
        $report = collect($this->reports)->firstWhere('id', $reportId);
        
        if (!$report) {
            session()->flash('error', 'Report not found');
            return;
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=platform-report-' . $report['driver']['name'] . '-' . $report['report_date'] . '.csv',
        ];

        $callback = function() use ($report) {
            $file = fopen('php://output', 'w');

            // Prepare all headers and values
            $allHeaders = [
                'Platform Name',
                'Platform ID', 
                'Report Date',
                'Driver Name',
                'Driver Iqaama',
                'Branch'
            ];
            
            $allValues = [
                $this->businessName,
                $this->platformId,
                \Carbon\Carbon::parse($report['report_date'])->format('d-m-Y'),
                $report['driver']['name'],
                $report['driver']['iqaama_number'],
                $report['branch']['name']
            ];

            // Add regular field values
            foreach ($report['field_values'] as $fieldName => $fieldData) {
                if ($fieldData['type'] !== 'DOCUMENT') {
                    $allHeaders[] = $fieldName;
                    $allValues[] = $fieldData['value'];
                }
            }

            // Add document fields
            $documentFields = array_filter($report['field_values'], fn($field) => $field['type'] === 'DOCUMENT');
            foreach ($documentFields as $fieldName => $fieldData) {
                $allHeaders[] = $fieldName . ' (Document)';
                $files = json_decode($fieldData['value'], true) ?? [];
                $allValues[] = !empty($files) ? 
                    implode('; ', array_map(fn($file) => asset('storage/' . $file), $files)) : 
                    'No files';
            }

            // Write headers and values
            fputcsv($file, $allHeaders);
            fputcsv($file, $allValues);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.dms.platform-ids-report.platform-ids-report-modal');
    }
}