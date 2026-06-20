<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

public function run()
{
    foreach (\App\Models\Assessment::all() as $assessment) {
        foreach (\App\Models\Category::all() as $category) {
            \App\Models\AssessmentResult::create([
                'assessment_id' => $assessment->id,
                'category_id' => $category->id,
                'average_score' => rand(1, 4),
                'feedback' => 'Automated feedback placeholder based on score.',
            ]);
        }
    }
}
