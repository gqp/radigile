<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Invitation;
use Illuminate\Support\Str;


class InvitationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Invitation::create([
                'invitation_code' => Str::random(64),
                'expires_at' => now()->addDays(rand(1, 7)),
            ]);
        }

    }
}
