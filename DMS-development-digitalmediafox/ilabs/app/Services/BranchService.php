<?php

namespace App\Services;

use App\Repositories\BranchRepository;

class BranchService
{
   /**
     * Create a new class instance.
     */
    public function __construct(protected BranchRepository $branchRepository)
    {
        //
    }

    public function all()
    {
        return $this->branchRepository->all();
    }

    public function first()
    {
        return $this->branchRepository->first();
    }


}
