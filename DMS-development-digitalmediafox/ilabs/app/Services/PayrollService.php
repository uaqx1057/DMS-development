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

    public function all($perPage)
    {
        return $this->payrollRepository->all($perPage);
    }

}
