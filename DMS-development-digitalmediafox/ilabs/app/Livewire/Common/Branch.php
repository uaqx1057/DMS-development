<?php

namespace App\Livewire\Common;

use App\Services\BranchService;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class Branch extends Component
{
    #[Modelable]
    public $branch_id = '';
    public $branches = [];
    protected BranchService $branchService;

    public function mount(BranchService $branchService): void
    {
        $this->branchService = $branchService;
        $this->branches = cache()->rememberForever('branches', function () {
            return $this->branchService->all();
        });
    }

    public function render()
    {
        return view('livewire.common.branch');
    }
}
