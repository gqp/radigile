<?php

namespace Database\Seeders;

use App\Models\TeamMemberRole;
use Illuminate\Database\Seeder;

class TeamMemberRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'        => 'Team Lead',
                'slug'        => 'lead',
                'is_default'  => false,
                'sort_order'  => 1,
                'permissions' => ['take-assessments', 'view-results', 'view-results-detail', 'export-results', 'invite-members', 'manage-assessments'],
            ],
            [
                'name'        => 'Team Member',
                'slug'        => 'member',
                'is_default'  => true,
                'sort_order'  => 2,
                'permissions' => ['take-assessments', 'view-results', 'view-results-detail'],
            ],
            [
                'name'        => 'Observer',
                'slug'        => 'observer',
                'is_default'  => false,
                'sort_order'  => 3,
                'permissions' => ['view-results'],
            ],
        ];

        foreach ($roles as $role) {
            TeamMemberRole::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
