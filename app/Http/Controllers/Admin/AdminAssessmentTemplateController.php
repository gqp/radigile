<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\Searchable;
use App\Http\Controllers\Concerns\Sortable;
use App\Http\Controllers\Controller;
use App\Mail\AssessmentTemplatePublishRequestApproved;
use App\Mail\AssessmentTemplatePublishRequestRejected;
use App\Models\AssessmentTemplate;
use App\Models\AssessmentTemplatePublishRequest;
use Illuminate\Support\Facades\Mail;

class AdminAssessmentTemplateController extends Controller
{
    use Sortable, Searchable;

    public function index()
    {
        $query = AssessmentTemplate::with(['team', 'creator'])->withCount('questions');
        $this->applySearch($query, ['title', 'team.name', 'creator.name']);
        $this->applySort($query, ['title', 'created_at'], 'created_at', 'desc');
        $templates = $query->paginate(15)->withQueryString();

        $pendingRequests = AssessmentTemplatePublishRequest::pending()
            ->with(['assessmentTemplate.team', 'requester'])
            ->latest()
            ->get();

        if (request()->ajax()) {
            return view('dashboard.admin.assessment-templates._results', compact('templates'));
        }

        return view('dashboard.admin.assessment-templates.index', compact('templates', 'pendingRequests'));
    }

    public function approveRequest(AssessmentTemplatePublishRequest $assessmentTemplatePublishRequest)
    {
        abort_unless($assessmentTemplatePublishRequest->isPending(), 404);

        $assessmentTemplatePublishRequest->assessmentTemplate->update(['is_public' => true]);

        $assessmentTemplatePublishRequest->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        Mail::to($assessmentTemplatePublishRequest->requester->email)
            ->queue(new AssessmentTemplatePublishRequestApproved($assessmentTemplatePublishRequest));

        return back()->with('success', "\"{$assessmentTemplatePublishRequest->assessmentTemplate->title}\" is now public.");
    }

    public function rejectRequest(AssessmentTemplatePublishRequest $assessmentTemplatePublishRequest)
    {
        abort_unless($assessmentTemplatePublishRequest->isPending(), 404);

        $assessmentTemplatePublishRequest->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        Mail::to($assessmentTemplatePublishRequest->requester->email)
            ->queue(new AssessmentTemplatePublishRequestRejected($assessmentTemplatePublishRequest));

        return back()->with('info', "The request to publish \"{$assessmentTemplatePublishRequest->assessmentTemplate->title}\" was declined.");
    }
}
