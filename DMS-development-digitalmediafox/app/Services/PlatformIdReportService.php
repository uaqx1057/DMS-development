<?php

namespace App\Services;

use App\Repositories\PlatformIdReportRepository;
use Illuminate\Support\Facades\{DB, Log};

class PlatformIdReportService
{
    public function __construct(protected PlatformIdReportRepository $platformIdReportRepository)
    {
        //
    }

    public function all($perPage = 10, $currentPage = null, $filters = [])
    {
        return $this->platformIdReportRepository->all($perPage, $currentPage, $filters);
    }

    public function create($data)
    {
        return $this->platformIdReportRepository->create($data);
    }

    public function update($data)
    {
        return $this->platformIdReportRepository->update($data);
    }

    public function checkDuplicateReport($data)
    {
        return $this->platformIdReportRepository->checkDuplicateReport($data);
    }

    public function checkDuplicateReportUpdate($data, $id)
    {
        return $this->platformIdReportRepository->checkDuplicateReportUpdate($data, $id);
    }

    public function find($id)
    {
        return $this->platformIdReportRepository->find($id);
    }

    // New method to get all reports by business_id_value
    public function getReportsByBusinessIdValue($businessIdValue)
    {
        return $this->platformIdReportRepository->getReportsByBusinessIdValue($businessIdValue);
    }
}