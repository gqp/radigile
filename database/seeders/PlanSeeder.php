<?php

// database/seeders/PlanSeeder.php
namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::create([
            'name' => 'Free Plan',
            'description' => 'Access to basic features for free.',
            'price' => 0,
            'interval' => 'free',
            'is_active' => true,
        ]);
    }
}
