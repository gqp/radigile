<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class QuestionCategory extends Model
{
    protected $fillable = ['name', 'description'];

    /**
     * Admin-managed and rarely changes — cache instead of hitting the DB on
     * every question-library/assessment-builder page load.
     */
    public static function cached()
    {
        return Cache::remember('question_categories', now()->addHour(), fn () => static::all());
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('question_categories'));
        static::deleted(fn () => Cache::forget('question_categories'));
    }

    // Relationship: A QuestionCategory can have many Questions
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'category_id');
    }

    // Function to generate radar data (MUST be inside the class)
    public function radarData($id)
    {
        $category = QuestionCategory::with('questions')->findOrFail($id);

        $radarData = $category->questions->map(function ($question) {
            return [
                'question' => $question->text,
                'tips' => [
                    '0' => $question->tip_0,
                    '1' => $question->tip_1,
                    '2' => $question->tip_2,
                    '3' => $question->tip_3,
                    '4' => $question->tip_4,
                ],
            ];
        });

        return $radarData;
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_domain', 'team_domain');
    }

}
