<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\InvitationSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'firstName' => 'Test',
            'lastName' => 'User',
            'password' => bcrypt('password'),
            'email' => 'test@example.com',
        ]);

        // Call the InvitationsSeeder
        $this->call([
            InvitationSeeder::class,
        ]);

    }


}
