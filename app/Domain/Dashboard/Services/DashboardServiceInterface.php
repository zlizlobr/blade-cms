<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Services;

interface DashboardServiceInterface
{
    /**
     * Get dashboard statistics for a tenant.
     *
     * @return array{totalSubmissions: int, submissionsThisWeek: int, activeUsers: int, recentSubmissions: \Illuminate\Support\Collection}
     */
    public function getStatistics(int $tenantId): array;
}
