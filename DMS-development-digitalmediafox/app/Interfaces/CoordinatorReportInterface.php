<?php

namespace App\Interfaces;

interface CoordinatorReportInterface
{
    public function all($perPage, $currentPage, $filters = []);
    public function create(array $data);
    public function update(array $data);
    public function checkDuplicateReport(array $data);
    public function checkDuplicateReportUpdate(array $data, $id);
}
