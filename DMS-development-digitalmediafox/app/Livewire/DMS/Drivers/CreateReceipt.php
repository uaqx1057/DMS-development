<?php

namespace App\Livewire\DMS\Drivers;

use App\Models\Booklet;
use App\Models\BusinessId;
use App\Models\CoordinatorReport;
use App\Models\CoordinatorReportFieldValue;
use App\Models\Driver;
use App\Models\DriverReceipt;
use App\Services\BusinessService;
use App\Services\DriverService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateReceipt extends Component
{
    use WithFileUploads;

    public string $main_menu = 'Receipt';
    public string $menu = 'Driver Receipt';

    public $driver;
    public $driverExists = true;

    // Form fields
    public $booklet_number;
    public $reaceipt_no;
    public $receipt_date;
    public $receipt_image;
    public $driver_id;
    public $business_id;
    public $business_id_value;
    public $amount_received;
    public $max_receivable = 0;

    public $businessValues = [];
    public $assignBusiness = [];

    protected DriverService $driverService;
    protected BusinessService $businessService;

    public function boot(DriverService $driverService, BusinessService $businessService)
    {
        $this->driverService = $driverService;
        $this->businessService = $businessService;
    }

    public function updatedAmountReceived($value)
    {
        if ($this->max_receivable > 0 && $value > $this->max_receivable) {
            $this->amount_received = $this->max_receivable;
            $this->addError('amount_received', 'Amount cannot be more than ' . $this->max_receivable);
            return;
        }
        $this->resetErrorBag('amount_received');
    }

    public function updatedDriverId($driverId)
    {
        $this->driverExists = true;

        $this->driver = $this->driverService->find($driverId);

        if (!$this->driver) {
            $this->driverExists = false;
            session()->flash('error', __('Driver not found!'));
            return;
        }

        $this->driver_id = $driverId;

        // RESET dependent dropdowns
        $this->business_id = null;
        $this->business_id_value = null;
        $this->businessValues = [];

        // Receipt calculations
        $total_receipts = DriverReceipt::where('driver_id', $driverId)->sum('amount_received');

        $reportIDs = CoordinatorReport::where('driver_id', $driverId)->pluck('id');

        $cash_collected = CoordinatorReportFieldValue::whereIn('coordinator_report_id', $reportIDs)
            ->where('field_id', 7)
            ->sum('value');

        $this->max_receivable = $cash_collected - $total_receipts;

        // Fetch assigned businesses
        $this->assignBusiness = DB::table('business_driver')
            ->where('driver_id', $driverId)
            ->pluck('business_id')
            ->unique()
            ->values()
            ->toArray();
    }

    public function updatedBusinessId($businessId)
    {
        if (!$businessId) {
            $this->businessValues = [];
            $this->business_id_value = null;
            return;
        }

        $allIDs = BusinessId::where('business_id', $businessId)->get();

        $assignedIDs = DB::table('driver_business_ids')
            ->where('driver_id', $this->driver_id)
            ->whereNull('transferred_at')
            ->pluck('business_id_id')
            ->toArray();

        // Only show IDs assigned to this driver
        $this->businessValues = $allIDs->whereIn('id', $assignedIDs)->values();
        $this->business_id_value = null;
    }

    public function save()
    {
        $this->validate([
            'booklet_number' => 'required',
            'reaceipt_no' => 'required|string|unique:driver_receipts,reaceipt_no',
            'receipt_date' => 'required|date|before_or_equal:today',
            'receipt_image' => 'required|image',
            'driver_id' => 'required',
            'business_id' => 'required',
            'business_id_value' => 'required',
            'amount_received' => 'required|numeric|min:1',
        ]);

        // Prevent duplicate receipts for same date
        $exist = DriverReceipt::where('driver_id', $this->driver_id)
            ->where('receipt_date', $this->receipt_date)
            ->exists();

        if ($exist) {
            $this->addError('receipt_date', 'This driver has already created receipt on this date.');
            return;
        }
        $driverData = Driver::where('id',$this->driver_id)->first();

        if ($this->receipt_image) {
            $filename = now()->format('Ymd_His') . '-' . $driverData->name . '-' . $driverData->iqaama_number . '.' . $this->receipt_image->getClientOriginalExtension();
            $path = $this->receipt_image->storeAs('receipts', $filename, 'public');
        } else {
            $path = null;
        }


        DriverReceipt::create([
            'booklet_id' => $this->booklet_number,
            'user_id' => auth()->id(),
            'reaceipt_no' => $this->reaceipt_no,
            'receipt_date' => $this->receipt_date,
            'receipt_image' => $path,
            'driver_id' => $this->driver_id,
            'business_id' => $this->business_id,
            'business_id_value' => $this->business_id_value,
            'amount_received' => $this->amount_received,
        ]);

        session()->flash('success', __('Receipt created successfully.'));
        return redirect()->route('driver-difference.index');
    }

    public function render()
    {
        $drivers = Driver::when(auth()->user()->role_id != 1, function ($q) {
            $q->where('branch_id', auth()->user()->branch_id);
        })->get();

        $businesses = $this->businessService->all();

        $booklets = Booklet::when(auth()->user()->role_id != 1, function ($q) {
            $q->whereHas('user', fn ($u) => $u->where('branch_id', auth()->user()->branch_id));
        })->get();

        return view('livewire.dms.drivers.create-receipt', [
            'main_menu'   => $this->main_menu,
            'menu'        => $this->menu,
            'drivers'     => $drivers,
            'businesses'  => $businesses,
            'booklets'    => $booklets,
        ]);
    }
}
