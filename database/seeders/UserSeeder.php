<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $userRole = Role::firstOrCreate(['name' => 'User']);

        // Create Permissions
        $permissions = [
            'manage users',
            'manage roles',
            'edit posts',
            'delete posts',
            'create posts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->syncPermissions($permissions); // Admin gets all permissions
        $userRole->syncPermissions(['create posts']); // User gets limited permissions

        // Seed Users
        $users = [
            [
                'name' => 'Garrick Admin',
                'nId' => 1001,
                'email' => 'gqplaisted@gmail.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Garrick User',
                'nId' => 1002,
                'email' => 'garrick.plaisted@gmail.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email'],
            ], [
                'name' => $userData['name'],
                'nId' => $userData['nId'],
                'password' => $userData['password'],
            ]);

            $user->assignRole($userData['role']); // Assign role to the user
        }
    }
}
