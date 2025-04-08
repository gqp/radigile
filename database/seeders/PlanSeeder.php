<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::insert([
            [
                'name' => 'Free Plan',
                'description' => 'A free plan for testing purposes.',
                'price' => 0.00,
                'interval' => 'free',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Monthly Plan',
                'description' => 'A monthly subscription plan.',
                'price' => 9.99,
                'interval' => 'monthly',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yearly Plan',
                'description' => 'A yearly subscription plan.',
                'price' => 99.99,
                'interval' => 'yearly',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
