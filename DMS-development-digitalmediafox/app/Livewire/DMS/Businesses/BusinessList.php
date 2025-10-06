<?php

namespace App\Livewire\DMS\Businesses;

use App\Services\BusinessService;
use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\Branch;
use App\Models\Business;

#[Title('Business List')]
class BusinessList extends Component
{
    protected BusinessService $businessService;
    private string $main_menu  = 'Businesss';
    private string $menu  = 'Business List';

    public function mount(BusinessService $businessService)
    {
        $this->businessService = $businessService;
    }

    public function render()
    {
        $businesses = Business::with('branch')->get();
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $add_permission = CheckPermission(config('const.ADD'), config('const.BUSINESSES'));
        $edit_permission = CheckPermission(config('const.EDIT'), config('const.BUSINESSES'));
        return view('livewire.dms.businesses.business-list', compact('businesses', 'main_menu', 'menu', 'add_permission', 'edit_permission'));
    }
}
