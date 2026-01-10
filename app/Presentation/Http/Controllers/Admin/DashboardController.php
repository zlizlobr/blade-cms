<?php

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\FormSubmission\Models\FormSubmission;
use App\Domain\User\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $tenantId = auth()->user()->current_tenant_id;

        // Total submissions count
        $totalSubmissions = FormSubmission::where('tenant_id', $tenantId)->count();

        // Submissions this week
        $submissionsThisWeek = FormSubmission::where('tenant_id', $tenantId)
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();

        // Active users count (users who belong to this tenant)
        $activeUsers = User::whereHas('tenants', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })->count();

        // Recent submissions (last 5)
        $recentSubmissions = FormSubmission::where('tenant_id', $tenantId)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'totalSubmissions' => $totalSubmissions,
            'submissionsThisWeek' => $submissionsThisWeek,
            'activeUsers' => $activeUsers,
            'recentSubmissions' => $recentSubmissions,
        ]);
    }
}
