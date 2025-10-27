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

            // Write report header
            fputcsv($file, ['Platform Report Details']);
            fputcsv($file, []);
            
            // Basic Information
            fputcsv($file, ['Basic Information']);
            fputcsv($file, ['Platform Name', $this->businessName]);
            fputcsv($file, ['Platform ID', $this->platformId]);
            fputcsv($file, ['Report Date', \Carbon\Carbon::parse($report['report_date'])->format('d-m-Y')]);
            fputcsv($file, ['Driver Name', $report['driver']['name']]);
            fputcsv($file, ['Driver Iqaama', $report['driver']['iqaama_number']]);
            fputcsv($file, ['Branch', $report['branch']['name']]);
            fputcsv($file, ['Status', $report['status']]);
            fputcsv($file, []);

            // Field Values
            fputcsv($file, ['Report Values']);
            fputcsv($file, ['Field', 'Value']);
            
            foreach ($report['field_values'] as $fieldName => $fieldData) {
                if ($fieldData['type'] !== 'DOCUMENT') {
                    fputcsv($file, [$fieldName, $fieldData['value']]);
                }
            }

            // Document Section
            $documentFields = array_filter($report['field_values'], fn($field) => $field['type'] === 'DOCUMENT');
            if (!empty($documentFields)) {
                fputcsv($file, []);
                fputcsv($file, ['Document Attachments']);
                fputcsv($file, ['Document Type', 'File Path']);
                
                foreach ($documentFields as $fieldName => $fieldData) {
                    $files = json_decode($fieldData['value'], true) ?? [];
                    foreach ($files as $file_path) {
                        fputcsv($file, [$fieldName, asset('storage/' . $file_path)]);
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.dms.platform-ids-report.platform-ids-report-modal');
    }
}