<?php

namespace App\Services;

use Carbon\CarbonImmutable;

class PregnancyAgeService
{
    /**
     * @return array{days_total:int,weeks:int,days:int,trimester:int}
     */
    public function calculateFromHpht(?string $hphtDate, ?CarbonImmutable $asOf = null): array
    {
        if (! $hphtDate) {
            return [
                'days_total' => 0,
                'weeks' => 0,
                'days' => 0,
                'trimester' => 1,
            ];
        }

        $reference = CarbonImmutable::parse($hphtDate)->startOfDay();
        $current = ($asOf ?? CarbonImmutable::now())->startOfDay();
        $daysTotal = (int) max(0, $reference->diffInDays($current));

        return [
            'days_total' => $daysTotal,
            'weeks' => intdiv($daysTotal, 7),
            'days' => $daysTotal % 7,
            'trimester' => match (true) {
                $daysTotal < 13 * 7 => 1,
                $daysTotal < 28 * 7 => 2,
                default => 3,
            },
        ];
    }
}
