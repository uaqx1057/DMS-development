<?php

namespace App\Livewire;

use App\Enums\DriverStatus;
use App\Enums\OrderStatus;
use App\Models\Branch;
use App\Models\Business;
use App\Models\Driver;
use App\Models\Order;
use App\Services\{BusinessService, DriverService};
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    protected DriverService $driverService;
    protected BusinessService $branchService;

    public int $activeDrivers = 0;
    public int $inactiveDrivers = 0;
    public int $busyDrivers = 0;
    public ?string $date_range = null;

    public function mount(DriverService $driverService, BusinessService $branchService)
    {
        $this->driverService = $driverService;
    }

    public function boot(DriverService $driverService, BusinessService $branchService)
    {
        $this->driverService = $driverService;
    }

    public function updatedBusinessId()
    {
        // Reload counts or other logic when business_id changes
        $this->loadDriverCounts();
    }

    public function getBranchWithFilteredDrivers(array $filters = [])
    {
        $branches = Branch::with(['drivers' => function ($query) use ($filters) {
            if (!empty($filters['statuses'])) {
                $query->whereIn('status', $filters['statuses']);
            }

            if (!empty($filters['date_range'])) {
                $dates = explode(' to ', $filters['date_range']);
                $query->whereBetween('updated_at', [
                    $dates[0],
                    $dates[1] ?? $dates[0],
                ]);
            } else {
                $query->whereDate('updated_at', now()->format('Y-m-d'));
            }
        }])->get()->map(function ($branch) {
            $statuses = $branch->drivers->groupBy('status')->map->count();

            return [
                'branch_name' => $branch->name,
                'total_drivers' => $branch->drivers->count(),
                'active' => $statuses[DriverStatus::Active->value] ?? 0,
                'inactive' => $statuses[DriverStatus::Inactive->value] ?? 0,
                'busy' => $statuses[DriverStatus::Busy->value] ?? 0,
            ];
        });

        return $branches;
    }

    public function getOrderStatistics()
    {
        $orderQuery = Order::query();

        $statuses = OrderStatus::cases();
        $statusCounts = [];

        foreach ($statuses as $status) {
            $statusCounts[$status->value] = [
                'today' => $orderQuery->where('status', $status->value)
                    ->whereDate('created_at', Carbon::today())->count(),
                'week' => $orderQuery->where('status', $status->value)
                    ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
                'month' => $orderQuery->where('status', $status->value)
                    ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count(),
                'total' => $orderQuery->where('status', $status->value)->count(),
            ];
        }

        $totals = [
            'today' => $orderQuery->whereDate('created_at', Carbon::today())->count(),
            'week' => $orderQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'month' => $orderQuery->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count(),
            'total' => $orderQuery->count(),
        ];

        return compact('statusCounts', 'totals');
    }

    public function render()
    {
        $filters = [
            'date_range' => $this->date_range,
        ];


        $activeDrivers = $this->driverService->getCount(DriverStatus::Active, $filters);
        $inactiveDrivers = $this->driverService->getCount(DriverStatus::Inactive, $filters);
        $busyDrivers = $this->driverService->getCount(DriverStatus::Busy, $filters);

        $branches = $this->getBranchWithFilteredDrivers($filters);
        $orders = $this->getOrderStatistics();

        return view('livewire.dashboard.index', compact('branches', 'activeDrivers', 'inactiveDrivers', 'busyDrivers'));
    }
}
