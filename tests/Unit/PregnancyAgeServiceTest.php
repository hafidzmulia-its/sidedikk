<?php

namespace Tests\Unit;

use App\Services\PregnancyAgeService;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class PregnancyAgeServiceTest extends TestCase
{
    public function test_it_calculates_gestational_age_from_hpht(): void
    {
        $service = new PregnancyAgeService;

        $result = $service->calculateFromHpht(
            '2026-05-16',
            CarbonImmutable::parse('2026-07-25'),
        );

        $this->assertSame(70, $result['days_total']);
        $this->assertSame(10, $result['weeks']);
        $this->assertSame(0, $result['days']);
        $this->assertSame(1, $result['trimester']);
    }
}
