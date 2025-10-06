<?php

namespace App\Livewire\DMS\RevenueReporting;

use App\Services\RevenueReportService;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Revenue Reporting List')]
class RevenueReportingList extends Component
{
    public string $main_menu = 'Revenue Report';
    public string $menu = 'Revenue Report List';

    protected RevenueReportService $revenueReportService;

    public function boot(RevenueReportService $revenueReportService)
    {
        $this->revenueReportService = $revenueReportService;
    }

    public function render()
    {
        if(auth()->user()->role_id!=1){
            $branch_id = auth()->user()->branch_id; // get user branch
        }else {
            $branch_id = null;
        }
        

        return view('livewire.dms.revenue-reporting.revenue-reporting-list', [
            'main_menu' => $this->main_menu,
            'menu' => $this->menu,
            'revenueReports' => $this->revenueReportService->all($branch_id),
            'businesses' => $this->revenueReportService->getBusinesses($branch_id),
            'revenueStats' => $this->revenueReportService->getRevenueStats($branch_id),
        ]);
    }
}
