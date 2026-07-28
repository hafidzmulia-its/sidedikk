<?php

namespace Database\Factories;

use App\Models\RiskLevel;
use App\Models\RiskRuleVersion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<RiskLevel>
 */
class RiskLevelFactory extends Factory
{
    protected $model = RiskLevel::class;

    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'risk_rule_version_id' => RiskRuleVersion::factory(),
            'name' => $name,
            'slug' => Str::slug($name.'-'.fake()->unique()->numberBetween(1, 999)),
            'min_score' => 0,
            'max_score' => 4,
            'semantic_color' => 'success',
            'description' => 'DEMO DATA - NOT FOR MEDICAL USE',
            'recommendation' => 'DEMO DATA - NOT FOR MEDICAL USE',
            'display_priority' => 1,
            'is_active' => true,
        ];
    }
}
