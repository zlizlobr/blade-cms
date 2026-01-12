<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\FormSubmission\Models\FormSubmission;
use App\Domain\FormSubmission\Services\SubmissionQueryServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function __construct(
        private readonly SubmissionQueryServiceInterface $queryService
    ) {}

    /**
     * Display a listing of form submissions.
     */
    public function index(Request $request): View
    {
        $tenantId = auth()->user()->current_tenant_id;

        $filters = $request->only(['search', 'form_type']);

        $submissions = $this->queryService->getPaginatedSubmissions($tenantId, $filters);

        return view('admin::submissions.index', [
            'submissions' => $submissions,
        ]);
    }

    /**
     * Display the specified form submission.
     */
    public function show(FormSubmission $submission): View
    {
        $tenantId = auth()->user()->current_tenant_id;

        // Authorization check - ensure submission belongs to current tenant
        if (! $this->queryService->belongsToTenant($submission, $tenantId)) {
            abort(403, 'Unauthorized access to submission');
        }

        return view('admin::submissions.show', [
            'submission' => $submission,
        ]);
    }
}
