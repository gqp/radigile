<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->insert([
            ['name' => 'invite_only', 'value' => true], // Default: registration open without invites
        ]);
        DB::table('settings')->insert([
            ['name' => 'notify_me', 'value' => true], // Default: registration open without invites
        ]);
    }
}
