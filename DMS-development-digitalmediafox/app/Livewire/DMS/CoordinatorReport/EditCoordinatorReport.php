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

    public function update(){
        $validated = $this->validations();
        $validated['selectedBusinessIds'] = $this->selectedBusinessIds;
        $validated['formData'] = $this->formData;
        $validated['files'] = $this->files;
        $validated['id'] = $this->coordinatorReportId;
        $validated['status'] = $this->status;
        
        // Automatically set branch_id from the selected driver
        if (!empty($validated['driver_id'])) {
            $driver = \App\Models\Driver::find($validated['driver_id']);
            if ($driver && $driver->branch_id) {
                $validated['branch_id'] = $driver->branch_id;
            }
        }
        
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

    // Add this method to handle individual checkbox changes
    public function toggleBusinessId($businessIdValue, $checked)
    {
        if ($checked) {
            // Add to selectedBusinessIds if checked
            if (!in_array($businessIdValue, $this->selectedBusinessIds)) {
                $this->selectedBusinessIds[] = $businessIdValue;
            }
        } else {
            // Remove from selectedBusinessIds if unchecked
            $this->selectedBusinessIds = array_filter($this->selectedBusinessIds, function($id) use ($businessIdValue) {
                return $id != $businessIdValue;
            });
        }
        
        // Re-index array
        $this->selectedBusinessIds = array_values($this->selectedBusinessIds);
        
        // Initialize form data
        $this->initializeFormData();
    }

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $drivers = $this->driverService->all();
        
        // Use availableBusinesses instead of all businesses
        $businesses = !empty($this->driver_id) ? $this->availableBusinesses : collect([]);
        
        $businesses_with_fields = $this->businesses_with_fields;
        return view('livewire.dms.coordinator-report.edit-coordinator-report', compact('main_menu', 'menu', 'drivers', 'businesses', 'businesses_with_fields'));
    }
}