<?php

namespace App\Services;

use App\Repositories\DepartmentRepository;

class DepartmentService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected DepartmentRepository $departmentRepository)
    {
        //
    }

    public function all()
    {
        return $this->departmentRepository->all();
    }
}
