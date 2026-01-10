<?php

namespace App\Presentation\Http\Controllers\Web;

use App\Domain\FormSubmission\Events\FormSubmitted;
use App\Domain\FormSubmission\Services\FormSubmissionService;
use App\Presentation\Http\Requests\FormSubmission\StoreFormSubmissionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class FormSubmissionController extends Controller
{
    public function __construct(
        private readonly FormSubmissionService $formSubmissionService
    ) {}

    /**
     * Store a new form submission.
     */
    public function store(StoreFormSubmissionRequest $request): JsonResponse|RedirectResponse
    {
        $submission = $this->formSubmissionService->createSubmission(
            $request->validated()
        );

        event(new FormSubmitted($submission));

        // Return JSON for AJAX requests
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your submission! We will get back to you soon.',
                'submission' => [
                    'id' => $submission->id,
                    'created_at' => $submission->created_at->toISOString(),
                ],
            ], 201);
        }

        // Return redirect for traditional form submission
        return back()->with('success', 'Thank you for your submission! We will get back to you soon.');
    }
}
