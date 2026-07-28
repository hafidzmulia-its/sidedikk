<?php

namespace Tests\Feature;

use App\Enums\EducationPostStatus;
use App\Enums\ScreeningStatus;
use App\Enums\VersionStatus;
use App\Models\EducationPost;
use App\Models\Question;
use App\Models\QuestionnaireVersion;
use App\Models\RiskLevel;
use App\Models\RiskRuleVersion;
use App\Models\Screening;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_single_questionnaire(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.questionnaires.store'), [
                'title' => 'Kuesioner Screening SIDEDIKK',
                'questions' => [
                    [
                        'text' => 'Apakah Ibu mengalami gejala A?',
                        'help_text' => 'Mohon pilih sesuai kondisi saat ini.',
                        'score_yes' => 2,
                        'score_no' => 0,
                        'is_active' => '1',
                    ],
                    [
                        'text' => 'Apakah Ibu mengalami gejala B?',
                        'help_text' => '',
                        'score_yes' => 3,
                        'score_no' => 0,
                        'is_active' => '1',
                    ],
                ],
            ])
            ->assertRedirect();

        $questionnaire = QuestionnaireVersion::query()
            ->where('title', 'Kuesioner Screening SIDEDIKK')
            ->firstOrFail();

        $this->assertSame(VersionStatus::Published, $questionnaire->status);
        $this->assertSame(5, $questionnaire->max_score_snapshot);
        $this->assertDatabaseCount('questions', 2);

        $this->actingAs($admin)
            ->put(route('admin.questionnaires.update', $questionnaire), [
                'title' => 'Kuesioner Screening Ibu Hamil',
                'questions' => [
                    [
                        'text' => 'Apakah Ibu mengalami gejala A?',
                        'help_text' => 'Mohon pilih sesuai kondisi saat ini.',
                        'score_yes' => 4,
                        'score_no' => 0,
                        'is_active' => '1',
                    ],
                    [
                        'text' => 'Apakah Ibu mengalami gejala C?',
                        'help_text' => 'Perhatikan perubahan kondisi Ibu.',
                        'score_yes' => 1,
                        'score_no' => 0,
                        'is_active' => '0',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.questionnaires.show', $questionnaire));

        $questionnaire->refresh();

        $this->assertSame(VersionStatus::Published, $questionnaire->status);
        $this->assertSame('Kuesioner Screening Ibu Hamil', $questionnaire->title);
        $this->assertSame(4, $questionnaire->max_score_snapshot);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin.questionnaire.updated',
            'subject_id' => $questionnaire->id,
        ]);
    }

    public function test_risk_rule_draft_range_validation_and_publish_workflow(): void
    {
        $admin = User::factory()->admin()->create();

        $publishedQuestionnaire = QuestionnaireVersion::factory()->create([
            'status' => VersionStatus::Published,
            'published_at' => now()->subDay(),
            'max_score_snapshot' => 6,
        ]);

        Question::factory()->for($publishedQuestionnaire)->create([
            'display_order' => 1,
            'score_yes' => 6,
        ]);

        $previousPublishedRule = RiskRuleVersion::factory()->create([
            'title' => 'Aturan Lama',
            'status' => VersionStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        RiskLevel::factory()->for($previousPublishedRule)->create([
            'name' => 'Risiko Rendah',
            'slug' => 'risiko-rendah-lama',
            'min_score' => 0,
            'max_score' => 6,
            'display_priority' => 1,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.risk-rules.create'))
            ->post(route('admin.risk-rules.store'), [
                'title' => 'Draft Dengan Gap',
                'is_demo_data' => '1',
                'medical_approval_required' => '1',
                'risk_levels' => [
                    [
                        'name' => 'Risiko Rendah',
                        'slug' => 'risiko-rendah',
                        'min_score' => 0,
                        'max_score' => 2,
                        'semantic_color' => 'success',
                        'description' => 'DEMO DATA - NOT FOR MEDICAL USE',
                        'recommendation' => 'DEMO DATA - NOT FOR MEDICAL USE',
                        'is_active' => '1',
                    ],
                    [
                        'name' => 'Risiko Tinggi',
                        'slug' => 'risiko-tinggi',
                        'min_score' => 4,
                        'max_score' => 6,
                        'semantic_color' => 'danger',
                        'description' => 'DEMO DATA - NOT FOR MEDICAL USE',
                        'recommendation' => 'DEMO DATA - NOT FOR MEDICAL USE',
                        'is_active' => '1',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.risk-rules.create'))
            ->assertSessionHasErrors('risk_levels');

        $this->actingAs($admin)
            ->post(route('admin.risk-rules.store'), [
                'title' => 'Draft Aturan Baru',
                'is_demo_data' => '1',
                'medical_approval_required' => '1',
                'risk_levels' => [
                    [
                        'name' => 'Risiko Rendah',
                        'slug' => 'rendah',
                        'min_score' => 0,
                        'max_score' => 2,
                        'semantic_color' => 'success',
                        'description' => 'DEMO DATA - NOT FOR MEDICAL USE',
                        'recommendation' => 'DEMO DATA - NOT FOR MEDICAL USE',
                        'is_active' => '1',
                    ],
                    [
                        'name' => 'Risiko Sedang',
                        'slug' => 'sedang',
                        'min_score' => 3,
                        'max_score' => 4,
                        'semantic_color' => 'warning',
                        'description' => 'DEMO DATA - NOT FOR MEDICAL USE',
                        'recommendation' => 'DEMO DATA - NOT FOR MEDICAL USE',
                        'is_active' => '1',
                    ],
                    [
                        'name' => 'Risiko Tinggi',
                        'slug' => 'tinggi',
                        'min_score' => 5,
                        'max_score' => 6,
                        'semantic_color' => 'danger',
                        'description' => 'DEMO DATA - NOT FOR MEDICAL USE',
                        'recommendation' => 'DEMO DATA - NOT FOR MEDICAL USE',
                        'is_active' => '1',
                    ],
                ],
            ])
            ->assertRedirect();

        $draft = RiskRuleVersion::query()
            ->where('title', 'Draft Aturan Baru')
            ->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.risk-rules.publish', $draft))
            ->assertRedirect(route('admin.risk-rules.show', $draft));

        $draft->refresh();
        $previousPublishedRule->refresh();

        $this->assertSame(VersionStatus::Published, $draft->status);
        $this->assertSame(6, $draft->max_score_covered);
        $this->assertSame(VersionStatus::Archived, $previousPublishedRule->status);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin.risk-rule.published',
            'subject_id' => $draft->id,
        ]);
    }

    public function test_admin_screening_resource_is_read_only_and_filterable(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $lowRisk = Screening::factory()->create([
            'user_id' => $user->id,
            'risk_label_snapshot' => 'Risiko Rendah',
            'status' => ScreeningStatus::Completed,
            'total_score' => 3,
        ]);

        Screening::factory()->create([
            'user_id' => $user->id,
            'risk_label_snapshot' => 'Risiko Tinggi',
            'status' => ScreeningStatus::Completed,
            'total_score' => 12,
        ]);

        $inProgress = Screening::factory()->create([
            'user_id' => $user->id,
            'status' => ScreeningStatus::InProgress,
            'completed_at' => null,
            'risk_label_snapshot' => null,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.screenings.show', $lowRisk))
            ->assertOk()
            ->assertSee('Detail Screening Read-Only')
            ->assertSee('Risiko Rendah');

        $this->actingAs($admin)
            ->get(route('admin.screenings.show', $inProgress))
            ->assertNotFound();

        $this->actingAs($admin)
            ->get(route('admin.screenings.index', ['risk_label' => 'Risiko Rendah']))
            ->assertOk()
            ->assertSee('Risiko Rendah')
            ->assertDontSee('Risiko Tinggi');
    }

    public function test_regular_user_cannot_access_admin_screening_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.screenings.index'))
            ->assertForbidden();
    }

    public function test_admin_education_management_validates_uploads_and_stores_published_posts(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.education.create'))
            ->post(route('admin.education.store'), [
                'title' => 'Artikel Invalid',
                'excerpt' => 'Ringkasan singkat.',
                'body' => 'Isi artikel.',
                'status' => EducationPostStatus::Published->value,
                'is_demo_data' => '1',
                'cover_image' => UploadedFile::fake()->create('invalid.pdf', 50, 'application/pdf'),
            ])
            ->assertRedirect(route('admin.education.create'))
            ->assertSessionHasErrors('cover_image');

        $this->actingAs($admin)
            ->post(route('admin.education.store'), [
                'title' => 'Artikel Valid',
                'excerpt' => 'Ringkasan singkat artikel valid.',
                'body' => 'Isi artikel valid untuk pengujian.',
                'status' => EducationPostStatus::Published->value,
                'is_demo_data' => '1',
                'cover_image' => UploadedFile::fake()->image('cover.png'),
            ])
            ->assertRedirect();

        $post = EducationPost::query()->where('title', 'Artikel Valid')->firstOrFail();

        $this->assertSame(EducationPostStatus::Published, $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertStringContainsString('/storage/education-covers/', $post->cover_image_path ?? '');
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $post->cover_image_path));
    }

    public function test_admin_user_resource_does_not_expose_passwords(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'password' => bcrypt('super-secret'),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.show', $user))
            ->assertOk()
            ->assertDontSee('super-secret')
            ->assertDontSee('password');
    }
}
