<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMemberRole extends Model
{
    protected $fillable = ['name', 'slug', 'is_default', 'sort_order', 'permissions'];

    protected $casts = [
        'is_default'  => 'boolean',
        'permissions' => 'array',
    ];

    public static array $availablePermissions = [
        'take-assessments'      => 'Take Assessments',
        'view-results'          => 'View Assessment Results',
        'view-results-detail'   => 'View Detailed Results & Analytics',
        'export-results'        => 'Export Results (CSV)',
        'invite-members'        => 'Invite Team Members',
        'manage-assessments'    => 'Create & Manage Assessments',
        'approve-join-requests' => 'Approve Join Requests',
    ];

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? [], true);
    }

    public static function labelFor(string $slug): string
    {
        return static::where('slug', $slug)->value('name') ?? ucfirst($slug);
    }

    public static function default(): ?self
    {
        return static::where('is_default', true)->orderBy('sort_order')->first();
    }

    public static function allOrdered(): \Illuminate\Database\Eloquent\Collection
    {
        return static::orderBy('sort_order')->orderBy('name')->get();
    }
}
