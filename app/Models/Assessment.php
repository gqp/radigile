<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Team;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResponse;
use App\Models\AssessmentResult;

class Assessment extends Model
{
    protected $fillable = ['team_id', 'title', 'description', 'status'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(AssessmentResponse::class);
    }

    public function result(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }
}
