<?php

namespace Database\Factories;

use App\Enums\VersionStatus;
use App\Models\QuestionnaireVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionnaireVersion>
 */
class QuestionnaireVersionFactory extends Factory
{
    protected $model = QuestionnaireVersion::class;

    public function definition(): array
    {
        return [
            'version_number' => fake()->numberBetween(1, 5),
            'title' => 'DEMO DATA - NOT FOR MEDICAL USE',
            'status' => VersionStatus::Published,
            'published_at' => now(),
            'max_score_snapshot' => 20,
            'is_demo_data' => true,
            'medical_approval_required' => true,
        ];
    }
}
