<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TeamFramework extends Model
{
    protected $fillable = ['name'];

    /**
     * Admin-managed and rarely changes — cache instead of hitting the DB on
     * every team create/edit form load.
     */
    public static function cached()
    {
        return Cache::remember('team_frameworks', now()->addHour(), fn () => static::all());
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('team_frameworks'));
        static::deleted(fn () => Cache::forget('team_frameworks'));
    }
}
