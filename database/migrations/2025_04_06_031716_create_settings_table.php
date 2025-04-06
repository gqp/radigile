<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->insert([
            [
                'name' => 'invite_only',
                'value' => false, // Default: invite-only mode is disabled
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
