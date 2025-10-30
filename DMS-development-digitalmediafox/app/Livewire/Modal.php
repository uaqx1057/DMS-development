<?php

namespace App\Livewire;

use App\Models\CoordinatorReport;
use App\Models\CoordinatorReportFieldValue;
use Livewire\Component;

class Modal extends Component
{
    public $showModal = false;
    public $reportData;
    public $status;
    public $totalOrders = 0;
    protected $listeners = ['openModal' => 'show'];

    public function show($reportId)
    {
        // Load the report with all necessary relationships
        $report = CoordinatorReport::with([
            'driver',
            'branch',
            'businesses',
            'report_fields.field', // Make sure we load the field relationship
        ])->find($reportId);

        if ($report) {
            $this->reportData = $report->toArray();
            $this->status = $report->status ?? 'Pending';
            $this->totalOrders = $report->report_fields()
                ->where('field_id', 1)
                ->sum('value');
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset('reportData'); // Reset data when closing the modal
    }

    public function updateStatus()
    {
        // Update the status in the database
        CoordinatorReport::where('id', $this->reportData['id'])->update([
            'status' => $this->status,
        ]);

        $this->dispatch('statusUpdated');
        $this->showModal = false;

    }

    public function render()
    {
        return view('livewire.modal');
    }
}
