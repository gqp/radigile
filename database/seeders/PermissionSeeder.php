<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminPermissions = [
            'access-admin-panel'  => 'Access the admin panel and sidebar',
            'manage-users'        => 'Create, edit and delete users',
            'manage-roles'        => 'Create, edit and delete roles and permissions',
            'manage-questions'    => 'Create, edit and delete questions in the library',
            'manage-teams'        => 'View and manage all teams',
            'manage-plans'        => 'Create, edit and delete subscription plans',
            'manage-assessments'  => 'View and manage all assessments across all teams',
        ];

        $userPermissions = [
            'access-user-portal'  => 'Access user-facing pages (teams, assessments, profile)',
            'manage-own-teams'    => 'Create and manage own teams',
            'take-assessments'    => 'Take assessments and view results',
        ];

        foreach (array_merge($adminPermissions, $userPermissions) as $name => $description) {
            Permission::firstOrCreate(['name' => $name]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(array_keys(array_merge($adminPermissions, $userPermissions)));

        $userRole = Role::firstOrCreate(['name' => 'User']);
        $userRole->syncPermissions(['access-user-portal', 'manage-own-teams', 'take-assessments']);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Permissions seeded and assigned to Admin and User roles.');
    }
}
