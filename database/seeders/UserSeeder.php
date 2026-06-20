<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Permissions (remain unchanged)
        $permissions = [
            'manage users',
            'manage roles',
            'manage permissions',
            'view dashboard',
            'edit profile',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Create Roles (remain unchanged)
        $roles = [
            'Admin' => ['manage users', 'manage roles', 'manage permissions', 'view dashboard'],
            'User' => ['view dashboard', 'edit profile'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            // Create the role if it doesn't exist
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Assign permissions to the role
            foreach ($rolePermissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                $role->givePermissionTo($permission);
            }
        }

        // Create an Admin User (unchanged)
        $admin = User::firstOrCreate([
            'email' => 'gqplaisted@gmail.com',
        ], [
            'name' => 'Garrick Admin',
            'password' => bcrypt('password'), // Make sure to change this in production
        ]);

        // Assign Admin Role to Admin User
        $admin->assignRole('Admin');

        // -- Generate 20 Unique Users with Role of 'User' --
        for ($i = 1; $i <= 20; $i++) {
            // Ensure unique email by using the loop index
            $email = "user{$i}@example.com";

            // Create the User
            $user = User::create([
                'name' => "User $i",
                'email' => $email, // Unique email
                'password' => bcrypt('password'), // Set a password
                'email_verified_at' => now(), // Set email as verified
                'last_login_at' => now(), // Set today's date as the last login
                'is_active' => true, // Assuming there is a column `is_active`
            ]);

            // Assign 'User' Role to the newly created user
            $user->assignRole('User');
        }

        // Log results to ensure everything seeded successfully
        \Log::info('UserSeeder executed successfully.');
    }
}
