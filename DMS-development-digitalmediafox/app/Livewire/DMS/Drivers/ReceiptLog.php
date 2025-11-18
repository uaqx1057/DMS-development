<?php

namespace App\Livewire\DMS\Drivers;

use App\Models\DriverReceipt;
use App\Traits\DataTableTrait;
use Livewire\Component;

class ReceiptLog extends Component
{
    use DataTableTrait;

    private string $main_menu = 'Receipt';
    private string $menu = 'Driver Receipt List';

    public function mount()
    {
        if (session()->has('page')) {
            $this->page = session('page');
        }
    }

    public function boot()
    {
        if (session()->has('page')) {
            $this->page = session('page');
        }
    }
    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $add_permission = false;
        $edit_permission = false;

        $columns = [
            ['label' => 'Created By', 'column' => 'user', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Booklet No', 'column' => 'booklet', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'booklet_number'],
            ['label' => 'Receipt No', 'column' => 'reaceipt_no', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Receipt Date', 'column' => 'receipt_date', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Driver', 'column' => 'driver', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Platform', 'column' => 'business', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Platform ID', 'column' => 'businessValue', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'value'],
            ['label' => 'Amount Received', 'column' => 'amount_received', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Image', 'column' => 'receipt_image', 'isData' => false, 'hasRelation' => false],
            ['label' => 'Created At', 'column' => 'created_at', 'isData' => true, 'hasRelation' => false],
        ];

        $driverReceipts = DriverReceipt::with(['driver', 'user', 'booklet', 'business', 'businessValue'])
            ->when(auth()->user()->role_id != 1, function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->when($this->search, function ($query) {
                $search = $this->search;

                $query->where(function ($q) use ($search) {
                    $q->where('reaceipt_no', 'like', "%$search%")
                        ->orWhere('receipt_date', 'like', "%$search%")
                        ->orWhere('amount_received', 'like', "%$search%")
                        ->orWhere('created_at', 'like', "%$search%")
                        ->orWhereHas('user', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('driver', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('booklet', function ($q2) use ($search) {
                            $q2->where('booklet_number', 'like', "%$search%");
                        })
                        ->orWhereHas('business', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('businessValue', function ($q2) use ($search) {
                            $q2->where('value', 'like', "%$search%");
                        });
                });
            })
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);
        return view('livewire.dms.drivers.receipt-log', compact(
            'driverReceipts',
            'main_menu',
            'menu',
            'add_permission',
            'edit_permission',
            'columns'
        ));
    }
}
