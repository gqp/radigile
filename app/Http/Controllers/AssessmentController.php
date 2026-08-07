<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Searchable;
use App\Http\Controllers\Concerns\Sortable;
use App\Models\Assessment;
use App\Models\AssessmentEvaluator;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentTemplate;
use App\Models\AssessmentTemplateQuestion;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\Team;
use App\Models\User;
use App\Jobs\GenerateAssessmentQuestionBatch;
use App\Services\AiQuestionGenerator;
use App\Services\AssessmentContextBuilder;
use App\Services\TeamMaturityService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AssessmentController extends Controller
{
    use Sortable, Searchable;

    public function __construct(private TeamMaturityService $teamMaturityService)
    {
    }

    public function index()
    {
        $user = auth()->user();

        $myAssessmentsQuery = Assessment::where('created_by', $user->id)
            ->with(['team', 'questions', 'responses', 'results']);
        $this->applySearch($myAssessmentsQuery, ['title', 'team.name'], 'my_search');
        $this->applySort($myAssessmentsQuery, ['title', 'status', 'created_at'], 'created_at', 'desc', 'my_sort', 'my_dir');
        $myAssessments = $myAssessmentsQuery->paginate(15, ['*'], 'my_page')->withQueryString();

        $teamIds = $user->teamsMemberOf()->pluck('teams.id')
            ->merge($user->teamsOwned()->pluck('id'))
            ->unique();

        // Pending/Completed can only be told apart after loading each user's
        // response state (hasBeenCompletedBy / isMemberExcluded aren't plain
        // DB columns), so those two are filtered in PHP, then paginated
        // manually rather than via ->paginate() on the query.
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
        $pendingAssessments = $this->paginateCollection($pendingAssessments, 'title', 'pending');

        $completedAssessments = Assessment::where('status', 'active')
            ->where(function ($q) use ($user, $teamIds) {
                $q->whereIn('team_id', $teamIds)
                  ->orWhereHas('evaluators', fn($eq) => $eq->where('user_id', $user->id));
            })
            ->where('created_by', '!=', $user->id)
            ->with(['team', 'questions', 'responses'])
            ->get()
            ->filter(fn($a) => $a->hasBeenCompletedBy($user));
        $completedAssessments = $this->paginateCollection($completedAssessments, 'title', 'completed');

        $closedAssessmentsQuery = Assessment::where('status', 'closed')
            ->where(function ($q) use ($user, $teamIds) {
                $q->whereIn('team_id', $teamIds)
                  ->orWhereHas('evaluators', fn($eq) => $eq->where('user_id', $user->id));
            })
            ->where('created_by', '!=', $user->id)
            ->with(['team']);
        $this->applySearch($closedAssessmentsQuery, ['title', 'team.name'], 'closed_search');
        $this->applySort($closedAssessmentsQuery, ['title', 'created_at'], 'created_at', 'desc', 'closed_sort', 'closed_dir');
        $closedAssessments = $closedAssessmentsQuery->paginate(15, ['*'], 'closed_page')->withQueryString();

        $relevantTeamIds = $myAssessments->getCollection()->pluck('team_id')
            ->merge($pendingAssessments->getCollection()->pluck('team_id'))
            ->merge($completedAssessments->getCollection()->pluck('team_id'))
            ->merge($closedAssessments->getCollection()->pluck('team_id'))
            ->unique();
        $allTeams = Team::with('members')->whereIn('id', $relevantTeamIds)->get();
        $maturityByTeamId = $this->teamMaturityService->build($allTeams)->keyBy(fn ($entry) => $entry['team']->id);

        if (request()->ajax()) {
            $partials = [
                'my'        => ['dashboard.user.assessments._my-results', ['myAssessments' => $myAssessments]],
                'pending'   => ['dashboard.user.assessments._pending-results', ['pendingAssessments' => $pendingAssessments]],
                'completed' => ['dashboard.user.assessments._completed-results', ['completedAssessments' => $completedAssessments]],
                'closed'    => ['dashboard.user.assessments._closed-results', ['closedAssessments' => $closedAssessments]],
            ];
            [$view, $data] = $partials[request('section')] ?? $partials['my'];
            return view($view, [...$data, 'maturityByTeamId' => $maturityByTeamId]);
        }

        return view('dashboard.user.assessments.index', compact(
            'myAssessments', 'pendingAssessments', 'completedAssessments', 'closedAssessments', 'maturityByTeamId'
        ));
    }

    /**
     * Search + sort + slice an already-fetched collection into a
     * LengthAwarePaginator, for lists that can't be paginated at the query
     * level (see index()).
     */
    private function paginateCollection($collection, string $defaultSort, string $paramPrefix, int $perPage = 15): LengthAwarePaginator
    {
        if ($term = trim((string) request("{$paramPrefix}_search"))) {
            $needle = strtolower($term);
            $collection = $collection->filter(function ($item) use ($needle) {
                return str_contains(strtolower($item->title), $needle)
                    || str_contains(strtolower($item->team->name ?? ''), $needle);
            });
        }

        $sort = request("{$paramPrefix}_sort", $defaultSort);
        $dir = request("{$paramPrefix}_dir", 'desc');

        $sorted = $collection->sortBy(
            fn ($item) => $sort === 'created_at' ? $item->created_at->timestamp : strtolower($item->{$sort} ?? ''),
            SORT_REGULAR,
            $dir === 'desc'
        )->values();

        $page = (int) request("{$paramPrefix}_page", 1);

        return new LengthAwarePaginator(
            $sorted->forPage($page, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query(), 'pageName' => "{$paramPrefix}_page"]
        );
    }

    public function create()
    {
        $user = auth()->user();
        $isAdmin = $user->hasPermissionTo('manage-assessments');

        $teams = $isAdmin
            ? Team::orderBy('name')->get()
            : $user->teamsOwned->merge($user->teamsMemberOf)->unique('id')
                ->filter(fn (Team $team) => $team->canManageAssessments($user) && $user->planHasFeature('create-assessments'))
                ->sortBy('name')->values();

        if (!$isAdmin && $teams->isEmpty()) {
            return redirect()->route('user.assessments.index')
                ->with('error', 'You don\'t have permission to create assessments for any team, or your plan doesn\'t include this feature.');
        }

        $templates = AssessmentTemplate::with('team')
            ->where('is_public', true)
            ->orWhereIn('team_id', $teams->pluck('id'))
            ->orderBy('title')
            ->get();

        // planHasFeature() already returns true for admins (access-admin-panel bypass).
        $aiGenerationEnabled = $user->planHasFeature('ai-question-generation');

        return view('dashboard.user.assessments.create', compact('teams', 'templates', 'aiGenerationEnabled'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_id'     => 'required|exists:teams,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_id' => 'nullable|exists:assessment_templates,id',
            'ai_generate' => 'nullable|boolean',
            'ai_note'     => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $team = Team::findOrFail($validated['team_id']);
        $isAdmin = $user->hasPermissionTo('manage-assessments');

        abort_unless(
            $isAdmin || ($team->canManageAssessments($user) && $user->planHasFeature('create-assessments')),
            403
        );

        $template = null;
        if (!empty($validated['template_id'])) {
            $template = AssessmentTemplate::findOrFail($validated['template_id']);
            abort_unless($template->is_public || $template->team_id === $team->id, 403, 'This template is not available to this team.');
        }

        $assessment = Assessment::create([
            'team_id'     => $validated['team_id'],
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'created_by'  => auth()->id(),
            'status'      => 'draft',
        ]);

        if ($template) {
            $order = 0;
            foreach ($template->questions()->with('question')->get() as $templateQuestion) {
                if (!$templateQuestion->question?->is_active) {
                    continue; // skip questions retired since the template was made
                }
                AssessmentQuestion::firstOrCreate(
                    ['assessment_id' => $assessment->id, 'question_id' => $templateQuestion->question_id],
                    ['order' => ++$order]
                );
            }
        }

        if (!empty($validated['ai_generate'])) {
            return redirect()->route('user.assessments.show', $assessment)
                ->with('ai_autostart', true)
                ->with('ai_note', $validated['ai_note'] ?? null)
                ->with('success', 'Assessment created. Generating AI questions below...');
        }

        return redirect()->route('user.assessments.show', $assessment)
            ->with('success', $template
                ? 'Assessment created from template. Review the questions below.'
                : 'Assessment created. Add questions from the library below.');
    }

    public function saveAsTemplate(Assessment $assessment)
    {
        $this->authorizeOwner($assessment);
        abort_unless($assessment->questions()->count() > 0, 422, 'Add at least one question before saving as a template.');

        $validated = request()->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $isAdmin = auth()->user()->hasPermissionTo('manage-assessments');

        $template = AssessmentTemplate::create([
            'title'               => $validated['title'],
            'description'         => $validated['description'] ?? null,
            'team_id'             => $isAdmin ? null : $assessment->team_id,
            'created_by'          => auth()->id(),
            'is_public'           => $isAdmin,
            'source_assessment_id' => $assessment->id,
        ]);

        $order = 0;
        foreach ($assessment->questions()->with('question')->get() as $assessmentQuestion) {
            AssessmentTemplateQuestion::create([
                'assessment_template_id' => $template->id,
                'question_id'            => $assessmentQuestion->question_id,
                'order'                  => ++$order,
            ]);
        }

        return back()->with('success', $isAdmin
            ? 'Template saved and published globally.'
            : 'Template saved for your team. You can request it be made public from the team page.');
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

    public function aiContext(Assessment $assessment, AssessmentContextBuilder $contextBuilder)
    {
        $this->authorizeAiGeneration($assessment);

        $context = $contextBuilder->build($assessment->team);

        return response()->json([
            'summary'         => $context['summary'],
            'has_history'     => $context['history']['has_history'],
            'candidate_count' => count($context['candidates']),
            'is_high_churn'   => $context['churn']['is_high_churn'] ?? false,
        ]);
    }

    /**
     * Dispatches question-batch generation to the queue and returns
     * immediately — a full batch routinely takes 20-30+ seconds, too long
     * to hold open a synchronous request reliably. The browser polls
     * pollGenerateBatch() for the result.
     */
    public function generateQuestionsBatch(Request $request, Assessment $assessment)
    {
        $this->authorizeAiGeneration($assessment);

        $validated = $request->validate([
            'note'  => 'nullable|string|max:500',
            'count' => 'nullable|integer|min:3|max:12',
        ]);

        $requestId = (string) Str::uuid();

        // Seed a "pending" entry before dispatch so the first poll — which
        // can land before the queue worker has even picked the job up —
        // finds a real status instead of a false "not found".
        Cache::put(
            "ai-batch-request:{$requestId}",
            ['assessment_id' => $assessment->id, 'status' => 'pending'],
            now()->addMinutes(10)
        );

        GenerateAssessmentQuestionBatch::dispatch(
            $assessment->id,
            $requestId,
            $validated['note'] ?? null,
            $validated['count'] ?? 8,
        );

        return response()->json(['request_id' => $requestId]);
    }

    public function pollGenerateBatch(Assessment $assessment, string $requestId)
    {
        $this->authorizeAiGeneration($assessment);

        $result = Cache::get("ai-batch-request:{$requestId}");

        if (!$result || (int) $result['assessment_id'] !== $assessment->id) {
            return response()->json(['status' => 'failed', 'error' => 'This request has expired. Please try again.'], 404);
        }

        return response()->json($result);
    }

    public function commitGeneratedQuestions(Request $request, Assessment $assessment)
    {
        $this->authorizeAiGeneration($assessment);

        $validated = $request->validate([
            'items'               => 'required|array|min:1',
            'items.*.source'      => 'required|in:candidate,new',
            'items.*.question_id' => 'required_if:items.*.source,candidate|nullable|exists:questions,id',
            'items.*.text'        => 'required_if:items.*.source,new|nullable|string|max:255',
            'items.*.category_id' => 'required_if:items.*.source,new|nullable|exists:question_categories,id',
            'items.*.tips'        => 'nullable|array',
            'items.*.tips.0'      => 'nullable|string',
            'items.*.tips.1'      => 'nullable|string',
            'items.*.tips.2'      => 'nullable|string',
            'items.*.tips.3'      => 'nullable|string',
            'items.*.tips.4'      => 'nullable|string',
        ]);

        $order = $assessment->questions()->max('order') ?? 0;
        $added = collect();

        foreach ($validated['items'] as $item) {
            if ($item['source'] === 'candidate') {
                $question = Question::findOrFail($item['question_id']);
            } else {
                $question = Question::create([
                    'text'        => $item['text'],
                    'category_id' => $item['category_id'],
                    'tip_0'       => $item['tips']['0'] ?? null,
                    'tip_1'       => $item['tips']['1'] ?? null,
                    'tip_2'       => $item['tips']['2'] ?? null,
                    'tip_3'       => $item['tips']['3'] ?? null,
                    'tip_4'       => $item['tips']['4'] ?? null,
                    'is_active'   => true,
                ]);
            }

            $order++;
            $aq = AssessmentQuestion::firstOrCreate(
                ['assessment_id' => $assessment->id, 'question_id' => $question->id],
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

        return back()->with('success', 'AI-generated questions added.');
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
