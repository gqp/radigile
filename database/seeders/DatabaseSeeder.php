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
            TeamDomainSeeder::class,  // Ensure this populates the `team_domains` table
            TeamFrameworkSeeder::class,  // Ensure this populates the `team_frameworks` table
            TeamSeeder::class,  // Finally, this creates `teams`
            QuestionCategorySeeder::class,
            QuestionSeeder::class,
        ]);
    }
}
