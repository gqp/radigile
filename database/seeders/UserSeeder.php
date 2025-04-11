<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Permissions
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

        // Create Roles
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

        // Create an Admin User
        $admin = User::firstOrCreate([
            'email' => 'gqplaisted@gmail.com',
        ], [
            'name' => 'Garrick Admin',
            'password' => bcrypt('password'), // Be sure to change this in a production environment
        ]);

        // Assign Admin Role to Admin User
        $admin->assignRole('Admin');

        // Create a standard User
        $user = User::firstOrCreate([
            'email' => 'garrick.plaisted@gmail.com',
        ], [
            'name' => 'Garrick User',
            'password' => bcrypt('password'), // Be sure to change this in a production environment
        ]);

        // Assign User Role to Standard User
        $user->assignRole('User');

        // Log results to ensure everything seeded successfully
        \Log::info('UserSeeder executed successfully.');
    }
}
