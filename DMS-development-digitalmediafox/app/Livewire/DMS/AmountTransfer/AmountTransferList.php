<?php

namespace App\Livewire\DMS\AmountTransfer;

use App\Models\AmountTransfer;
use App\Traits\DataTableTrait;
use Livewire\Component;

class AmountTransferList extends Component
{
    use DataTableTrait;

    private string $main_menu = 'Transfer';
    private string $menu = 'Amount Transfer List';

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
        $add_permission = CheckPermission(config('const.ADD'), config('const.AMOUNTTRANSFER'));
        $edit_permission = false;

        $columns = [
            ['label' => 'Payment Type', 'column' => 'payment_type', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Date', 'column' => 'receipt_date', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Amount', 'column' => 'amount', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Image', 'column' => 'receipt_image', 'isData' => false, 'hasRelation' => false],
        ];

        if (auth()->user()->role_id == 1) {
            $columns = array_merge([
                ['label' => 'Supervisor Name', 'column' => 'supervisor', 'isData' => true, 'hasRelation'=> true, 'columnRelation' => 'name'],
                ['label' => 'Created By', 'column' => 'creator', 'isData' => true, 'hasRelation'=> true, 'columnRelation' => 'name'],
            ], $columns);
            
        $amountTransfers = AmountTransfer::with('supervisor')->with('creator')->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);
        }
        else{
            $amountTransfers = AmountTransfer::with('supervisor')
                                ->with('creator')
                                ->when(auth()->user()->role_id != 1, function($query) {
                                    $query->whereHas('supervisor', function ($q) {
                                        $q->where('branch_id', auth()->user()->branch_id);
                                    });
                                })
                                ->orderBy($this->sortColumn, $this->sortDirection)
                                ->paginate($this->perPage);
        }


        return view('livewire.dms.amount-transfer.amount-transfer-list', compact(
            'amountTransfers',
            'main_menu',
            'menu',
            'add_permission',
            'edit_permission',
            'columns'
        ));
    }
}
