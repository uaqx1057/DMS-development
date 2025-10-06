<?php

namespace App\Services;

use App\Repositories\PayrollRepository;

class PayrollService
{
   /**
     * Create a new class instance.
     */
    public function __construct(protected PayrollRepository $payrollRepository)
    {
        //
    }

    public function all($perPage,int|null $branch_id = null)
    {
        return $this->payrollRepository->all($perPage,$branch_id);
    }

}
