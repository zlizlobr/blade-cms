<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\Dashboard\Services\DashboardServiceInterface;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardServiceInterface $dashboardService
    ) {}

    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $tenantId = auth()->user()->current_tenant_id;

        $statistics = $this->dashboardService->getStatistics($tenantId);

        return view('admin.dashboard', $statistics);
    }
}
