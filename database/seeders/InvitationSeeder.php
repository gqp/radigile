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
        // List of predefined unique invitation codes
        $invitationCodes = [
            '2345',
            '2346',
            '2347',
            '2348',
            '2349',
            '2350',
            '2351',
            '2352',
            '2353',
            '2354',
        ];

        // Loop through the predefined codes
        foreach ($invitationCodes as $code) {
            Invitation::create([
                'invitation_code' => $code,
                'expires_at' => now()->addDays(rand(1, 7)),
            ]);
        }

    }
}
