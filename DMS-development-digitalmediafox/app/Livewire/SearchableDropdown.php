<?php
namespace App\Http\Livewire;

use Livewire\Component;

class SearchableDropdown extends Component
{
    public $search = '';
    public $selected = null;
    public $options = [];

    public function mount($options, $selected = null)
    {
        $this->options = $options;
        $this->selected = $selected;
    }

    public function render()
    {
        $filteredOptions = collect($this->options)
            ->filter(function ($option) {
                return str_contains(strtolower($option['name']), strtolower($this->search));
            })
            ->toArray();

        return view('livewire.searchable-dropdown', compact('filteredOptions'));
    }
}
