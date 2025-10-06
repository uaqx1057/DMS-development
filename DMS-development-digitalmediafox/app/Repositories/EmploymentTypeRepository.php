<?php

namespace App\Repositories;

use App\Interfaces\EmploymentTypeInterface;
use App\Models\EmploymentType;

class EmploymentTypeRepository implements EmploymentTypeInterface
{
    public function all(){
        return EmploymentType::all();
    }
}
