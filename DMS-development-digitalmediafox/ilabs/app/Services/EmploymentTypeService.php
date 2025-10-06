<?php

namespace App\Services;

use App\Repositories\EmploymentTypeRepository;

class EmploymentTypeService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected EmploymentTypeRepository $employmentTypeRepository)
    {
        //
    }

    public function all()
    {
        return $this->employmentTypeRepository->all();
    }
}
