<?php

namespace App\Livewire\DMS\Fields;

use App\Services\FieldService;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

#[Title('Field List')]
class FieldList extends Component
{
    use WithFileUploads, WithPagination;

    protected FieldService $fieldService;
    private string $main_menu  = 'Field';
    private string $menu  = 'Field List';

    // Properties for modal inputs
    public $name;
    public $type;
    public $required = false;
    public $is_default = false;

    // Modal visibility
    public $showModal = false;

    public function mount(FieldService $fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $fields = $this->fieldService->all();
        $add_permission = CheckPermission(config('const.ADD'), config('const.BUSINESSFIELDS'));
        return view('livewire.dms.fields.field-list', compact('main_menu', 'menu', 'fields', 'add_permission'));
    }

}
