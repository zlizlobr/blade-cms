<?php

namespace App\Domain\FormSubmission\Policies;

use App\Domain\FormSubmission\Models\FormSubmission;
use App\Domain\User\Models\User;

class FormSubmissionPolicy
{
    /**
     * Determine whether the user can view any form submissions.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the form submission.
     */
    public function view(User $user, FormSubmission $submission): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create form submissions.
     * Anyone can submit forms, including unauthenticated users.
     */
    public function create(?User $user): bool
    {
        return true; // Anyone can submit forms
    }
}
