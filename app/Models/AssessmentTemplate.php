<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentTemplate extends Model
{
    protected $fillable = ['title', 'description', 'team_id', 'created_by', 'is_public', 'source_assessment_id'];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sourceAssessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'source_assessment_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentTemplateQuestion::class)->orderBy('order');
    }

    public function publishRequests(): HasMany
    {
        return $this->hasMany(AssessmentTemplatePublishRequest::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
