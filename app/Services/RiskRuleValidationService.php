<?php

namespace App\Services;

use App\Models\RiskRuleVersion;
use LogicException;

class RiskRuleValidationService
{
    public function assertCoverage(RiskRuleVersion $version, int $maxScore): void
    {
        $levels = $version->riskLevels()
            ->active()
            ->orderBy('min_score')
            ->get();

        if ($levels->isEmpty()) {
            throw new LogicException('Aturan risiko aktif belum tersedia.');
        }

        $expectedMin = 0;

        foreach ($levels as $level) {
            if ($level->min_score !== $expectedMin) {
                throw new LogicException('Rentang aturan risiko memiliki celah atau tidak dimulai dari skor 0.');
            }

            if ($level->max_score < $level->min_score) {
                throw new LogicException('Rentang aturan risiko tidak valid.');
            }

            $expectedMin = $level->max_score + 1;
        }

        if (($levels->last()?->max_score ?? -1) < $maxScore) {
            throw new LogicException('Rentang aturan risiko belum mencakup seluruh skor kuesioner.');
        }
    }
}
