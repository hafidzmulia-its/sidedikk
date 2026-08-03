<?php

namespace Tests\Unit;

use App\Models\RiskLevel;
use PHPUnit\Framework\TestCase;

class RiskLevelCastTest extends TestCase
{
    public function test_numeric_risk_level_attributes_are_cast_to_integers(): void
    {
        $riskLevel = new RiskLevel([
            'min_score' => '0',
            'max_score' => '68',
            'display_priority' => '1',
            'is_active' => '1',
        ]);

        $this->assertSame(0, $riskLevel->min_score);
        $this->assertSame(68, $riskLevel->max_score);
        $this->assertSame(1, $riskLevel->display_priority);
        $this->assertTrue($riskLevel->is_active);
    }
}
