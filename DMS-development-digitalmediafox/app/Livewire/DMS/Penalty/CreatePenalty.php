<?php

namespace App\Livewire\DMS\Penalty;

use App\Models\{Penalty, CoordinatorReport, CoordinatorReportFieldValue, Driver, Business, BusinessId};
use Livewire\Component;
use Livewire\WithFileUploads;

class CreatePenalty extends Component
{
    use WithFileUploads;

    public string $main_menu = 'DMS';
    public string $menu = 'Create Penalty';

    public $drivers = [];
    public $businesses = [];
    public $businessIds = [];

    public $driver_id;
    public $business_id;
    public $business_id_value;
    public $penalty_date;
    public $penalty_value;
    public $penalty_file;

    public function mount()
    {
        $this->drivers = Driver::select('id', 'name')->get();
    }

    public function updatedDriverId()
    {
        $this->reset(['business_id', 'business_id_value', 'businesses', 'businessIds']);

        if (!$this->driver_id) return;

        $driver = Driver::with('businesses')->find($this->driver_id);
        $this->businesses = $driver ? $driver->businesses : collect();
    }

    public function updatedBusinessId()
    {
        $this->reset(['business_id_value', 'businessIds']);

        if (!$this->driver_id || !$this->business_id) return;

        $driver = Driver::find($this->driver_id);

        $this->businessIds = $driver->businessIds()
            ->where('business_id', $this->business_id)
            ->wherePivotNull('transferred_at')
            ->get(['business_ids.id', 'business_ids.value']);
    }

    public function save()
    {
        $this->validate([
            'driver_id' => 'required|exists:drivers,id',
            'business_id' => 'required|exists:businesses,id',
            'business_id_value' => 'required|exists:business_ids,id',
            'penalty_date' => 'required|date',
            'penalty_value' => 'required|numeric|min:0',
            'penalty_file' => 'required|file|max:2048',
        ]);

        $filePath = $this->penalty_file ? $this->penalty_file->store('penalties', 'public') : null;

        // Find or create coordinator report
        $report = CoordinatorReport::firstOrCreate(
            [
                'driver_id' => $this->driver_id,
                'report_date' => $this->penalty_date,
            ],
            [
                'status' => 'Pending',
                'branch_id' => optional(Driver::find($this->driver_id))->branch_id,
            ]
        );
        if (!empty($this->business_id_value)) {
                $businessTypeIds = BusinessId::whereIn('id', [$this->business_id_value])
                    ->pluck('business_id')
                    ->unique()
                    ->toArray();

                $report->businesses()->syncWithoutDetaching($businessTypeIds);
            }
        // Check existing field value (field_id = 14)
        $fieldValue = CoordinatorReportFieldValue::where('coordinator_report_id', $report->id)
            ->where('business_id', $this->business_id)
            ->where('business_id_value', $this->business_id_value)
            ->where('field_id', 14)
            ->first();

        if ($fieldValue) {
            $fieldValue->update(['value' => $this->penalty_value]);
        } else {
            CoordinatorReportFieldValue::create([
                'coordinator_report_id' => $report->id,
                'business_id' => $this->business_id,
                'business_id_value' => $this->business_id_value,
                'field_id' => 14,
                'value' => $this->penalty_value,
            ]);
        }

        // Create penalty record
        Penalty::create([
            'driver_id' => $this->driver_id,
            'business_id' => $this->business_id,
            'business_id_value' => $this->business_id_value,
            'coordinator_report_id' => $report->id,
            'penalty_date' => $this->penalty_date,
            'penalty_value' => $this->penalty_value,
            'penalty_file' => $filePath,
        ]);

        $this->reset(['driver_id', 'business_id', 'business_id_value', 'penalty_date', 'penalty_value', 'penalty_file']);
        session()->flash('success', 'Penalty added successfully and report updated!');
        return redirect()->route('penalty.index');
    }

    public function render()
    {
        return view('livewire.dms.penalty.create-penalty', [
            'main_menu' => $this->main_menu,
            'menu' => $this->menu,
        ]);
    }
}