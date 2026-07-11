<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'category_id',
        'tip_0',
        'tip_1',
        'tip_2',
        'tip_3',
        'tip_4',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Query Scope: Get only active (enabled) questions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relationship with QuestionCategory
    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionCategory::class);
    }

    // Relationship with Team
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'category_id', 'team_domain');
    }

    // Relationship with Tags
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'question_tag');
    }
}
