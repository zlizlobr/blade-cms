<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Services;

use App\Domain\FormSubmission\Models\FormSubmission;
use App\Domain\User\Models\User;
use Illuminate\Support\Collection;

class DashboardService implements DashboardServiceInterface
{
    /**
     * Create service instance.
     * Factory method for convenient instantiation outside Laravel container.
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Get dashboard statistics for a tenant.
     *
     * @return array{totalSubmissions: int, submissionsThisWeek: int, activeUsers: int, recentSubmissions: Collection}
     */
    public function getStatistics(int $tenantId): array
    {
        return [
            'totalSubmissions' => $this->getTotalSubmissions($tenantId),
            'submissionsThisWeek' => $this->getSubmissionsThisWeek($tenantId),
            'activeUsers' => $this->getActiveUsers($tenantId),
            'recentSubmissions' => $this->getRecentSubmissions($tenantId),
        ];
    }

    /**
     * Get total submissions count for a tenant.
     */
    private function getTotalSubmissions(int $tenantId): int
    {
        return FormSubmission::where('tenant_id', $tenantId)->count();
    }

    /**
     * Get submissions count for this week (from Monday).
     */
    private function getSubmissionsThisWeek(int $tenantId): int
    {
        return FormSubmission::where('tenant_id', $tenantId)
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();
    }

    /**
     * Get active users count (users who belong to this tenant).
     */
    private function getActiveUsers(int $tenantId): int
    {
        return User::whereHas('tenants', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })->count();
    }

    /**
     * Get recent submissions (last 5) with user relation.
     */
    private function getRecentSubmissions(int $tenantId): Collection
    {
        return FormSubmission::where('tenant_id', $tenantId)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();
    }
}
