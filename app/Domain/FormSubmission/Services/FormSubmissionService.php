<?php

declare(strict_types=1);

namespace App\Domain\FormSubmission\Services;

use App\Domain\FormSubmission\Models\FormSubmission;
use App\Domain\FormSubmission\Repositories\FormSubmissionRepository;
use App\Domain\FormSubmission\Repositories\FormSubmissionRepositoryInterface;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FormSubmissionService implements FormSubmissionServiceInterface
{
    public function __construct(
        private readonly FormSubmissionRepositoryInterface $repository
    ) {}

    /**
     * Create service instance with default dependencies.
     * Factory method for convenient instantiation outside Laravel container.
     */
    public static function create(): self
    {
        return new self(new FormSubmissionRepository);
    }

    /**
     * Create a new form submission.
     * Finds or creates a user based on the email provided.
     */
    public function createSubmission(array $data): FormSubmission
    {
        $email = $data['email'];
        $name = $data['name'];

        // Find or create user based on email
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(Str::random(32)), // Random password for auto-created users
                'email_verified_at' => null, // User hasn't verified email yet
            ]
        );

        // Prepare submission data
        $submissionData = [
            'form_type' => $data['form_type'] ?? 'contact',
            'data' => [
                'name' => $name,
                'email' => $email,
                'message' => $data['message'],
            ],
            'user_id' => $user->id,
            // tenant_id will be automatically set by BelongsToTenant trait
        ];

        return $this->repository->create($submissionData);
    }
}
