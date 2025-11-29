<?php

namespace App\Livewire\DMS\Recharge;

use App\Models\RequestRecharge;
use App\Traits\DataTableTrait;
use Livewire\Component;

class Rechargelist extends Component
{
    use DataTableTrait;
    private string $main_menu = 'Recharge';
    private string $menu = 'Request Recharge List';

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
            ['label' => 'Driver Name', 'column' => 'driver', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Phone No.', 'column' => 'mobile', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Opearator', 'column' => 'opearator', 'isData' => true, 'hasRelation' => false],
            // ['label' => 'Status', 'column' => 'status', 'isData' => false, 'hasRelation' => false],
            ['label' => 'Approved By', 'column' => 'approved', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Action', 'column' => 'driverRecharge', 'isData' => false, 'hasRelation' => false],
        ];


        $query = RequestRecharge::with('recharge')->where('status', 'accept');

        // Apply search if not empty
        if ($this->search) {
            $search = $this->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('driver', function ($d) use ($search) {
                    $d->where('name', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('mobile', 'LIKE', "%{$search}%")
                    ->orWhere('opearator', 'LIKE', "%{$search}%");
            });
        }

        // Final result
        $requestRecharges = $query
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);
            
        return view('livewire.dms.recharge.rechargelist', compact(
            'requestRecharges',
            'main_menu',
            'menu',
            'add_permission',
            'edit_permission',
            'columns'
        ));
    }
}
