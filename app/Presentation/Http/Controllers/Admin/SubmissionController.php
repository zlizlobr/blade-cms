<?php

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\FormSubmission\Models\FormSubmission;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    /**
     * Display a listing of form submissions.
     */
    public function index(): View
    {
        $tenantId = auth()->user()->current_tenant_id;

        // Build query
        $query = FormSubmission::where('tenant_id', $tenantId)->with('user');

        // Filter by search term
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('data->name', 'like', "%{$search}%")
                  ->orWhere('data->email', 'like', "%{$search}%")
                  ->orWhere('data->message', 'like', "%{$search}%");
            });
        }

        // Filter by form type
        if (request('form_type')) {
            $query->where('form_type', request('form_type'));
        }

        // Get submissions with pagination
        $submissions = $query->latest()->paginate(20)->withQueryString();

        return view('admin.submissions.index', [
            'submissions' => $submissions,
        ]);
    }

    /**
     * Display the specified form submission.
     */
    public function show(FormSubmission $submission): View
    {
        // Authorization check - ensure submission belongs to current tenant
        if ($submission->tenant_id !== auth()->user()->current_tenant_id) {
            abort(403, 'Unauthorized access to submission');
        }

        return view('admin.submissions.show', [
            'submission' => $submission,
        ]);
    }
}
