<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionnaireVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'questionnaire_version_id' => QuestionnaireVersion::factory(),
            'text' => $this->faker->sentence(8),
            'help_text' => $this->faker->sentence(),
            'score_yes' => $this->faker->numberBetween(1, 6),
            'score_no' => 0,
            'display_order' => $this->faker->unique()->numberBetween(1, 30),
            'is_active' => true,
        ];
    }
}
