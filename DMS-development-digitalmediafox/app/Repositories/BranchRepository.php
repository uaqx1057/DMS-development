<?php

namespace App\Repositories;

use App\Interfaces\BranchInterface;
use App\Models\Branch;

class BranchRepository implements BranchInterface
{
    public function all(){
        return Branch::all();
    }

    public function first(){
        return Branch::find(1);
    }
}
