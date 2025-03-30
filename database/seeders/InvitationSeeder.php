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
                //'invitation_code' => Str::random(64),
                'invitation_code' => "4567",
                'expires_at' => now()->addDays(rand(1, 7)),
            ]);
            Invitation::create([
                //'invitation_code' => Str::random(64),
                'invitation_code' => "2345",
                'expires_at' => now()->addDays(rand(1, 7)),
            ]);
        }

    }
}
