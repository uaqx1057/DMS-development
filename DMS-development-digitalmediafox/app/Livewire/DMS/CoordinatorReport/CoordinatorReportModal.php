<?php

namespace App\Livewire\DMS\CoordinatorReport;

use App\Models\CoordinatorReport;
use Livewire\Component;

class CoordinatorReportModal extends Component
{

    public $showModal = false;
    public $reportData;
    public $status;

    protected $listeners = ['openModal' => 'show'];

    public function show($reportData)
    {
        $this->reportData = $reportData;
        // dd($reportData);
        $this->status = $reportData['status'] ?? 'Pending';

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset('reportData'); // Reset data when closing the modal
    }

    // public function updateStatus()
    // {
    //     // Update the status in the database
    //     CoordinatorReport::where('id', $this->reportData['id'])->update([
    //         'status' => $this->status,
    //     ]);

    //     // Send success feedback and close modal
    //     session()->flash('success', 'Status updated successfully1111');
    //     return $this->redirectRoute('coordinator-report.index', navigate:true);
    // }

    public function render()
    {
        return view('livewire.dms.coordinator-report.coordinator-report-modal');
    }
}
