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

    public function render()
    {
        return view('livewire.dms.platform-ids-report.platform-ids-report-modal');
    }
}