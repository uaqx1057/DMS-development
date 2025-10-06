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

    /**
     * Get all revenue reports, optionally filtered by branch.
     */
    public function all(int|null $branch_id = null)
    {
        return $this->revenueReportRepository->all($branch_id);
    }

    /**
     * Get all businesses, optionally filtered by branch.
     */
    public function getBusinesses(int|null $branch_id = null)
    {
        return $this->revenueReportRepository->getBusinesses($branch_id);
    }

    /**
     * Get revenue statistics, optionally filtered by branch.
     */
    public function getRevenueStats(int|null $branch_id = null)
    {
        return $this->revenueReportRepository->getRevenueStats($branch_id);
    }
}
