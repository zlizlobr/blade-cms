<?php

declare(strict_types=1);

namespace App\Domain\FormSubmission\Services;

use App\Domain\FormSubmission\Models\FormSubmission;

interface FormSubmissionServiceInterface
{
    /**
     * Create a new form submission.
     * Finds or creates a user based on the email provided.
     */
    public function createSubmission(array $data): FormSubmission;
}
