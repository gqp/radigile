<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentResponse;
use App\Models\AssessmentResult;
use Illuminate\Http\Request;

class AssessmentResponseController extends Controller
{
    public function show(Assessment $assessment)
    {
        $user = auth()->user();

        [$isMember, $isEvaluator] = $this->resolveAccess($assessment, $user);
        abort_unless($isMember || $isEvaluator, 403);
        abort_unless($assessment->isActive(), 403, 'This assessment is not currently active.');

        if ($assessment->hasBeenCompletedBy($user)) {
            return redirect()->route('user.assessments.show', $assessment)
                ->with('info', 'You have already submitted your responses.');
        }

        $assessment->load(['questions.question.category', 'team']);

        $existingResponses = $assessment->responses()
            ->where('user_id', $user->id)
            ->pluck('score', 'question_id');

        $respondentType = $isEvaluator && !$isMember ? 'external' : 'member';

        return view('dashboard.user.assessments.take', compact(
            'assessment', 'existingResponses', 'respondentType'
        ));
    }

    public function store(Request $request, Assessment $assessment)
    {
        $user = auth()->user();

        [$isMember, $isEvaluator] = $this->resolveAccess($assessment, $user);
        abort_unless($isMember || $isEvaluator, 403);
        abort_unless($assessment->isActive(), 403);

        $questionIds = $assessment->questions()->pluck('question_id')->toArray();

        $request->validate([
            'responses'   => 'required|array',
            'responses.*' => 'required|integer|min:0|max:4',
        ]);

        $respondentType = $isEvaluator && !$isMember ? 'external' : 'member';

        foreach ($request->responses as $questionId => $score) {
            if (!in_array((int) $questionId, $questionIds)) continue;

            AssessmentResponse::updateOrCreate(
                ['assessment_id' => $assessment->id, 'user_id' => $user->id, 'question_id' => $questionId],
                ['score' => $score, 'comment' => $request->comments[$questionId] ?? null, 'respondent_type' => $respondentType]
            );
        }

        $avgScore = AssessmentResponse::where('assessment_id', $assessment->id)
            ->where('user_id', $user->id)
            ->avg('score');

        AssessmentResult::updateOrCreate(
            ['assessment_id' => $assessment->id, 'user_id' => $user->id],
            ['overall_score' => round($avgScore, 2), 'respondent_type' => $respondentType]
        );

        if ($isEvaluator) {
            $assessment->evaluators()->where('user_id', $user->id)->update(['status' => 'accepted']);
        }

        return redirect()->route('user.assessments.show', $assessment)
            ->with('success', 'Your responses have been submitted. Thank you!');
    }

    private function resolveAccess(Assessment $assessment, $user): array
    {
        $isOwner   = $assessment->team->owner_id === $user->id;
        $isCreator = $assessment->created_by === $user->id;
        $canManage = $user->hasPermissionTo('manage-assessments');
        $isTeamMember = $user->teamsMemberOf()->where('teams.id', $assessment->team_id)->exists();

        // The team owner, the assessment's creator, and admins are never excluded
        // from their own assessment; a plain team member can be, via the creator's
        // participant toggle.
        $isExcluded = $isTeamMember && !$isOwner && !$isCreator && !$canManage && $assessment->isMemberExcluded($user->id);

        $isMember    = $isOwner || $isCreator || $canManage || ($isTeamMember && !$isExcluded);
        $isEvaluator = $assessment->evaluators()->where('user_id', $user->id)->exists();
        return [$isMember, $isEvaluator];
    }
}
