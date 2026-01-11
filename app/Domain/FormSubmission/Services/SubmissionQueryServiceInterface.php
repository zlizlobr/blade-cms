<?php

declare(strict_types=1);

namespace App\Domain\FormSubmission\Services;

use App\Domain\FormSubmission\Models\FormSubmission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SubmissionQueryServiceInterface
{
    /**
     * Get paginated submissions for a tenant with optional filters.
     *
     * @param  array{search?: string, form_type?: string}  $filters
     */
    public function getPaginatedSubmissions(
        int $tenantId,
        array $filters = [],
        int $perPage = 20
    ): LengthAwarePaginator;

    /**
     * Check if submission belongs to tenant.
     */
    public function belongsToTenant(FormSubmission $submission, int $tenantId): bool;
}
