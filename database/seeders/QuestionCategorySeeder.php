<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionCategory;

class QuestionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['name' => 'Daily Standup'],
            ['name' => 'Sprint Planning'],
            ['name' => 'Retrospectives'],
            ['name' => 'Backlog Grooming'],
            ['name' => 'Team Collaboration'],
        ];

        foreach ($categories as $category) {
            QuestionCategory::updateOrCreate(
                ['name' => $category['name']], // Match by name for idempotency
                $category                       // Update or insert with this data
            );
        }
    }
}
