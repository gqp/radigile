<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create Roles
        $adminRole = Role::create(['name' => 'Admin']);
        $userRole = Role::create(['name' => 'User']);

        // Create Permissions
        $permissions = [
            'manage users',
            'manage roles',
            'edit posts',
            'delete posts',
            'create posts',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to Admin role
        $adminRole->givePermissionTo($permissions);

        // Optionally, assign some permissions to User role
        $userRole->givePermissionTo(['create posts']);
    }
}
