<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    public function run()
    {
        foreach (\App\Models\Team::all() as $team) {
            \App\Models\Assessment::create([
                'team_id' => $team->id,
                'title' => 'Initial Maturity Check',
                'created_by' => $team->owner_id,
            ]);
        }
    }
}
