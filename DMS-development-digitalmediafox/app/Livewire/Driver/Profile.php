<?php

namespace App\Livewire\Driver;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class Profile extends Component
{
    use WithFileUploads;

    #[Layout('components.layouts.plain')]
    public $driver;
    public $totalOrders;
    public $branchName;

    public $editMode = false; // toggle edit mode
    public $name;
    public $mobile;
    public $image;
    public $nationality;
    public $language;

    public function mount()
    {
        
    }

    private function loadDriverData()
    {
        $this->driver = Auth::guard('driver')->user();

        $this->name = $this->driver->name;
        $this->mobile = $this->driver->mobile;
        $this->nationality = $this->driver->nationality;
        $this->language = $this->driver->language;

        // total orders with status 'Drop'
        $this->totalOrders = Order::where('driver_id', $this->driver->id)
            ->where('status', 'Drop')
            ->count();

        $this->branchName = $this->driver->branch ? $this->driver->branch->name : 'No Branch';
    }

    public function toggleEdit()
    {
        $this->editMode = !$this->editMode;
    }

    public function saveProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'image' => 'nullable|image|max:1024',
            'nationality' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:255',
        ]);

        $this->driver->update([
            'name' => $this->name,
            'mobile' => $this->mobile,
            'nationality' => $this->nationality,
            'language' => $this->language,
        ]);

        if ($this->image) {
            $path = $this->image->store('drivers', 'public');
            $this->driver->update(['image' => $path]);
        }


        $this->editMode = false;

        // Optional: show a success message
        session()->flash('message', 'Profile updated successfully.');
    }

    public function render()
    {
         // Refresh driver data after save
        $this->loadDriverData();
        return view('livewire.driver.profile', [
            'driver' => $this->driver,
            'totalOrders' => $this->totalOrders,
            'branchName' => $this->branchName,
        ]);
    }
}
