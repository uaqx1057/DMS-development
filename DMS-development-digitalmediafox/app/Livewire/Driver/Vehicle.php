<?php

namespace App\Livewire\Driver;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\AssignDriverReport;

class Vehicle extends Component
{
    public $currentAssignments = [];
    public $historyAssignments = [];

    public function mount()
    {
        $driverId = auth()->guard('driver')->id();

        $assignments = AssignDriverReport::with('vehicle')
            ->where('driver_id', $driverId)
            ->latest()
            ->get();

        $this->currentAssignments = $assignments->where('status', 'assign');
        $this->historyAssignments = $assignments->where('status', 'unassign');
    }

    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.driver.vehicle');
    }
}
