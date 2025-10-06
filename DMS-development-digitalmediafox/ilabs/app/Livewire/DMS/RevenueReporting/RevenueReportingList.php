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

    protected $revenueReportService;

    public function boot(RevenueReportService $revenueReportService)
    {
        $this->revenueReportService = $revenueReportService;
    }

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $revenueReports = $this->revenueReportService->all();
        $businesses = $this->revenueReportService->getBusinesses();
        $revenueStats = $this->revenueReportService->getRevenueStats();
        return view('livewire.dms.revenue-reporting.revenue-reporting-list', compact('main_menu', 'menu', 'revenueReports', 'businesses', 'revenueStats'));
    }
}
