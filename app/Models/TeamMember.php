<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    protected $table = 'team_user';

    protected $fillable = ['user_id', 'team_id', 'role'];

    /**
     * Relationship to the associated team.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Relationship to the associated user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for filtering members by role.
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to check if a specific user is already a member of a team.
     */
    public function scopeIsMember($query, int $userId, int $teamId)
    {
        return $query->where('user_id', $userId)->where('team_id', $teamId);
    }
}
