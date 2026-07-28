<?php

namespace Tests\Feature;

use App\Enums\EducationPostStatus;
use App\Models\EducationPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_published_education_posts_are_visible_to_users(): void
    {
        $user = User::factory()->create();

        $publishedPost = EducationPost::factory()->create([
            'title' => 'Artikel Aman',
            'slug' => 'artikel-aman',
        ]);

        EducationPost::factory()->create([
            'title' => 'Artikel Draft',
            'slug' => 'artikel-draft',
            'status' => EducationPostStatus::Draft,
            'published_at' => null,
        ]);

        $this->actingAs($user)
            ->get(route('education.index'))
            ->assertOk()
            ->assertSee('Artikel Aman')
            ->assertDontSee('Artikel Draft');

        $this->actingAs($user)
            ->get(route('education.show', $publishedPost->slug))
            ->assertOk()
            ->assertSee('Artikel Aman');
    }

    public function test_unpublished_education_detail_returns_not_found(): void
    {
        $user = User::factory()->create();

        $draftPost = EducationPost::factory()->create([
            'slug' => 'artikel-draft',
            'status' => EducationPostStatus::Draft,
            'published_at' => null,
        ]);

        $this->actingAs($user)
            ->get(route('education.show', $draftPost->slug))
            ->assertNotFound();
    }
}
