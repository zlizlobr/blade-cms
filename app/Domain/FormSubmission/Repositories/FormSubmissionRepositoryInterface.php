<?php

namespace App\Domain\FormSubmission\Repositories;

use App\Domain\FormSubmission\Models\FormSubmission;
use Illuminate\Database\Eloquent\Collection;

interface FormSubmissionRepositoryInterface
{
    /**
     * Create a new form submission.
     *
     * @param array $data
     * @return FormSubmission
     */
    public function create(array $data): FormSubmission;

    /**
     * Find form submissions by tenant ID.
     *
     * @param int $tenantId
     * @return Collection
     */
    public function findByTenant(int $tenantId): Collection;

    /**
     * Find a form submission by ID.
     *
     * @param int $id
     * @return FormSubmission|null
     */
    public function findById(int $id): ?FormSubmission;
}
