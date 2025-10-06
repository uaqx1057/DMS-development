<?php

namespace App\Services;

use App\Repositories\CoordinatorReportRepository;
use Illuminate\Support\Facades\{DB, Log};

class CoordinatorReportService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected CoordinatorReportRepository $coordinatorReportRepository)
    {
        //
    }

    public function all($perPage = 10, $currentPage = null, $filters = [])
    {
        return $this->coordinatorReportRepository->all($perPage, $currentPage, $filters);
    }

    public function create($data)
    {
        return $this->coordinatorReportRepository->create($data);
    }

    public function update($data)
    {
        return $this->coordinatorReportRepository->update($data);
    }

    public function checkDuplicateReport($data)
    {
        return $this->coordinatorReportRepository->checkDuplicateReport($data);
    }

    public function checkDuplicateReportUpdate($data, $id)
    {
        return $this->coordinatorReportRepository->checkDuplicateReportUpdate($data, $id);
    }

    public function find($id)
    {
        return $this->coordinatorReportRepository->find($id);
    }

}
