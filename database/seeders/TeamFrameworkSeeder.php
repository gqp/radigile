<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamFrameworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $types = ['Scrum', 'KanBan'];
        foreach ($types as $type) {
            \App\Models\TeamFramework::create(['name' => $type]);
        }
    }

}
