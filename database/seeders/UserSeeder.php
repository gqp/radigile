<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'firstName' => 'Garrick',
            'lastName' => 'Plaisted-Admin',
            'email' => 'garrick.plaisted@gmail.com',
            'password' => Hash::make('password'),
            'password' => 'admin',
        ]);

        User::create([
            'firstName' => 'Garrick',
            'lastName' => 'Plaisted-Member',
            'email' => 'garrick.plaisted@gmail.com',
            'password' => Hash::make('password'),
            'password' => 'member',
        ]);
    }
}
