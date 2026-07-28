<?php

namespace Tests\Feature;

use App\Enums\ScreeningStatus;
use App\Models\Screening;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScreeningHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_page_only_shows_completed_screenings_for_the_authenticated_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Screening::factory()->create([
            'user_id' => $user->id,
            'risk_label_snapshot' => 'Risiko Rendah',
            'total_score' => 4,
        ]);

        Screening::factory()->create([
            'user_id' => $otherUser->id,
            'risk_label_snapshot' => 'Risiko Tinggi',
            'total_score' => 12,
        ]);

        Screening::factory()->create([
            'user_id' => $user->id,
            'status' => ScreeningStatus::InProgress,
            'completed_at' => null,
            'risk_label_snapshot' => null,
        ]);

        $this->actingAs($user)
            ->get(route('history.index'))
            ->assertOk()
            ->assertSee('Risiko Rendah')
            ->assertDontSee('Risiko Tinggi');
    }
}
