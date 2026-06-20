<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\TeamDomain;
use App\Models\TeamFramework;

class TeamSeeder extends Seeder
{
    public function run()
    {
        // Fetch all team domains and frameworks as arrays of their IDs
        $teamDomains = TeamDomain::pluck('id')->toArray(); // Get all domain IDs
        $teamFrameworks = TeamFramework::pluck('id')->toArray(); // Get all framework IDs

        // Guard against empty arrays
        if (empty($teamDomains) || empty($teamFrameworks)) {
            $this->command->warn('No data available in team_domains or team_frameworks for seeding teams.');
            return;
        }


        // Create 10 teams with random associations
        foreach (range(1, 10) as $i) {
            Team::create([
                'name' => "Team $i",
                'description' => "Description for Team $i",
                'team_domain_id' => $teamDomains[array_rand($teamDomains)], // Assign a random domain
                'team_framework_id' => $teamFrameworks[array_rand($teamFrameworks)], // Assign a random framework
                'owner_id' => rand(1, 20), // Replace with valid user ID logic
            ]);
        }
    }
}
