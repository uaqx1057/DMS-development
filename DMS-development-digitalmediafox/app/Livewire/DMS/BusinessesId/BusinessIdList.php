<?php

namespace App\Livewire\DMS\BusinessesId;

use App\Models\BusinessId;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Business ID List')]
class BusinessIdList extends Component
{
    private string $main_menu = 'Business';
    private string $menu = 'Business ID List';

    public function deleteBusinessId($id)
    {
        try {
            $businessId = BusinessId::findOrFail($id);
            $businessId->delete();
            
            session()->flash('success', translate('Business ID deleted successfully!'));
        } catch (\Exception $e) {
            session()->flash('error', translate('Failed to delete Business ID.'));
        }
    }

    public function render()
    {
        $businessIds = BusinessId::with('business')->get();
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $add_permission = CheckPermission(config('const.ADD'), config('const.BUSINESSESID'));
        $edit_permission = CheckPermission(config('const.EDIT'), config('const.BUSINESSESID'));
        
        return view('livewire.dms.business-ids.business-id-list', compact('businessIds', 'main_menu', 'menu', 'add_permission', 'edit_permission'));
    }
}