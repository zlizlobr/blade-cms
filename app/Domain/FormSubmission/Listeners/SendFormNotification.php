<?php

namespace App\Domain\FormSubmission\Listeners;

use App\Domain\FormSubmission\Events\FormSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendFormNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(FormSubmitted $event): void
    {
        $submission = $event->submission;

        // Log the submission for tracking
        Log::info('New form submission received', [
            'id' => $submission->id,
            'form_type' => $submission->form_type,
            'user_id' => $submission->user_id,
            'tenant_id' => $submission->tenant_id,
            'email' => $submission->data['email'] ?? null,
            'name' => $submission->data['name'] ?? null,
        ]);

        // TODO: Add email notification to admin in future
        // Example: Mail::to(config('mail.admin_email'))->send(new FormSubmittedMail($submission));

        // TODO: Add real-time notification via broadcasting in future
        // Example: broadcast(new FormSubmissionReceived($submission))->toOthers();
    }
}
