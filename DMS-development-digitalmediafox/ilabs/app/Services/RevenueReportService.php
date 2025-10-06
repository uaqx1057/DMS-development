<?php

namespace App\Services;

use App\Repositories\RevenueReportRepository;

class RevenueReportService
{
   /**
     * Create a new class instance.
     */
    public function __construct(protected RevenueReportRepository $revenueReportRepository)
    {
        //
    }

    public function all()
    {
        return $this->revenueReportRepository->all();
    }

    public function getBusinesses()
    {
        return $this->revenueReportRepository->getBusinesses();
    }

    public function getRevenueStats()
    {
        return $this->revenueReportRepository->getRevenueStats();
    }
}
