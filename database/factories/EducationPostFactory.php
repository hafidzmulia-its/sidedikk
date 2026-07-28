<?php

namespace Database\Factories;

use App\Enums\EducationPostStatus;
use App\Models\EducationPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EducationPost>
 */
class EducationPostFactory extends Factory
{
    protected $model = EducationPost::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(4);

        return [
            'title' => $title,
            'slug' => Str::slug($title.'-'.$this->faker->unique()->numberBetween(1, 9999)),
            'excerpt' => $this->faker->sentence(12),
            'body' => $this->faker->paragraphs(3, true),
            'cover_image_path' => '/demo/education/artikel-1.png',
            'status' => EducationPostStatus::Published,
            'published_at' => now(),
            'is_demo_data' => true,
        ];
    }
}
