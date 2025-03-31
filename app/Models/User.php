<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // Fields that can be mass-assigned
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'password',
        'role', // Added 'role' here
        'invitation_code',
        'agreed_to_terms',
        'email_verified_at',
        'is_active',
    ];

    // Fields that should be hidden in responses
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Field casting for specific data types
    protected $casts = [
        'email_verified_at' => 'datetime',
        'agreed_to_terms' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Check if the user is an admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

}
