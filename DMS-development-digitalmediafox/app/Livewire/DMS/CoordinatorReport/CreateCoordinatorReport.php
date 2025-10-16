<?php

namespace App\Livewire\DMS\CoordinatorReport;

use App\Traits\DMS\CoordinatorReportTrait;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\Attributes\Title;

#[Title('Create Coordinator Report')]
class CreateCoordinatorReport extends Component
{
    use WithFileUploads, CoordinatorReportTrait;

    public string $main_menu = 'Coordinator Report';
    public string $menu = 'Create Coordinator Report';

    public function onDriverChange()
    {
        $this->resetBusinessData();
    }

    public function create(){
        $validated = $this->validations();
        $validated['status'] = $this->status;
        // Make sure we're passing the correct data
        $validated['selectedBusinessIds'] = $this->selectedBusinessIds;
        $validated['formData'] = $this->formData;
        $validated['files'] = $this->files;
        
        $checkCoordinatorReport = $this->coordinatorReportService->checkDuplicateReport([
            'report_date' => $validated['report_date'],
            'driver_id' => $validated['driver_id']
        ]);

        if(!is_null($checkCoordinatorReport)){
            $this->addError('report_date', translate('Coordinator Report Against This Driver and Date already exists'));
            return;
        }

        $this->coordinatorReportService->create($validated);
        session()->flash('success', translate('Coordinator Report Created Successfully!'));
        return $this->redirectRoute('coordinator-report.index', navigate:true);
    }



    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $drivers = $this->driverService->all();
        $businesses = $this->businessService->all();
        $businesses_with_fields = $this->businesses_with_fields;
        return view('livewire.dms.coordinator-report.create-coordinator-report', compact('main_menu', 'menu', 'drivers', 'businesses', 'businesses_with_fields'));
    }

}
