<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TeamMember;
use App\Models\Team;
use App\Models\User;

class TeamMemberSeeder extends Seeder
{
    public function run()
    {

        foreach (Team::all() as $team) {
            $users = User::inRandomOrder()->take(5)->pluck('id');
            foreach ($users as $userId) {
                TeamMember::create([
                    'team_id' => $team->id,
                    'user_id' => $userId,
                    'role' => 'User',
                ]);
            }
        }
    }
}
