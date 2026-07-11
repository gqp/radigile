<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Team;
use App\Services\TeamMaturityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AssessmentResultController extends Controller
{
    public function __construct(private TeamMaturityService $teamMaturityService)
    {
    }

    public function show(Assessment $assessment)
    {
        $user = auth()->user();

        $isOwner     = $assessment->created_by === $user->id;
        $isMember    = $assessment->team->owner_id === $user->id
                       || $user->hasPermissionTo('manage-assessments')
                       || $user->teamsMemberOf()->where('teams.id', $assessment->team_id)->exists();
        $isEvaluator = $assessment->evaluators()->where('user_id', $user->id)->exists();

        abort_unless($isOwner || $isMember || $isEvaluator, 403);
        abort_unless($assessment->isClosed(), 403, 'Results are not available until the assessment is closed.');

        $assessment->load(['questions.question.category', 'team', 'responses.question.category', 'responses.user']);

        $categories = $assessment->questions
            ->pluck('question.category.name')
            ->unique()
            ->values();

        $memberData   = $this->buildCategoryScores($assessment, 'member', $categories);
        $externalData = $this->buildCategoryScores($assessment, 'external', $categories);

        $hasExternalData = collect($externalData)->sum() > 0;

        $individualScores = $assessment->responses
            ->where('respondent_type', 'member')
            ->groupBy('user_id')
            ->map(function ($responses) use ($categories) {
                $scoresByCategory = $responses->groupBy('question.category.name')
                    ->map(fn($r) => round($r->avg('score'), 2));
                return [
                    'name'   => $responses->first()->user->name,
                    'scores' => $categories->map(fn($cat) => $scoresByCategory[$cat] ?? 0)->values()->toArray(),
                ];
            })
            ->values();

        $overallTeamScore = $assessment->results()->where('respondent_type', 'member')->avg('overall_score');
        $overallExternalScore = $assessment->results()->where('respondent_type', 'external')->avg('overall_score');

        // Same cache/computation the admin dashboard uses, so "Org Average"
        // means the same number everywhere it appears.
        $maturitySnapshot = Cache::remember('admin.team_maturity_snapshot', now()->addMinutes(15), function () {
            return $this->teamMaturityService->build(Team::with('members')->get());
        });
        $ratedTeams = $maturitySnapshot->filter(fn ($entry) => $entry['overall'] !== null);
        $orgAverage = $ratedTeams->isNotEmpty() ? round($ratedTeams->avg('overall'), 2) : null;

        $orgCategoryAverages = $this->teamMaturityService->orgCategoryAverages($maturitySnapshot);
        $orgCategoryData = $categories->map(fn ($cat) => $orgCategoryAverages[$cat] ?? null)->toArray();

        return view('dashboard.user.assessments.results', compact(
            'assessment', 'categories', 'memberData', 'externalData',
            'hasExternalData', 'individualScores', 'isOwner',
            'overallTeamScore', 'overallExternalScore', 'orgAverage', 'orgCategoryData'
        ));
    }

    private function buildCategoryScores(Assessment $assessment, string $type, $categories): array
    {
        $scores = $assessment->responses
            ->where('respondent_type', $type)
            ->groupBy('question.category.name')
            ->map(fn($r) => round($r->avg('score'), 2));

        return $categories->map(fn($cat) => $scores[$cat] ?? 0)->values()->toArray();
    }
}
