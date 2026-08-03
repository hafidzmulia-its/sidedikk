<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'risk_rule_version_id',
        'name',
        'slug',
        'min_score',
        'max_score',
        'semantic_color',
        'description',
        'recommendation',
        'display_priority',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'min_score' => 'integer',
            'max_score' => 'integer',
            'display_priority' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function riskRuleVersion(): BelongsTo
    {
        return $this->belongsTo(RiskRuleVersion::class);
    }
}
