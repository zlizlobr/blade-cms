<?php

declare(strict_types=1);

namespace App\Domain\FormSubmission\Repositories;

use App\Domain\FormSubmission\Models\FormSubmission;
use Illuminate\Database\Eloquent\Collection;

interface FormSubmissionRepositoryInterface
{
    /**
     * Create a new form submission.
     */
    public function create(array $data): FormSubmission;

    /**
     * Find form submissions by tenant ID.
     */
    public function findByTenant(int $tenantId): Collection;

    /**
     * Find a form submission by ID.
     */
    public function findById(int $id): ?FormSubmission;
}
