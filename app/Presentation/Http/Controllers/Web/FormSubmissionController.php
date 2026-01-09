<?php

namespace App\Presentation\Http\Controllers\Web;

use App\Domain\FormSubmission\Events\FormSubmitted;
use App\Domain\FormSubmission\Services\FormSubmissionService;
use App\Presentation\Http\Requests\FormSubmission\StoreFormSubmissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class FormSubmissionController extends Controller
{
    public function __construct(
        private readonly FormSubmissionService $formSubmissionService
    ) {
    }

    /**
     * Store a new form submission.
     */
    public function store(StoreFormSubmissionRequest $request): RedirectResponse
    {
        $submission = $this->formSubmissionService->createSubmission(
            $request->validated()
        );

        event(new FormSubmitted($submission));

        return back()->with('success', 'Thank you for your submission! We will get back to you soon.');
    }
}
