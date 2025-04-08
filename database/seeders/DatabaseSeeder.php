<?php

namespace Database\Seeders;

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
        ]);
        $this->call([
            SettingsSeeder::class,
        ]);
        $this->call([
            PlanSeeder::class,
        ]);
        $this->call([
            SubscriptionSeeder::class,
        ]);
    }
}
