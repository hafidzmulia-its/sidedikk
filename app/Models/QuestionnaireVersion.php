<?php

namespace App\Models;

use App\Enums\VersionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionnaireVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'version_number',
        'title',
        'status',
        'published_at',
        'published_by',
        'max_score_snapshot',
        'is_demo_data',
        'medical_approval_required',
    ];

    protected function casts(): array
    {
        return [
            'status' => VersionStatus::class,
            'published_at' => 'datetime',
            'is_demo_data' => 'boolean',
            'medical_approval_required' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', VersionStatus::Published);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', VersionStatus::Draft);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('display_order');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
