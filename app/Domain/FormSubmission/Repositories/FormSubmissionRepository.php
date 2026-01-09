<?php

namespace App\Domain\FormSubmission\Repositories;

use App\Domain\FormSubmission\Models\FormSubmission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FormSubmissionRepository implements FormSubmissionRepositoryInterface
{
    /**
     * Create a new form submission.
     *
     * @param array $data
     * @return FormSubmission
     */
    public function create(array $data): FormSubmission
    {
        return FormSubmission::create($data);
    }

    /**
     * Find form submissions by tenant ID.
     *
     * @param int $tenantId
     * @return Collection
     */
    public function findByTenant(int $tenantId): Collection
    {
        return FormSubmission::where('tenant_id', $tenantId)->get();
    }

    /**
     * Find a form submission by ID.
     *
     * @param int $id
     * @return FormSubmission|null
     */
    public function findById(int $id): ?FormSubmission
    {
        return FormSubmission::find($id);
    }

    /**
     * Get a query builder instance.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return FormSubmission::query();
    }

    /**
     * Filter form submissions by form type.
     *
     * @param string $type
     * @return Builder
     */
    public function filterByFormType(string $type): Builder
    {
        return $this->query()->where('form_type', $type);
    }

    /**
     * Eager load user relationship.
     *
     * @return Builder
     */
    public function withUser(): Builder
    {
        return $this->query()->with('user');
    }

    /**
     * Get paginated form submissions.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()->paginate($perPage);
    }
}
