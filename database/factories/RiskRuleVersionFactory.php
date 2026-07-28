<?php

namespace Database\Factories;

use App\Enums\VersionStatus;
use App\Models\RiskRuleVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiskRuleVersion>
 */
class RiskRuleVersionFactory extends Factory
{
    protected $model = RiskRuleVersion::class;

    public function definition(): array
    {
        return [
            'version_number' => $this->faker->numberBetween(1, 5),
            'title' => 'DEMO DATA - NOT FOR MEDICAL USE',
            'status' => VersionStatus::Published,
            'published_at' => now(),
            'max_score_covered' => 20,
            'is_demo_data' => true,
            'medical_approval_required' => true,
        ];
    }
}
