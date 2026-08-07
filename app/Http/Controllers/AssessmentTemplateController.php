<?php

namespace App\Http\Controllers;

use App\Mail\AssessmentTemplatePublishRequestSubmitted;
use App\Models\AssessmentTemplate;
use App\Models\AssessmentTemplatePublishRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AssessmentTemplateController extends Controller
{
    /**
     * Team owner/permitted member requests their team-scoped template be
     * made public. Mirrors UserTeamJoinRequestController::store().
     */
    public function requestPublic(AssessmentTemplate $template)
    {
        $user = auth()->user();
        abort_unless($template->team_id && $template->team->canManageAssessments($user), 403);
        abort_if($template->is_public, 422, 'This template is already public.');

        if ($template->publishRequests()->pending()->exists()) {
            return back()->with('error', 'A publish request for this template is already pending.');
        }

        request()->validate(['message' => 'nullable|string|max:1000']);

        $publishRequest = AssessmentTemplatePublishRequest::create([
            'assessment_template_id' => $template->id,
            'requested_by'           => $user->id,
            'status'                 => 'pending',
            'message'                => request('message'),
        ]);

        foreach (User::permission('access-admin-panel')->get() as $admin) {
            Mail::to($admin->email)->queue(new AssessmentTemplatePublishRequestSubmitted($publishRequest, $admin->name));
        }

        return back()->with('success', 'Your request to publish this template has been sent to the admin team.');
    }

    /**
     * Delete a template — owner (team-manager) or admin only.
     */
    public function destroy(AssessmentTemplate $template)
    {
        $user = auth()->user();
        $isAdmin = $user->hasPermissionTo('manage-assessments');
        abort_unless($isAdmin || ($template->team_id && $template->team->canManageAssessments($user)), 403);

        $template->delete();

        return back()->with('success', 'Template deleted.');
    }
}
