<?php

namespace Database\Seeders;

use App\Models\QuestionCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call all required seeders in one method
        $this->call([
            UserSeeder::class,
            SettingsSeeder::class,
            PlanSeeder::class,
            SubscriptionSeeder::class,
            TeamDomainSeeder::class,
            TeamFrameworkSeeder::class,
            TeamMemberRoleSeeder::class,
            TeamSeeder::class,
            QuestionCategorySeeder::class,
            QuestionSeeder::class,
            AssessmentSeeder::class,
        ]);
    }
}
