<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentResponse;
use Illuminate\Database\Seeder;

class ResponseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Assessment::with(['team.members', 'questions'])->get() as $assessment) {
            if (!$assessment->team) {
                continue;
            }

            foreach ($assessment->team->members as $member) {
                foreach ($assessment->questions as $assessmentQuestion) {
                    AssessmentResponse::firstOrCreate(
                        [
                            'assessment_id' => $assessment->id,
                            'user_id'       => $member->id,
                            'question_id'   => $assessmentQuestion->question_id,
                        ],
                        [
                            'score'           => rand(0, 4),
                            'respondent_type' => 'member',
                        ]
                    );
                }
            }
        }
    }
}
