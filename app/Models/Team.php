<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'framework',
        'team_domain_id', // References the `team_domains` table
        'team_framework_id',   // References the `team_types` table
        'owner_id',
        'open_to_join_requests',
    ];

    protected $casts = [
        'open_to_join_requests' => 'boolean',
    ];

    public function isPublic(): bool
    {
        return $this->type === 'public';
    }

    public function isPrivate(): bool
    {
        return $this->type === 'private';
    }

    /**
     * Relationship with the owner of the team.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relationship with all members of the team.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withPivot('role') // Include the 'role' field from the pivot table
            ->withTimestamps();

    }

    /**
     * Relationship for invitations.
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }

    /**
     * Relationship for join requests.
     */
    public function joinRequests(): HasMany
    {
        return $this->hasMany(TeamJoinRequest::class);
    }

    /**
     * Whether $user can approve/reject join requests for this team — the
     * owner always can, plus any member whose team role has been granted
     * the 'approve-join-requests' permission.
     */
    public function canApproveJoinRequests(User $user): bool
    {
        if ($user->id === $this->owner_id) {
            return true;
        }

        $role = $this->members()->where('users.id', $user->id)->first()?->pivot->role;

        if (!$role) {
            return false;
        }

        return TeamMemberRole::where('slug', $role)->first()?->hasPermission('approve-join-requests') ?? false;
    }

    /**
     * Relationship for assessment templates saved by this team.
     */
    public function templates(): HasMany
    {
        return $this->hasMany(AssessmentTemplate::class);
    }

    /**
     * Whether $user can create/manage assessments (and, by extension, pick a
     * template / save-as-template / request-public) for this team — the
     * owner always can, plus any member whose team role has been granted
     * the 'manage-assessments' permission. Mirrors canApproveJoinRequests().
     */
    public function canManageAssessments(User $user): bool
    {
        if ($user->id === $this->owner_id) {
            return true;
        }

        $role = $this->members()->where('users.id', $user->id)->first()?->pivot->role;

        if (!$role) {
            return false;
        }

        return TeamMemberRole::where('slug', $role)->first()?->hasPermission('manage-assessments') ?? false;
    }

    /**
     * Scope: Get teams owned by a specific user.
     */
    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where('owner_id', $userId);
    }

    /**
     * Scope: Get teams currently accepting join requests.
     */
    public function scopeOpenToJoinRequests($query)
    {
        return $query->where('open_to_join_requests', true);
    }

    /**
     * Scope: Get public or private teams.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if a user has exceeded their team limit (e.g., 2 for free users).
     */
    public static function canCreateMoreTeams(User $user): bool
    {
        if ($user->hasPermissionTo('manage-teams')) {
            return true;
        }

        return $user->canCreateTeam();
    }

    public function questions()
    {
        return $this->hasManyThrough(
            Question::class,
            QuestionCategory::class,
            'team_domain',   // Foreign key on QuestionCategory table
            'category_id',   // Foreign key on Question table
            'team_domain',   // Local key on Team table
            'id'             // Local key on QuestionCategory table
        );
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(\App\Models\Assessment::class);
    }

    public function domain()
    {
        return $this->belongsTo(TeamDomain::class, 'team_domain_id');
    }

    public function team_frameq()
    {
        return $this->belongsTo(TeamFramework::class, 'team_framework_id');
    }


}
