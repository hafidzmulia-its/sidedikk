<?php

namespace App\Services;

use App\Models\RiskLevel;
use App\Models\RiskRuleVersion;

class RiskClassificationService
{
    public function classify(int $score, RiskRuleVersion $ruleVersion): ?RiskLevel
    {
        return $ruleVersion->riskLevels()
            ->where('is_active', true)
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->orderBy('display_priority')
            ->first();
    }
}
