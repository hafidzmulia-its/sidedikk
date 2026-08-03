<?php

namespace App\Models;

use App\Enums\ScreeningStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Screening extends Model
{
    use HasFactory;

    private const DEMO_SENTINEL = 'DEMO DATA - NOT FOR MEDICAL USE';

    protected $fillable = [
        'user_id',
        'questionnaire_version_id',
        'risk_rule_version_id',
        'status',
        'submission_key',
        'started_at',
        'completed_at',
        'gestational_age_weeks_snapshot',
        'gestational_age_days_snapshot',
        'total_score',
        'max_score',
        'risk_label_snapshot',
        'risk_description_snapshot',
        'recommendation_snapshot',
        'questionnaire_version_name_snapshot',
        'risk_rule_version_name_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'questionnaire_version_id' => 'integer',
            'risk_rule_version_id' => 'integer',
            'status' => ScreeningStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'gestational_age_weeks_snapshot' => 'integer',
            'gestational_age_days_snapshot' => 'integer',
            'total_score' => 'integer',
            'max_score' => 'integer',
        ];
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', ScreeningStatus::Completed);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', ScreeningStatus::InProgress);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questionnaireVersion(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireVersion::class);
    }

    public function riskRuleVersion(): BelongsTo
    {
        return $this->belongsTo(RiskRuleVersion::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ScreeningAnswer::class)->orderBy('display_order_snapshot');
    }

    public function getDisplayQuestionnaireVersionNameAttribute(): string
    {
        return $this->sanitizeDemoText($this->questionnaire_version_name_snapshot);
    }

    public function getDisplayRiskRuleVersionNameAttribute(): string
    {
        return $this->sanitizeDemoText($this->risk_rule_version_name_snapshot);
    }

    public function getDisplayRiskDescriptionAttribute(): string
    {
        $description = $this->sanitizeDemoText($this->risk_description_snapshot);

        return $description !== ''
            ? $description
            : 'Hasil ini menunjukkan klasifikasi risiko berdasarkan jawaban yang Ibu kirim.';
    }

    public function getDisplayRecommendationAttribute(): string
    {
        $recommendation = $this->sanitizeDemoText($this->recommendation_snapshot);

        return $recommendation !== ''
            ? $recommendation
            : 'Silakan lanjutkan pemeriksaan sesuai arahan tenaga kesehatan.';
    }

    protected function sanitizeDemoText(?string $value): string
    {
        $text = trim((string) $value);

        if ($text === '') {
            return '';
        }

        if ($text === self::DEMO_SENTINEL) {
            return '';
        }

        return trim(Str::replaceEnd(' - '.self::DEMO_SENTINEL, '', $text));
    }
}
