<?php

namespace App\Livewire\DMS\CoordinatorReport;

use App\Traits\DMS\CoordinatorReportTrait;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\Attributes\Title;

#[Title('Edit Coordinator Report')]
class EditCoordinatorReport extends Component
{
    use WithFileUploads, CoordinatorReportTrait;

    public string $main_menu = 'Coordinator Report';
    public string $menu = 'Edit Coordinator Report';

    public function onDriverChange()
    {
        $this->resetBusinessData();
    }

    public function update(){
        $validated = $this->validations();
        $validated['selectedBusinessIds'] = $this->selectedBusinessIds;
        $validated['formData'] = $this->formData;
        $validated['files'] = $this->files;
        $validated['id'] = $this->coordinatorReportId;
        $validated['status'] = $this->status;
        
        $checkCoordinatorReport = $this->coordinatorReportService->checkDuplicateReportUpdate([
            'report_date' => $validated['report_date'],
            'driver_id' => $validated['driver_id'],
        ], $this->coordinatorReportId);

        if(!is_null($checkCoordinatorReport)){
            $this->addError('report_date', translate('Coordinator Report Against This Driver and Date already exists'));
            return;
        }

        $this->coordinatorReportService->update($validated);
        session()->flash('success', translate('Coordinator Report Updated Successfully!'));
        return $this->redirectRoute('coordinator-report.index', navigate:true);
    }
public function toggleBusinessId($businessIdValue)
{
    if (in_array($businessIdValue, $this->selectedBusinessIds)) {
        // Remove if already selected
        $this->selectedBusinessIds = array_filter($this->selectedBusinessIds, function($id) use ($businessIdValue) {
            return $id != $businessIdValue;
        });
    } else {
        // Add if not selected
        $this->selectedBusinessIds[] = $businessIdValue;
    }
    
    // Reinitialize form data
    $this->initializeFormData();
}
    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $drivers = $this->driverService->all();
        $businesses = $this->businessService->all();
        $businesses_with_fields = $this->businesses_with_fields;
        return view('livewire.dms.coordinator-report.edit-coordinator-report', compact('main_menu', 'menu', 'drivers', 'businesses', 'businesses_with_fields'));
    }
}