<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentEvaluator;
use App\Models\AssessmentQuestion;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\Team;
use App\Models\User;
use App\Services\AiQuestionGenerator;
use App\Services\TeamMaturityService;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function __construct(private TeamMaturityService $teamMaturityService)
    {
    }

    public function index()
    {
        $user = auth()->user();

        $myAssessments = Assessment::where('created_by', $user->id)
            ->with(['team', 'questions', 'responses', 'results'])
            ->latest()
            ->get();

        $teamIds = $user->teamsMemberOf()->pluck('teams.id')
            ->merge($user->teamsOwned()->pluck('id'))
            ->unique();

        $pendingAssessments = Assessment::where('status', 'active')
            ->where(function ($q) use ($user, $teamIds) {
                $q->whereIn('team_id', $teamIds)
                  ->orWhereHas('evaluators', fn($eq) => $eq->where('user_id', $user->id));
            })
            ->where('created_by', '!=', $user->id)
            ->with(['team', 'questions', 'responses', 'evaluators', 'excludedMembers'])
            ->get()
            ->filter(fn($a) => !$a->hasBeenCompletedBy($user))
            ->filter(function ($a) use ($user) {
                $isEvaluator = $a->evaluators->contains('user_id', $user->id);
                return $isEvaluator || !$a->isMemberExcluded($user->id);
            });

        $completedAssessments = Assessment::where('status', 'active')
            ->where(function ($q) use ($user, $teamIds) {
                $q->whereIn('team_id', $teamIds)
                  ->orWhereHas('evaluators', fn($eq) => $eq->where('user_id', $user->id));
            })
            ->where('created_by', '!=', $user->id)
            ->with(['team', 'questions', 'responses'])
            ->get()
            ->filter(fn($a) => $a->hasBeenCompletedBy($user));

        $closedAssessments = Assessment::where('status', 'closed')
            ->where(function ($q) use ($user, $teamIds) {
                $q->whereIn('team_id', $teamIds)
                  ->orWhereHas('evaluators', fn($eq) => $eq->where('user_id', $user->id));
            })
            ->where('created_by', '!=', $user->id)
            ->with(['team'])
            ->get();

        $relevantTeamIds = $myAssessments->pluck('team_id')
            ->merge($pendingAssessments->pluck('team_id'))
            ->merge($completedAssessments->pluck('team_id'))
            ->merge($closedAssessments->pluck('team_id'))
            ->unique();
        $allTeams = Team::with('members')->whereIn('id', $relevantTeamIds)->get();
        $maturityByTeamId = $this->teamMaturityService->build($allTeams)->keyBy(fn ($entry) => $entry['team']->id);

        return view('dashboard.user.assessments.index', compact(
            'myAssessments', 'pendingAssessments', 'completedAssessments', 'closedAssessments', 'maturityByTeamId'
        ));
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->hasPermissionTo('manage-assessments') && !$user->planHasFeature('create-assessments')) {
            return redirect()->route('user.assessments.index')
                ->with('error', 'Your current plan does not include assessment creation. Please upgrade to access this feature.');
        }
        $teams = $user->hasPermissionTo('manage-assessments')
            ? Team::orderBy('name')->get()
            : $user->teamsOwned->merge($user->teamsMemberOf)->unique('id')->sortBy('name')->values();

        return view('dashboard.user.assessments.create', compact('teams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_id'     => 'required|exists:teams,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $user = auth()->user();
        $team = Team::findOrFail($validated['team_id']);
        $isTeamAssociated = $team->owner_id === $user->id
            || $team->members()->where('users.id', $user->id)->exists();

        abort_unless(
            $user->hasPermissionTo('manage-assessments')
            || ($isTeamAssociated && $user->planHasFeature('create-assessments')),
            403
        );

        $assessment = Assessment::create([
            ...$validated,
            'created_by' => auth()->id(),
            'status'     => 'draft',
        ]);

        return redirect()->route('user.assessments.show', $assessment)
            ->with('success', 'Assessment created. Add questions from the library below.');
    }

    public function show(Assessment $assessment)
    {
        $user = auth()->user();
        $isOwner     = $assessment->created_by === $user->id;
        $isTeamOwner = $assessment->team->owner_id === $user->id;
        $isMember    = $isTeamOwner
                       || $user->hasPermissionTo('manage-assessments')
                       || $user->teamsMemberOf()->where('teams.id', $assessment->team_id)->exists();
        $isEvaluator = $assessment->evaluators()->where('user_id', $user->id)->exists();

        abort_unless($isOwner || $isMember || $isEvaluator, 403);

        $isExcluded = !$isTeamOwner && !$isEvaluator && $assessment->isMemberExcluded($user->id);

        if (!$isOwner && !$isExcluded && $assessment->isActive() && !$assessment->hasBeenCompletedBy($user)) {
            return redirect()->route('user.assessments.take', $assessment);
        }

        if (!$isOwner && $assessment->isClosed()) {
            return redirect()->route('user.assessments.results', $assessment);
        }

        $assessment->load(['team.members', 'questions.question.category', 'evaluators.user', 'responses']);

        $maturity = $this->teamMaturityService->build(collect([$assessment->team]))->first();

        $completionStats = collect();
        $evaluatorStats = collect();
        $availableQuestions = collect();
        $potentialEvaluators = collect();
        $excludedMemberIds = collect();
        $teamMembers = collect();
        $categories = collect();

        if ($isOwner) {
            $questionCount = $assessment->questions->count();
            $teamMembers = $assessment->team->members;
            $excludedMemberIds = $assessment->excludedMembers->pluck('user_id');

            $completionStats = $teamMembers->map(function ($member) use ($assessment, $questionCount, $excludedMemberIds) {
                $responseCount = $assessment->responses->where('user_id', $member->id)->count();
                return [
                    'user'      => $member,
                    'excluded'  => $excludedMemberIds->contains($member->id),
                    'completed' => $questionCount > 0 && $responseCount >= $questionCount,
                    'responses' => $responseCount,
                    'total'     => $questionCount,
                ];
            });

            $evaluatorStats = $assessment->evaluators->map(function ($evaluator) use ($assessment, $questionCount) {
                $responseCount = $assessment->responses->where('user_id', $evaluator->user_id)->count();
                return [
                    'user'      => $evaluator->user,
                    'status'    => $evaluator->status,
                    'completed' => $questionCount > 0 && $responseCount >= $questionCount,
                    'responses' => $responseCount,
                    'total'     => $questionCount,
                ];
            });

            if ($assessment->isDraft()) {
                $addedIds = $assessment->questions->pluck('question_id')->toArray();
                $availableQuestions = Question::with(['category', 'tags'])
                    ->active()
                    ->whereNotIn('id', $addedIds)
                    ->get()
                    ->groupBy('category.name');

                $categories = QuestionCategory::orderBy('name')->get();

                $excludeIds = array_merge(
                    [$user->id],
                    $assessment->evaluators->pluck('user_id')->toArray(),
                    $teamMembers->pluck('id')->toArray()
                );
                $potentialEvaluators = User::whereNotIn('id', $excludeIds)->orderBy('name')->get();
            }

            if ($assessment->isActive()) {
                $excludeIds = array_merge(
                    [$user->id],
                    $assessment->evaluators->pluck('user_id')->toArray(),
                    $teamMembers->pluck('id')->toArray()
                );
                $potentialEvaluators = User::whereNotIn('id', $excludeIds)->orderBy('name')->get();
            }
        }

        return view('dashboard.user.assessments.show', compact(
            'assessment', 'isOwner', 'isMember', 'isEvaluator',
            'completionStats', 'evaluatorStats', 'availableQuestions', 'potentialEvaluators',
            'teamMembers', 'excludedMemberIds', 'categories', 'maturity'
        ));
    }

    public function addQuestion(Request $request, Assessment $assessment)
    {
        $this->authorizeOwner($assessment);
        abort_unless($assessment->isDraft(), 403, 'Cannot modify a published assessment.');

        $request->validate([
            'question_ids'   => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id,is_active,1',
        ]);

        $order = $assessment->questions()->max('order') ?? 0;
        $added = collect();

        foreach ($request->question_ids as $questionId) {
            $order++;
            $aq = AssessmentQuestion::firstOrCreate(
                ['assessment_id' => $assessment->id, 'question_id' => $questionId],
                ['order' => $order]
            );
            $added->push($aq->load('question.category'));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'added' => $added->map(fn ($aq) => [
                    'question_id' => $aq->question_id,
                    'text'        => $aq->question->text,
                    'category'    => $aq->question->category->name ?? '—',
                ]),
                'total' => $assessment->questions()->count(),
            ]);
        }

        $count = count($request->question_ids);
        return back()->with('success', $count . ' question' . ($count === 1 ? '' : 's') . ' added.');
    }

    public function generateQuestion(Request $request, AiQuestionGenerator $generator, Assessment $assessment)
    {
        $this->authorizeAiGeneration($assessment);

        $request->validate([
            'description' => 'required|string|min:10|max:500',
            'category'    => 'nullable|string|max:100',
        ]);

        $result = $generator->generate($request->description, $request->category);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }

    public function createAndAddQuestion(Request $request, Assessment $assessment)
    {
        $this->authorizeAiGeneration($assessment);

        $validated = $request->validate([
            'text'        => 'required|string|max:255',
            'category_id' => 'required|exists:question_categories,id',
            'tip_0'       => 'nullable|string',
            'tip_1'       => 'nullable|string',
            'tip_2'       => 'nullable|string',
            'tip_3'       => 'nullable|string',
            'tip_4'       => 'nullable|string',
        ]);

        $question = Question::create([...$validated, 'is_active' => true]);

        $order = $assessment->questions()->max('order') ?? 0;
        AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_id'   => $question->id,
            'order'         => $order + 1,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'added' => [[
                    'question_id' => $question->id,
                    'text'        => $question->text,
                    'category'    => $question->category->name ?? '—',
                ]],
                'total' => $assessment->questions()->count(),
            ]);
        }

        return back()->with('success', 'Question created and added.');
    }

    public function removeQuestion(Assessment $assessment, int $questionId)
    {
        $this->authorizeOwner($assessment);
        abort_unless($assessment->isDraft(), 403, 'Cannot modify a published assessment.');

        $assessment->questions()->where('question_id', $questionId)->delete();

        return back()->with('success', 'Question removed.');
    }

    public function inviteEvaluator(Request $request, Assessment $assessment)
    {
        $this->authorizeOwner($assessment);
        abort_unless(!$assessment->isClosed(), 403);

        $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->user_ids as $userId) {
            AssessmentEvaluator::firstOrCreate(
                ['assessment_id' => $assessment->id, 'user_id' => $userId],
                ['invited_by' => auth()->id(), 'status' => 'pending']
            );
        }

        $count = count($request->user_ids);
        return back()->with('success', $count . ' evaluator' . ($count === 1 ? '' : 's') . ' invited.');
    }

    public function updateParticipants(Request $request, Assessment $assessment)
    {
        $this->authorizeOwner($assessment);
        abort_unless(!$assessment->isClosed(), 403);

        $request->validate([
            'included_ids'   => 'nullable|array',
            'included_ids.*' => 'integer|exists:users,id',
        ]);

        $includedIds = collect($request->input('included_ids', []))->map(fn ($id) => (int) $id);
        $teamMemberIds = $assessment->team->members->pluck('id');
        // The assessment creator can never exclude themselves, even accidentally via "toggle all".
        $excludedIds = $teamMemberIds->diff($includedIds)->reject(fn ($id) => $id === $assessment->created_by);

        $assessment->excludedMembers()->delete();
        foreach ($excludedIds as $userId) {
            $assessment->excludedMembers()->create(['user_id' => $userId]);
        }

        return back()->with('success', 'Team member participation updated.');
    }

    public function removeEvaluator(Assessment $assessment, User $evaluatorUser)
    {
        $this->authorizeOwner($assessment);
        abort_unless(!$assessment->isClosed(), 403);

        $assessment->evaluators()->where('user_id', $evaluatorUser->id)->delete();

        return back()->with('success', 'Evaluator removed.');
    }

    public function publish(Assessment $assessment)
    {
        $this->authorizeOwner($assessment);
        abort_unless($assessment->isDraft(), 403, 'Assessment is already published.');
        abort_unless($assessment->questions()->count() > 0, 422, 'Add at least one question before publishing.');

        $assessment->update(['status' => 'active']);

        return back()->with('success', 'Assessment published. Team members can now take it.');
    }

    public function close(Assessment $assessment)
    {
        $this->authorizeOwner($assessment);
        abort_unless($assessment->isActive(), 403, 'Assessment is not active.');

        $assessment->update(['status' => 'closed']);

        return redirect()->route('user.assessments.results', $assessment)
            ->with('success', 'Assessment closed. Results are now available.');
    }

    public function destroy(Assessment $assessment)
    {
        $this->authorizeOwner($assessment);
        $assessment->delete();

        return redirect()->route('user.assessments.index')->with('success', 'Assessment deleted.');
    }

    private function authorizeOwner(Assessment $assessment): void
    {
        $user = auth()->user();
        abort_unless($user->hasPermissionTo('manage-assessments') || $assessment->created_by === $user->id, 403);
    }

    private function authorizeAiGeneration(Assessment $assessment): void
    {
        $this->authorizeOwner($assessment);
        abort_unless($assessment->isDraft(), 403, 'Cannot modify a published assessment.');
        abort_unless(auth()->user()->planHasFeature('ai-question-generation'), 403, 'Your plan does not include AI question generation.');
    }
}
