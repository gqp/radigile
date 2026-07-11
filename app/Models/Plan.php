<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'interval',
        'is_active',
        'stripe_price_id',
        'max_teams',
        'max_members',
        'features',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features'  => 'array',
    ];

    /**
     * Admin-managed and rarely changes — cache instead of hitting the DB on
     * every page that lists plans (signup, billing, admin user forms, etc.).
     */
    public static function cached()
    {
        return Cache::remember('plans', now()->addHour(), fn () => static::all());
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('plans'));
        static::deleted(fn () => Cache::forget('plans'));
    }

    /**
     * All toggleable features available across plans.
     * Key   = machine name used in code checks.
     * Value = human-readable label shown in admin UI.
     */
    public static array $availableFeatures = [
        'create-assessments'     => 'Create & Manage Assessments',
        'team-invitations'       => 'Team Member Invitations',
        'view-results-detail'    => 'Detailed Results & Analytics',
        'export-results'         => 'Export Results (CSV)',
        'ai-question-generation' => 'AI Question Generation',
    ];

    public function hasFeature(string $key): bool
    {
        return in_array($key, $this->features ?? [], true);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
