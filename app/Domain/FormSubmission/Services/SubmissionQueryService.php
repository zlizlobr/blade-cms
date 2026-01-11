<?php

declare(strict_types=1);

namespace App\Domain\FormSubmission\Services;

use App\Domain\FormSubmission\Models\FormSubmission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class SubmissionQueryService
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
     * Get paginated submissions for a tenant with optional filters.
     *
     * @param  array{search?: string, form_type?: string}  $filters
     */
    public function getPaginatedSubmissions(
        int $tenantId,
        array $filters = [],
        int $perPage = 20
    ): LengthAwarePaginator {
        $query = $this->buildQuery($tenantId, $filters);

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    /**
     * Build query with filters.
     */
    private function buildQuery(int $tenantId, array $filters): Builder
    {
        $query = FormSubmission::where('tenant_id', $tenantId)->with('user');

        // Apply search filter
        if (! empty($filters['search'])) {
            $this->applySearchFilter($query, $filters['search']);
        }

        // Apply form type filter
        if (! empty($filters['form_type'])) {
            $query->where('form_type', $filters['form_type']);
        }

        return $query;
    }

    /**
     * Apply fulltext search filter on JSON fields.
     */
    private function applySearchFilter(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('data->name', 'like', "%{$search}%")
                ->orWhere('data->email', 'like', "%{$search}%")
                ->orWhere('data->message', 'like', "%{$search}%");
        });
    }

    /**
     * Check if submission belongs to tenant.
     */
    public function belongsToTenant(FormSubmission $submission, int $tenantId): bool
    {
        return $submission->tenant_id === $tenantId;
    }
}
