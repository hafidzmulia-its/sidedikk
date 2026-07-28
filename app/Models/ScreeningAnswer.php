<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreeningAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'screening_id',
        'question_id',
        'questionnaire_version_id',
        'question_text_snapshot',
        'selected_answer',
        'awarded_score',
        'display_order_snapshot',
    ];

    public function screening(): BelongsTo
    {
        return $this->belongsTo(Screening::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
