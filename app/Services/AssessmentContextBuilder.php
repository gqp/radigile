<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\Question;
use App\Models\Team;

/**
 * Assembles what the AI batch generator needs to know about a team: its
 * framework/domain/size, its maturity history if it has one (weakest
 * category, trend, overall band), a membership-churn signal, and a set of
 * candidate questions already used by similar teams.
 *
 * Deliberately does not reuse TeamMaturityService — that service has no
 * demo-filtering hook and its per-team logic is protected. The maturity
 * arithmetic below is a small, intentional duplication of it, scoped to
 * non-demo assessments only. If TeamMaturityService's formulas change,
 * this needs to change too.
 */
class AssessmentContextBuilder
{
    private const CANDIDATE_LIMIT = 15;
    private const CHURN_RATIO_THRESHOLD = 0.5;
    private const CHURN_MIN_TEAM_SIZE = 4;

    public function build(Team $team): array
    {
        $team->loadMissing(['members', 'domain', 'team_frameq']);

        $assessments = $this->nonDemoClosedAssessments($team);
        $history = $this->historySignal($assessments);
        $churn = $this->churnSignal($team, $assessments->first());
        $candidates = $this->candidateQuestions($team);

        $teamInfo = [
            'id' => $team->id,
            'name' => $team->name,
            'size' => $team->members->count(),
            'framework' => $team->team_frameq?->name,
            'domain' => $team->domain?->name,
            'age_days' => (int) $team->created_at->diffInDays(now()),
        ];

        return [
            'team' => $teamInfo,
            'history' => $history,
            'churn' => $churn,
            'candidates' => $candidates,
            'summary' => $this->summarize($teamInfo, $history, $churn, $candidates),
        ];
    }

    private function nonDemoClosedAssessments(Team $team)
    {
        return Assessment::where('team_id', $team->id)
            ->where('status', 'closed')
            ->where('title', 'not like', '[Demo]%')
            ->with(['questions.question.category', 'responses.question.category', 'results'])
            ->orderByDesc('updated_at')
            ->get();
    }

    private function historySignal($assessments): array
    {
        $assessment = $assessments->first();
        $previous = $assessments->get(1);

        if (!$assessment) {
            return [
                'has_history' => false,
                'weakest_category' => null,
                'trend' => null,
                'overall' => null,
                'overall_band' => null,
            ];
        }

        $categoryAverages = $assessment->responses
            ->where('respondent_type', 'member')
            ->groupBy('question.category.name')
            ->map(fn ($responses) => round($responses->avg('score'), 2));

        $weakestCategory = null;
        if ($categoryAverages->isNotEmpty()) {
            $name = $categoryAverages->sort()->keys()->first();
            $weakestCategory = ['name' => $name, 'score' => $categoryAverages[$name]];
        }

        $overall = $this->averageOverallScore($assessment);
        $previousOverall = $previous ? $this->averageOverallScore($previous) : null;

        $trend = null;
        if ($overall !== null && $previousOverall !== null) {
            $diff = round($overall - $previousOverall, 2);
            $trend = $diff > 0.05 ? 'up' : ($diff < -0.05 ? 'down' : 'flat');
        }

        return [
            'has_history' => true,
            'weakest_category' => $weakestCategory,
            'trend' => $trend,
            'overall' => $overall,
            'overall_band' => $this->overallBand($overall),
        ];
    }

    private function overallBand(?float $overall): ?string
    {
        if ($overall === null) {
            return null;
        }

        return $overall < 2 ? 'low' : ($overall < 3 ? 'developing' : 'strong');
    }

    private function averageOverallScore(Assessment $assessment): ?float
    {
        $avg = $assessment->results->where('respondent_type', 'member')->avg('overall_score');

        return $avg !== null ? round($avg, 2) : null;
    }

    private function churnSignal(Team $team, ?Assessment $lastClosed): array
    {
        $totalMembers = $team->members->count();

        if (!$lastClosed) {
            return [
                'applicable' => false,
                'ratio' => null,
                'is_high_churn' => false,
                'members_joined_after_last_assessment' => null,
                'total_members' => $totalMembers,
            ];
        }

        $cutoff = $lastClosed->updated_at;
        $joinedAfter = $team->members->filter(
            fn ($member) => $member->pivot->created_at && $member->pivot->created_at->gt($cutoff)
        )->count();

        $ratio = $totalMembers > 0 ? round($joinedAfter / $totalMembers, 2) : 0.0;
        $isHighChurn = $ratio >= self::CHURN_RATIO_THRESHOLD && $totalMembers >= self::CHURN_MIN_TEAM_SIZE;

        return [
            'applicable' => true,
            'ratio' => $ratio,
            'is_high_churn' => $isHighChurn,
            'members_joined_after_last_assessment' => $joinedAfter,
            'total_members' => $totalMembers,
        ];
    }

    private function candidateQuestions(Team $team): array
    {
        if (!$team->team_framework_id || !$team->team_domain_id) {
            return [];
        }

        $counts = AssessmentQuestion::query()
            ->selectRaw('assessment_questions.question_id, COUNT(DISTINCT assessments.team_id) as usage_count')
            ->join('assessments', 'assessments.id', '=', 'assessment_questions.assessment_id')
            ->join('teams', 'teams.id', '=', 'assessments.team_id')
            ->where('teams.id', '!=', $team->id)
            ->where('teams.team_framework_id', $team->team_framework_id)
            ->where('teams.team_domain_id', $team->team_domain_id)
            ->where('assessments.title', 'not like', '[Demo]%')
            ->groupBy('assessment_questions.question_id')
            ->orderByDesc('usage_count')
            ->limit(self::CANDIDATE_LIMIT)
            ->pluck('usage_count', 'question_id');

        if ($counts->isEmpty()) {
            return [];
        }

        $questions = Question::with('category')
            ->active()
            ->whereIn('id', $counts->keys())
            ->get()
            ->keyBy('id');

        $candidates = [];
        $i = 0;
        foreach ($counts as $questionId => $usageCount) {
            $question = $questions->get($questionId);
            if (!$question) {
                continue; // retired since it was used, or otherwise no longer active
            }

            $i++;
            $candidates[] = [
                'ref' => "C{$i}",
                'id' => $question->id,
                'text' => $question->text,
                'category' => $question->category?->name ?? '—',
                'tips' => [
                    $question->tip_0, $question->tip_1, $question->tip_2, $question->tip_3, $question->tip_4,
                ],
                'usage_count' => $usageCount,
            ];
        }

        return $candidates;
    }

    private function summarize(array $team, array $history, array $churn, array $candidates): string
    {
        $parts = [];

        $parts[] = sprintf(
            '%s (%s · %s · %d member%s).',
            $team['name'],
            $team['framework'] ?? 'no framework set',
            $team['domain'] ?? 'no domain set',
            $team['size'],
            $team['size'] === 1 ? '' : 's'
        );

        if ($history['has_history']) {
            $parts[] = sprintf(
                'Based on closed assessments, current maturity is %s (%s/4)%s%s.',
                $history['overall_band'],
                $history['overall'],
                $history['trend'] ? ", trending {$history['trend']}" : '',
                $history['weakest_category'] ? ", weakest area: {$history['weakest_category']['name']}" : ''
            );
        } else {
            $parts[] = 'This team has no assessment history yet.';
        }

        if ($churn['applicable'] && $churn['is_high_churn']) {
            $parts[] = sprintf(
                '%d of %d current members (%d%%) joined after the last assessment — consider re-establishing foundational context.',
                $churn['members_joined_after_last_assessment'],
                $churn['total_members'],
                round($churn['ratio'] * 100)
            );
        }

        $candidateCount = count($candidates);
        if ($candidateCount > 0) {
            $parts[] = "{$candidateCount} candidate question" . ($candidateCount === 1 ? '' : 's')
                . ' found from other teams with the same framework/domain'
                . ($history['has_history'] ? '.' : ' — generation will lean on these where strong matches exist.');
        }

        return implode(' ', $parts);
    }
}
