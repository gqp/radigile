<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use Illuminate\Database\Seeder;

class AssessmentResultSeeder extends Seeder
{
    /**
     * Aggregates each member's existing responses into an overall score —
     * run ResponseSeeder first, or this has nothing to aggregate.
     */
    public function run(): void
    {
        foreach (Assessment::with(['team.members', 'responses'])->get() as $assessment) {
            if (!$assessment->team) {
                continue;
            }

            foreach ($assessment->team->members as $member) {
                $scores = $assessment->responses->where('user_id', $member->id)->pluck('score');
                if ($scores->isEmpty()) {
                    continue;
                }

                AssessmentResult::updateOrCreate(
                    ['assessment_id' => $assessment->id, 'user_id' => $member->id],
                    [
                        'overall_score'    => round($scores->avg(), 2),
                        'respondent_type'  => 'member',
                    ]
                );
            }
        }
    }
}
