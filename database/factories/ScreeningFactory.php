<?php

namespace Database\Factories;

use App\Enums\ScreeningStatus;
use App\Models\QuestionnaireVersion;
use App\Models\RiskRuleVersion;
use App\Models\Screening;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Screening>
 */
class ScreeningFactory extends Factory
{
    protected $model = Screening::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'questionnaire_version_id' => QuestionnaireVersion::factory(),
            'risk_rule_version_id' => RiskRuleVersion::factory(),
            'status' => ScreeningStatus::Completed,
            'submission_key' => (string) Str::uuid(),
            'started_at' => now()->subMinutes(10),
            'completed_at' => now(),
            'gestational_age_weeks_snapshot' => fake()->numberBetween(6, 32),
            'gestational_age_days_snapshot' => fake()->numberBetween(0, 6),
            'total_score' => fake()->numberBetween(0, 15),
            'max_score' => 20,
            'risk_label_snapshot' => 'Risiko Rendah',
            'risk_description_snapshot' => 'DEMO DATA - NOT FOR MEDICAL USE',
            'recommendation_snapshot' => 'DEMO DATA - NOT FOR MEDICAL USE',
            'questionnaire_version_name_snapshot' => 'DEMO DATA - NOT FOR MEDICAL USE',
            'risk_rule_version_name_snapshot' => 'DEMO DATA - NOT FOR MEDICAL USE',
        ];
    }
}
