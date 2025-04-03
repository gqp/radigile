<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Garrick',
                'nId'      => 1001,
                'email'     => 'gqplaisted@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 1,
            ],
            [
                'name' => 'Garrick',
                'nId'      => 1002,
                'email'     => 'garrick.plaisted@gmail.com',
                'password'  => Hash::make('password'),
                'role'      => 2,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
