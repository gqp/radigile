<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    protected $fillable = ['team_id', 'created_by', 'title', 'description', 'status'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class)->orderBy('order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(AssessmentResponse::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    public function evaluators(): HasMany
    {
        return $this->hasMany(AssessmentEvaluator::class);
    }

    public function excludedMembers(): HasMany
    {
        return $this->hasMany(AssessmentExcludedMember::class);
    }

    public function isMemberExcluded(int $userId): bool
    {
        return $this->excludedMembers->contains('user_id', $userId);
    }

    public function hasBeenCompletedBy(User $user): bool
    {
        // Property access (not method calls) so this uses already-eager-loaded
        // `questions`/`responses` collections when available instead of issuing
        // fresh queries — avoids N+1 when called across a list of assessments.
        $questionCount = $this->questions->count();
        if ($questionCount === 0) return false;
        return $this->responses->where('user_id', $user->id)->count() >= $questionCount;
    }

    public function isDraft(): bool { return $this->status === 'draft'; }
    public function isActive(): bool { return $this->status === 'active'; }
    public function isClosed(): bool { return $this->status === 'closed'; }
}
