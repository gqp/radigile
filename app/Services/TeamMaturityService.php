<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Team;
use Illuminate\Support\Collection;

/**
 * Computes maturity snapshots (current score, trend, weakest category,
 * participation, staleness) for a set of teams from their closed
 * assessments. Shared by the user dashboard, admin dashboard, and the
 * admin team-maturity report so the numbers stay consistent everywhere.
 */
class TeamMaturityService
{
    public function build(Collection $teams): Collection
    {
        $teamIds = $teams->pluck('id');

        $assessmentsByTeam = Assessment::whereIn('team_id', $teamIds)
            ->where('status', 'closed')
            ->with(['questions.question.category', 'responses.question.category', 'results'])
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy('team_id');

        return $teams->map(function (Team $team) use ($assessmentsByTeam) {
            $assessments = $assessmentsByTeam->get($team->id, collect());

            return $this->buildForTeam($team, $assessments->first(), $assessments->get(1));
        })->values();
    }

    protected function buildForTeam(Team $team, ?Assessment $assessment, ?Assessment $previous): array
    {
        if (!$assessment) {
            return [
                'team' => $team,
                'assessment' => null,
                'categories' => collect(),
                'scores' => collect(),
                'overall' => null,
                'previousOverall' => null,
                'trend' => null,
                'weakestCategory' => null,
                'participation' => null,
                'isStale' => null,
            ];
        }

        $categories = $assessment->questions
            ->pluck('question.category.name')
            ->filter()
            ->unique()
            ->values();

        $categoryAverages = $assessment->responses
            ->where('respondent_type', 'member')
            ->groupBy('question.category.name')
            ->map(fn ($responses) => round($responses->avg('score'), 2));

        $scores = $categories->map(fn ($cat) => $categoryAverages[$cat] ?? 0)->values();

        $overall = $this->averageOverallScore($assessment);
        $previousOverall = $previous ? $this->averageOverallScore($previous) : null;

        $trend = null;
        if ($overall !== null && $previousOverall !== null) {
            $diff = round($overall - $previousOverall, 2);
            $trend = $diff > 0.05 ? 'up' : ($diff < -0.05 ? 'down' : 'flat');
        }

        $weakestCategory = null;
        if ($categoryAverages->isNotEmpty()) {
            $name = $categoryAverages->sort()->keys()->first();
            $weakestCategory = ['name' => $name, 'score' => $categoryAverages[$name]];
        }

        $memberCount = $team->members->count();
        $respondedCount = $assessment->responses
            ->where('respondent_type', 'member')
            ->pluck('user_id')
            ->unique()
            ->count();
        $participation = $memberCount > 0 ? min(100, (int) round(($respondedCount / $memberCount) * 100)) : null;

        return [
            'team' => $team,
            'assessment' => $assessment,
            'categories' => $categories,
            'scores' => $scores,
            'overall' => $overall,
            'previousOverall' => $previousOverall,
            'trend' => $trend,
            'weakestCategory' => $weakestCategory,
            'participation' => $participation,
            'isStale' => $assessment->updated_at->lt(now()->subDays(90)),
        ];
    }

    protected function averageOverallScore(Assessment $assessment): ?float
    {
        $avg = $assessment->results->where('respondent_type', 'member')->avg('overall_score');

        return $avg !== null ? round($avg, 2) : null;
    }

    /**
     * Category name => org-wide average score for that category, from every
     * team's latest closed assessment — for a per-category "Org Avg" column
     * on an assessment's results breakdown table.
     */
    public function orgCategoryAverages(Collection $maturityEntries): Collection
    {
        return $maturityEntries
            ->filter(fn ($entry) => $entry['assessment'] !== null)
            ->flatMap(fn ($entry) => $entry['categories']->zip($entry['scores'])
                ->map(fn ($pair) => ['category' => $pair[0], 'score' => $pair[1]]))
            ->groupBy('category')
            ->map(fn ($group) => round($group->avg('score'), 2));
    }

    /**
     * Chronological overall-score series (one point per closed assessment)
     * for a team's maturity-over-time line chart.
     */
    public function history(Team $team): Collection
    {
        return $this->buildHistories(collect([$team]))->get($team->id, collect());
    }

    /**
     * Net change in overall score (latest closed assessment vs. earliest)
     * for each team — used to rank teams by trajectory rather than current
     * level, e.g. "Most Improved" / "Declining" on the admin dashboard.
     */
    public function trends(Collection $teams): Collection
    {
        $historiesByTeam = $this->buildHistories($teams);

        return $teams->map(function (Team $team) use ($historiesByTeam) {
            $history = $historiesByTeam->get($team->id, collect());

            if ($history->count() < 2) {
                return ['team' => $team, 'history' => $history, 'delta' => null, 'months' => $history->count()];
            }

            $delta = round($history->last()['overall'] - $history->first()['overall'], 2);

            return ['team' => $team, 'history' => $history, 'delta' => $delta, 'months' => $history->count()];
        })->values();
    }

    /**
     * team_id => chronological [['date' => ..., 'overall' => ...], ...],
     * from a single bulk query rather than one query per team.
     */
    protected function buildHistories(Collection $teams): Collection
    {
        $teamIds = $teams->pluck('id');

        return Assessment::whereIn('team_id', $teamIds)
            ->where('status', 'closed')
            ->with('results')
            ->orderBy('updated_at')
            ->get()
            ->groupBy('team_id')
            ->map(fn ($assessments) => $assessments
                ->map(fn (Assessment $assessment) => [
                    'date' => $assessment->updated_at->format('M Y'),
                    'overall' => $this->averageOverallScore($assessment),
                ])
                ->filter(fn ($point) => $point['overall'] !== null)
                ->values());
    }
}
