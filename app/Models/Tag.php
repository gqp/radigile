<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class Tag extends Model
{
    protected $fillable = ['name'];

    /**
     * Changes more often than the other reference tables (new tags get
     * created inline while editing questions), but still cached — the
     * `saved`/`deleted` hooks below bust the cache the moment that happens.
     */
    public static function cached()
    {
        return Cache::remember('tags', now()->addHour(), fn () => static::all());
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('tags'));
        static::deleted(fn () => Cache::forget('tags'));
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'question_tag');
    }
}

