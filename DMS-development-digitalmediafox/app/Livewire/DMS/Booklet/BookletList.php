<?php

namespace App\Livewire\DMS\Booklet;

use App\Models\Booklet;
use Livewire\Component;
use App\Traits\DataTableTrait;

class BookletList extends Component
{
    use DataTableTrait;

    private string $main_menu = 'Booklet';
    private string $menu = 'Booklet List';

    public function mount()
    {
        $perPage = $this->perPage;

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
        $add_permission = CheckPermission(config('const.ADD'), config('const.BOOKLET'));
        $edit_permission = CheckPermission(config('const.EDIT'), config('const.BOOKLET'));

        $columns = [
            ['label' => 'Booklet Number', 'column' => 'booklet_number', 'isData' => true, 'hasRelation' => false],
            ['label' => 'Operation Superviser', 'column' => 'user', 'isData' => true, 'hasRelation' => true, 'columnRelation' => 'name'],
            ['label' => 'Action', 'column' => 'action', 'isData' => false, 'hasRelation' => false],
        ];
       $booklets = Booklet::with('user')
        ->whereHas('user', function ($q) {
            $q->where('branch_id', auth()->user()->branch_id);
        })
        ->where(function ($query) {
            $query->where('booklet_number', 'like', "%{$this->search}%")
                ->orWhereHas('user', function ($q2) {
                        $q2->where('name', 'like', "%{$this->search}%");
                });
        })
        ->orderBy($this->sortColumn, $this->sortDirection)
        ->paginate($this->perPage);


        return view('livewire.dms.booklet.booklet-list', compact('booklets', 'main_menu', 'menu', 'add_permission', 'edit_permission', 'columns'));
    }

    public function delete($id)
    {
        $booklet = Booklet::findOrFail($id);
        $booklet->delete(); // soft delete

        session()->flash('success', __('Booklet deleted successfully.'));
    }
}
