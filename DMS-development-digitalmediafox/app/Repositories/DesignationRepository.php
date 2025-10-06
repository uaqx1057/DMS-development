<?php

namespace App\Repositories;

use App\Interfaces\DesignationInterface;
use App\Models\Designation;

class DesignationRepository implements DesignationInterface
{
    public function all(){
        return Designation::all();
    }
}
