<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;


class User extends Authenticatable
{
    use HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'remaining_invites',
        'invite_code',
    ];

    /**
     * Ensure roles are always eagerly loaded when retrieving the user.
     */
    protected $with = ['roles'];

    /**
     * Define the roles relationship.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * A user has one subscription.
     */
    public function subscription(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    // Define the `subscriptions` relationship
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }


    /**
     * Return the user's active subscription, if any.
     */
    public function activeSubscription(): ?Subscription
    {
        return $this->subscription()->where('is_active', true)->first();
    }

    /**
     * Check if the user is subscribed to a given plan.
     *
     * @param  Plan  $plan
     * @return bool
     */
    public function subscribedTo(Plan $plan): bool
    {
        $activeSubscription = $this->activeSubscription();
        return $activeSubscription && $activeSubscription->plan_id === $plan->id;
    }

    /**
     * Determine if the user is on the free tier.
     * (Checks if they are subscribed to a free plan)
     *
     * @return bool
     */
    public function onFreeTier(): bool
    {
        $freePlan = Plan::where('price', 0)->first();
        return $freePlan && $this->subscribedTo($freePlan);
    }
}
