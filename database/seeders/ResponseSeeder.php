<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

public function run()
{
    foreach (\App\Models\Assessment::all() as $assessment) {
        foreach ($assessment->team->members as $member) {
            foreach ($assessment->questions as $question) {
                \App\Models\Response::create([
                    'assessment_id' => $assessment->id,
                    'question_id' => $question->id,
                    'user_id' => $member->id,
                    'score' => rand(0, 4),
                ]);
            }
        }
    }
}
