<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'created_by',
        'invited_user_id',
        'max_uses',
        'times_used',
        'is_active',
        'expires_at',
        'email',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }

    public function team()
    {
        return $this->belongsTo(TeamFramework::class);
    }

    // Helper: Check if invite is valid
    public function isValid(): bool
    {
        return $this->is_active && $this->expires_at > now() && $this->times_used < $this->max_uses;
    }

    public function isReusableBy($userId): bool
    {
        return $this->is_active
            && $this->created_by === $userId
            && $this->times_used < $this->max_uses
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    // Ensure `expires_at` is casted to a DateTime object
    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
