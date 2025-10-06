<?php

namespace App\Services;

use App\Repositories\DesignationRepository;

class DesignationService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected DesignationRepository $designationRepository)
    {
        //
    }

    public function all()
    {
        return $this->designationRepository->all();
    }
}
