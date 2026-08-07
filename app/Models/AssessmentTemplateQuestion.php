<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentTemplateQuestion extends Model
{
    protected $fillable = ['assessment_template_id', 'question_id', 'order'];

    public function assessmentTemplate(): BelongsTo
    {
        return $this->belongsTo(AssessmentTemplate::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
