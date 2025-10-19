<?php

namespace App\Livewire\DMS\BusinessesId;

use App\Models\BusinessId;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Platform')]
class BusinessIdList extends Component
{
    use WithPagination;

    private string $main_menu = 'Business';
    private string $menu = 'Platform';
    
    public $search = '';
    public $perPage = 10;

    public function deleteBusinessId($id)
    {
        try {
            $businessId = BusinessId::findOrFail($id);
            $businessId->delete();
            
            session()->flash('success', translate('Platform deleted successfully!'));
        } catch (\Exception $e) {
            session()->flash('error', translate('Failed to delete Platform.'));
        }
    }

    public function toggleStatus($id)
    {
        try {
            $businessId = BusinessId::findOrFail($id);
            $businessId->update([
                'is_active' => !$businessId->is_active
            ]);
            
            $status = $businessId->is_active ? 'activated' : 'deactivated';
            session()->flash('success', translate("Platform {$status} successfully!"));
        } catch (\Exception $e) {
            session()->flash('error', translate('Failed to update platform status.'));
        }
    }

    public function render()
    {
        $businessIds = BusinessId::with('business')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('value', 'like', '%' . $this->search . '%')
                      ->orWhereHas('business', function ($q2) {
                          $q2->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->paginate($this->perPage);

        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $add_permission = CheckPermission(config('const.ADD'), config('const.BUSINESSESID'));
        $edit_permission = CheckPermission(config('const.EDIT'), config('const.BUSINESSESID'));
        
        return view('livewire.dms.business-ids.business-id-list', compact('businessIds', 'main_menu', 'menu', 'add_permission', 'edit_permission'));
    }
}