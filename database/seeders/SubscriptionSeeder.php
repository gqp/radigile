<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch the first active plan (e.g., the "Free Plan")
        $freePlan = Plan::where('name', 'Free Plan')->firstOrFail();

        // Assign the free plan to all users without subscriptions
        User::doesntHave('subscriptions')->each(function (User $user) use ($freePlan) {
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $freePlan->id,
                'starts_at' => Carbon::now(),
                'ends_at' => null, // Ongoing subscription
                'is_active' => true,
            ]);
        });

        // Example: Add a Pro Plan subscription for a specific user
        $proPlan = Plan::where('name', 'Pro Plan')->first();

        if ($proPlan) {
            Subscription::create([
                'user_id' => 1, // Replace with the ID of a specific user
                'plan_id' => $proPlan->id,
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addMonth(),
                'is_active' => true,
            ]);
        }
    }
}
