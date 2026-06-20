<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamDomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $domains = ['devops', 'product', 'development', 'design', 'marketing'];
        foreach ($domains as $domain) {
            \App\Models\TeamDomain::create(['name' => $domain]);
        }
    }
}
