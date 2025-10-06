<?php

namespace App\Livewire\Driver;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\DriverAttendance;
use Carbon\Carbon;
use App\Models\Business;
use Livewire\WithFileUploads;
use App\Models\Order;

class Dashboard extends Component
{
    use WithFileUploads;

    public $error = null;
    public $showMeterInput = false;
    public $meter_reading;
    public $meter_image;
    
    public $attendance;
    public $totalHours = "0h 0m";
    public $businesses;
    
    public $showOutMeterInput = false;
    public $out_meter_reading;
    public $out_meter_image;
    
    public $todayOrdersCount;


    #[Layout('components.layouts.plain')]
    public function render()
    {
        $this->businesses = Business::select('id', 'name', 'image')->where('branch_id',auth()->user()->branch_id)->get();
        return view('livewire.driver.dashboard', [
            'attendance' => $this->attendance,
            'totalHours' => $this->totalHours,
            'businesses' => $this->businesses,
        ]);
        
        
    }

    public function mount()
    {
        if (!Auth::guard('driver')->check()) {
            return redirect()->route('driver.login');
        }

        $driver = Auth::guard('driver')->user();

        // Check if driver is already checked in today (without checkout)
        $this->attendance = DriverAttendance::where('driver_id', $driver->id)
            ->whereDate('checkin_time', Carbon::today())
            ->whereNull('checkout_time')
            ->first();

        $this->calculateTotalHours($driver->id);

        $this->todayOrdersCount = Order::where('driver_id', $driver->id)
            ->whereDate('created_at', Carbon::today())
            ->count();
    }

    public function checkIn()
    {
         $this->showMeterInput = true;
        
    }
    
    public function checkinCancel()
    {
        $this->showMeterInput = false;
    }
    
   public function saveCheckInData()
    {
        $this->validate([
    'meter_reading' => 'required_without:meter_image|numeric|min:0|nullable',
    'meter_image'   => 'required_without:meter_reading|image|max:2048|nullable',
], [
    'meter_reading.required_without' => '⚠️ Please enter the meter reading if no image is uploaded.',
    'meter_reading.numeric'          => '⚠️ Meter reading must be a number.',
    'meter_reading.min'              => '⚠️ Meter reading must be at least 0.',
    
    'meter_image.required_without'   => '⚠️ Please upload a meter image if no reading is entered.',
    'meter_image.image'              => '⚠️ The meter image must be a valid image file.',
    'meter_image.max'                => '⚠️ The meter image size may not exceed 2MB.',
]);

    
        $path = null;
        if ($this->meter_image) {
            $path = $this->meter_image->store('meter_images', 'public');
        }
        
        $driver = Auth::guard('driver')->user();

        $this->attendance = DriverAttendance::create([
            'driver_id' => $driver->id,
            'checkin_time' => Carbon::now(),
            'meter_reading' => $this->meter_reading,
            'meter_image'   => $path,
        ]);

        $this->calculateTotalHours($driver->id);
        session()->flash('success', '✅ Check-in completed successfully!');
        $this->reset(['meter_reading', 'meter_image', 'showMeterInput']);
    }


    public function checkOut()
    {
        $driver = Auth::guard('driver')->user();
    
        // Check if the driver has any active orders (not delivered or canceled)
        $hasActiveOrders = Order::where('driver_id', $driver->id)
        ->where(function ($query) {
        $query->whereNull('status')
        ->orWhereNotIn('status', ['Drop', 'Cancel']);
        })
        ->exists();

    
        if ($hasActiveOrders) {
            $this->error = "You cannot check out because you have active orders pending.";
            $this->showOutMeterInput = false;
        } else {
            $this->error = null;
            $this->showOutMeterInput = true;
        }
    }

    
     public function checkoutCancel()
    {
        $this->showOutMeterInput = false;
    }

    public function saveCheckOutData()
    {
          // Step 2: Validate input
        $this->validate([
            'out_meter_reading' => 'required_without:out_meter_image|numeric|min:0|nullable',
            'out_meter_image'   => 'required_without:out_meter_reading|image|max:2048|nullable',
        ], [
            'out_meter_reading.required_without' => '⚠️ Please enter the checkout meter reading if no image is uploaded.',
            'out_meter_reading.numeric'          => '⚠️ Checkout meter reading must be a number.',
            'out_meter_reading.min'              => '⚠️ Checkout meter reading must be at least 0.',
        
            'out_meter_image.required_without'   => '⚠️ Please upload a checkout meter image if no reading is entered.',
            'out_meter_image.image'              => '⚠️ The checkout meter image must be a valid image file.',
            'out_meter_image.max'                => '⚠️ The checkout meter image size may not exceed 2MB.',
        ]);

        
            // Step 3: Save image if uploaded
            $path = null;
            if ($this->out_meter_image) {
                $path = $this->out_meter_image->store('meter_images', 'public');
            }
        
            // Step 4: Update attendance record
            if ($this->attendance) {
                $this->attendance->update([
                    'checkout_time'     => Carbon::now(),
                    'out_meter_reading' => $this->out_meter_reading,
                    'out_meter_image'   => $path,
                ]);
        
                $driver = Auth::guard('driver')->user();
                $this->attendance = null;
        
                $this->calculateTotalHours($driver->id);
            }
        
            // Step 5: Reset state
            session()->flash('success', '✅ Check-out completed successfully!');
            $this->reset(['out_meter_reading', 'out_meter_image', 'showOutMeterInput']);
    }

public function calculateTotalHours($driverId)
{
    $attendances = \App\Models\DriverAttendance::where('driver_id', $driverId)
        ->whereNotNull('checkin_time')
        ->whereDate('checkin_time', \Carbon\Carbon::today()) // keep only today
        ->get();

    if ($attendances->isEmpty()) {
        $this->totalHours = "0h 0m";
        return;
    }

    $totalSeconds = 0;

    foreach ($attendances as $attendance) {
        $start = \Carbon\Carbon::parse($attendance->checkin_time);

        // if not checked out, use current time
        $end = $attendance->checkout_time
            ? \Carbon\Carbon::parse($attendance->checkout_time)
            : now();

        $totalSeconds += $end->diffInSeconds($start);
    }

    $interval = \Carbon\CarbonInterval::seconds($totalSeconds)->cascade();

    $humanReadable = $interval->forHumans([
        'short' => true,
        'parts' => 2,
        'join' => true,
    ]);

    $this->totalHours = str_replace(' and ', ' ', $humanReadable);
}





}
