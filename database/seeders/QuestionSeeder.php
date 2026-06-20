<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\QuestionCategory;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Loop through all categories
        foreach (QuestionCategory::all() as $category) {
            foreach (range(1, 5) as $i) {
                Question::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'text' => "How well does your team handle {$category->name} activity $i?"
                    ],
                    [
                        'tip_0' => "Not practiced.",
                        'tip_1' => "Rarely effective.",
                        'tip_2' => "Showing progress.",
                        'tip_3' => "Mostly effective.",
                        'tip_4' => "Fully effective and consistent."
                    ]
                );
            }
        }
    }
}
