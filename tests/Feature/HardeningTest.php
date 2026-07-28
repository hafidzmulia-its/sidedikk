<?php

namespace Tests\Feature;

use App\Enums\VersionStatus;
use App\Models\Question;
use App\Models\QuestionnaireVersion;
use App\Models\RiskLevel;
use App\Models\RiskRuleVersion;
use App\Models\Screening;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_include_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $response->assertHeader('Cross-Origin-Resource-Policy', 'same-origin');
    }

    public function test_sensitive_pages_include_no_store_headers(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $userResponse = $this->actingAs($user)->get(route('dashboard'));
        $adminResponse = $this->actingAs($admin)->get(route('admin.dashboard'));

        $userResponse->assertOk();
        $userResponse->assertHeader('Pragma', 'no-cache');
        $userResponse->assertHeader('Expires', '0');
        $this->assertStringContainsString('no-store', (string) $userResponse->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', (string) $userResponse->headers->get('Cache-Control'));

        $adminResponse->assertOk();
        $this->assertStringContainsString('no-store', (string) $adminResponse->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', (string) $adminResponse->headers->get('Cache-Control'));
    }

    public function test_login_is_rate_limited_after_too_many_failed_attempts(): void
    {
        $user = User::factory()->create();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from('/login')->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])->assertRedirect('/login');
        }

        $this->from('/login')
            ->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');
    }

    public function test_screening_submit_route_is_rate_limited(): void
    {
        $user = User::factory()->create();
        $this->createPublishedInstrument();

        $this->actingAs($user)->post(route('screenings.start'));
        $screening = Screening::query()->whereBelongsTo($user)->sole();

        $this->actingAs($user)->put(
            route('screenings.questions.update', ['screening' => $screening, 'step' => 1]),
            ['answer' => 'yes'],
        );

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->actingAs($user)
                ->post(route('screenings.submit', $screening), [
                    'submission_key' => $screening->submission_key,
                ])
                ->assertRedirect();
        }

        $this->actingAs($user)
            ->post(route('screenings.submit', $screening), [
                'submission_key' => $screening->submission_key,
            ])
            ->assertStatus(429);
    }

    protected function createPublishedInstrument(): void
    {
        $questionnaire = QuestionnaireVersion::query()->create([
            'version_number' => 1,
            'title' => 'Instrumen Hardening Demo',
            'status' => VersionStatus::Published,
            'published_at' => now()->subMinute(),
            'max_score_snapshot' => 2,
            'is_demo_data' => true,
            'medical_approval_required' => true,
        ]);

        Question::query()->create([
            'questionnaire_version_id' => $questionnaire->id,
            'text' => 'Apakah gejala hardening muncul?',
            'help_text' => 'DEMO DATA - NOT FOR MEDICAL USE',
            'score_yes' => 2,
            'score_no' => 0,
            'display_order' => 1,
            'is_active' => true,
        ]);

        $riskRule = RiskRuleVersion::query()->create([
            'version_number' => 1,
            'title' => 'Aturan Hardening Demo',
            'status' => VersionStatus::Published,
            'published_at' => now()->subMinute(),
            'max_score_covered' => 2,
            'is_demo_data' => true,
            'medical_approval_required' => true,
        ]);

        RiskLevel::query()->create([
            'risk_rule_version_id' => $riskRule->id,
            'name' => 'Risiko Rendah',
            'slug' => 'rendah-hardening',
            'min_score' => 0,
            'max_score' => 2,
            'semantic_color' => 'success',
            'description' => 'DEMO DATA - NOT FOR MEDICAL USE',
            'recommendation' => 'DEMO DATA - NOT FOR MEDICAL USE',
            'display_priority' => 1,
            'is_active' => true,
        ]);
    }
}
