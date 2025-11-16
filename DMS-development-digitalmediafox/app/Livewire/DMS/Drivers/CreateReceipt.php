<?php

namespace App\Livewire\DMS\Drivers;

use App\Models\Booklet;
use App\Models\BusinessId;
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
    public $driverId;
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

    public $businesses = [];
    public $businessValues = [];
    public $assignBusinessIds = [];

    protected DriverService $driverService;
    protected BusinessService $businessService;

    public function boot(DriverService $driverService, BusinessService $businessService)
    {
        $this->driverService = $driverService;
        $this->businessService = $businessService;
    }

    public function mount($id)
    {
        $this->driverId = $id;
        $this->driver = $this->driverService->find($id);

        if (!$this->driver) {
            $this->driverExists = false;
            session()->flash('error', __('Driver not found!'));
            return;
        }

        $this->businesses = $this->businessService->all();
        $this->driver_id = $id;
        $total_receipts = \App\Models\DriverReceipt::where('driver_id', $this->driver_id)->sum('amount_received');

        $driver_coordinate_reports_ids = \App\Models\CoordinatorReport::where('driver_id', $this->driver_id)
            ->pluck('id')
            ->toArray();

        $cash_collected = \App\Models\CoordinatorReportFieldValue::whereIn('coordinator_report_id', $driver_coordinate_reports_ids)
            ->where('field_id', 7)
            ->sum('value');

        $this->max_receivable = $cash_collected - $total_receipts;

    }
    public function updatedAmountReceived($value)
    {
        if ($this->max_receivable > 0 && $value > $this->max_receivable) {
            $this->amount_received = $this->max_receivable;      
            $this->addError('amount_received', 'Amount cannot be more than ' . $this->max_receivable);
            return;
        }
        // If amount is valid → remove the validation error
        $this->resetErrorBag('amount_received');
    }


    public function updatedBusinessId($businessId)
    {
        if (!$businessId) {
            $this->businessValues = [];
            $this->business_id_value = null;
            return;
        }

        // Get all IDs for the selected business
        $allBusinessValues = BusinessId::where('business_id', $businessId)->get();

        // Get IDs assigned to this driver for this business
        $assignedIds = DB::table('driver_business_ids')
            ->where('driver_id', $this->driver_id)
            ->whereNull('transferred_at')
            ->pluck('business_id_id')
            ->toArray();

        // Filter only assigned IDs for this business
        $this->businessValues = $allBusinessValues->filter(function ($item) use ($assignedIds) {
            return in_array($item->id, $assignedIds);
        })->values();

        $this->business_id_value = null; // reset selection
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

        // Check if this supervisor already created a booklet today
        $existDriverReceipt = DriverReceipt::where('driver_id', $this->driver_id)
            ->where('receipt_date', $this->receipt_date)
            ->exists();

        if ($existDriverReceipt) {
            $this->addError('receipt_date', 'This Driver has already created receipt this date.');
            return;
        }

        $path = null;
        if ($this->receipt_image) {
            $path = $this->receipt_image->store('receipts', 'public');
        }

        DriverReceipt::create([
            'booklet_id' => $this->booklet_number,
            'user_id' => auth()->user()->id,
            'reaceipt_no' => $this->reaceipt_no,
            'receipt_date' => $this->receipt_date,
            'receipt_image' => $path,
            'driver_id' => $this->driver_id,
            'business_id' => $this->business_id,
            'business_id_value' => $this->business_id_value,
            'amount_received' => $this->amount_received,
        ]);

        // Driver::where('id', $this->driver_id)->update([
        //     'total_receipt' => DB::raw('total_receipt + ' . $this->amount_received)
        // ]);

        session()->flash('success', __('Receipt created successfully.'));
        return redirect()->route('drivers.index');
    }

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $driver = $this->driver;
        $businesses = $this->businesses;

        $booklets = Booklet::whereHas('user',function($query) {
            $query->where('branch_id', auth()->user()->branch_id);
        })->get();

        $assignBusiness = DB::table('business_driver')
            ->where('driver_id', $this->driver_id)
            ->pluck('business_id')
            ->unique()
            ->values()
            ->toArray();

        return view('livewire.dms.drivers.create-receipt', compact(
            'main_menu',
            'menu',
            'driver',
            'businesses',
            'assignBusiness',
            'booklets'
        ));
    }
}
