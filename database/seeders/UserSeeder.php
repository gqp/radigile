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
                'email' => 'gqplaisted@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'Admin', // Add role key
            ],
            [
                'name' => 'Garrick User',
                'email' => 'garrick.plaisted@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'User', // Add role key
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email'],
            ], [
                'name' => $userData['name'],
                'password' => $userData['password'],
            ]);

            $user->assignRole($userData['role']); // Assign role to the user
        }
    }
}
