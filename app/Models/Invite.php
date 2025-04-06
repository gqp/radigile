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

    // Helper: Check if invite is valid
    public function isValid(): bool
    {
        return $this->is_active && $this->expires_at > now() && $this->times_used < $this->max_uses;
    }
}
