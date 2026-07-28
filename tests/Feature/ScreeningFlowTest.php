<?php

namespace Tests\Feature;

use App\Enums\ScreeningStatus;
use App\Enums\UserRole;
use App\Enums\VersionStatus;
use App\Models\Question;
use App\Models\QuestionnaireVersion;
use App\Models\RiskLevel;
use App\Models\RiskRuleVersion;
use App\Models\Screening;
use App\Models\User;
use App\Services\PregnancyAgeService;
use App\Services\QuestionnairePublishingService;
use App\Services\RiskRulePublishingService;
use App\Services\ScreeningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class ScreeningFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_screening_uses_only_published_versions(): void
    {
        $user = User::factory()->create();

        [$publishedQuestionnaire, $publishedRiskRule] = $this->createPublishedInstrument(
            questionnaireTitle: 'Kuesioner Published',
            riskRuleTitle: 'Aturan Published',
        );

        $this->createDraftQuestionnaire('Kuesioner Draft');
        $this->createDraftRiskRule('Aturan Draft');

        $this->actingAs($user)
            ->post(route('screenings.start'))
            ->assertRedirect();

        $screening = Screening::query()->sole();

        $this->assertSame($publishedQuestionnaire->id, $screening->questionnaire_version_id);
        $this->assertSame($publishedRiskRule->id, $screening->risk_rule_version_id);
        $this->assertSame('Kuesioner Published', $screening->questionnaire_version_name_snapshot);
        $this->assertSame('Aturan Published', $screening->risk_rule_version_name_snapshot);
    }

    public function test_in_progress_screening_keeps_locked_versions_after_new_publication(): void
    {
        $user = User::factory()->create();
        $publisher = User::factory()->create(['role' => UserRole::Admin]);

        [$questionnaireV1, $riskRuleV1] = $this->createPublishedInstrument(
            questionnaireTitle: 'Kuesioner V1',
            riskRuleTitle: 'Aturan V1',
            questions: [
                ['Pertanyaan lama yang harus tetap terkunci?', 2],
                ['Apakah Ibu mengalami gejala lain?', 3],
            ],
        );

        $this->actingAs($user)->post(route('screenings.start'));
        $screening = Screening::query()->sole();

        $questionnaireV2 = $this->createDraftQuestionnaire(
            'Kuesioner V2',
            [
                ['Pertanyaan baru setelah publish?', 4],
                ['Apakah data v2 aktif?', 2],
            ],
        );

        $riskRuleV2 = $this->createDraftRiskRule(
            'Aturan V2',
            [
                ['Risiko Rendah', 'rendah', 0, 3, 'success'],
                ['Risiko Sedang', 'sedang', 4, 6, 'warning'],
                ['Risiko Tinggi', 'tinggi', 7, 99, 'danger'],
            ],
        );

        app(QuestionnairePublishingService::class)->publish($questionnaireV2, $publisher->id);
        app(RiskRulePublishingService::class)->publish($riskRuleV2, $publisher->id);

        $screening->refresh();

        $this->assertSame($questionnaireV1->id, $screening->questionnaire_version_id);
        $this->assertSame($riskRuleV1->id, $screening->risk_rule_version_id);

        $this->actingAs($user)
            ->get(route('screenings.questions.show', ['screening' => $screening, 'step' => 1]))
            ->assertOk()
            ->assertSee('Pertanyaan lama yang harus tetap terkunci?')
            ->assertDontSee('Pertanyaan baru setelah publish?');

        $newUser = User::factory()->create();

        $this->actingAs($newUser)->post(route('screenings.start'));

        $latestScreening = Screening::query()
            ->whereBelongsTo($newUser)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame($questionnaireV2->id, $latestScreening->questionnaire_version_id);
        $this->assertSame($riskRuleV2->id, $latestScreening->risk_rule_version_id);
    }

    public function test_submit_requires_every_active_question_to_be_answered(): void
    {
        $user = User::factory()->create();
        $this->createPublishedInstrument();

        $this->actingAs($user)->post(route('screenings.start'));
        $screening = Screening::query()->sole();

        $this->actingAs($user)->put(
            route('screenings.questions.update', ['screening' => $screening, 'step' => 1]),
            ['answer' => 'yes'],
        );

        $this->actingAs($user)
            ->from(route('screenings.review', $screening))
            ->post(route('screenings.submit', $screening), [
                'submission_key' => $screening->submission_key,
            ])
            ->assertRedirect(route('screenings.review', $screening))
            ->assertSessionHasErrors('screening');

        $screening->refresh();

        $this->assertSame(ScreeningStatus::InProgress, $screening->status);
        $this->assertDatabaseCount('screening_answers', 0);
    }

    public function test_server_side_score_and_risk_label_ignore_browser_submitted_values(): void
    {
        $user = User::factory()->create();
        $this->createPublishedInstrument(
            questions: [
                ['Apakah gejala A muncul?', 2],
                ['Apakah gejala B muncul?', 6],
            ],
            riskLevels: [
                ['Risiko Rendah', 'rendah', 0, 2, 'success'],
                ['Risiko Sedang', 'sedang', 3, 5, 'warning'],
                ['Risiko Tinggi', 'tinggi', 6, 99, 'danger'],
            ],
        );

        $this->actingAs($user)->post(route('screenings.start'));
        $screening = Screening::query()->sole();

        $this->actingAs($user)->put(
            route('screenings.questions.update', ['screening' => $screening, 'step' => 1]),
            ['answer' => 'yes', 'awarded_score' => 99],
        );

        $this->actingAs($user)->put(
            route('screenings.questions.update', ['screening' => $screening, 'step' => 2]),
            ['answer' => 'no', 'awarded_score' => 99],
        );

        $this->actingAs($user)
            ->post(route('screenings.submit', $screening), [
                'submission_key' => $screening->submission_key,
                'total_score' => 88,
                'risk_label_snapshot' => 'Direkayasa Browser',
            ])
            ->assertRedirect(route('screenings.result', $screening));

        $screening->refresh();
        $screening->load('answers');

        $this->assertSame(ScreeningStatus::Completed, $screening->status);
        $this->assertSame(2, $screening->total_score);
        $this->assertSame(8, $screening->max_score);
        $this->assertSame('Risiko Rendah', $screening->risk_label_snapshot);
        $this->assertSame([2, 0], $screening->answers->pluck('awarded_score')->all());
    }

    public function test_scrollable_questionnaire_can_store_multiple_answers_in_one_request(): void
    {
        $user = User::factory()->create();
        $this->createPublishedInstrument(
            questions: [
                ['Apakah gejala A muncul?', 2],
                ['Apakah gejala B muncul?', 3],
            ],
        );

        $this->actingAs($user)->post(route('screenings.start'));
        $screening = Screening::query()->sole();
        $questions = app(ScreeningService::class)->questionsFor($screening)->values();

        $this->actingAs($user)
            ->put(route('screenings.questions.update', ['screening' => $screening, 'step' => 1]), [
                'answers' => [
                    $questions[0]->id => 'yes',
                    $questions[1]->id => 'no',
                ],
            ])
            ->assertRedirect(route('screenings.review', $screening));

        $this->actingAs($user)
            ->post(route('screenings.submit', $screening), [
                'submission_key' => $screening->submission_key,
            ])
            ->assertRedirect(route('screenings.result', $screening));

        $screening->refresh();

        $this->assertSame(ScreeningStatus::Completed, $screening->status);
        $this->assertSame(2, $screening->total_score);
    }

    public function test_duplicate_submission_is_idempotent_and_completed_screening_remains_immutable(): void
    {
        $user = User::factory()->create();
        $this->createPublishedInstrument();

        $screening = $this->completeScreeningForUser($user, ['yes', 'no']);

        $this->actingAs($user)
            ->post(route('screenings.submit', $screening), [
                'submission_key' => $screening->submission_key,
            ])
            ->assertRedirect(route('screenings.result', $screening));

        $this->assertDatabaseCount('screening_answers', 2);

        $originalScores = $screening->fresh()->answers()->pluck('awarded_score')->all();

        $this->actingAs($user)
            ->put(route('screenings.questions.update', ['screening' => $screening, 'step' => 1]), [
                'answer' => 'yes',
            ])
            ->assertRedirect(route('screenings.result', $screening));

        $screening->refresh();

        $this->assertSame(ScreeningStatus::Completed, $screening->status);
        $this->assertSame($originalScores, $screening->answers()->pluck('awarded_score')->all());
    }

    public function test_completed_screening_preserves_historical_snapshots_after_new_publication(): void
    {
        $user = User::factory()->create();
        $publisher = User::factory()->create(['role' => UserRole::Admin]);

        $this->createPublishedInstrument(
            questionnaireTitle: 'Kuesioner Snapshot V1',
            riskRuleTitle: 'Aturan Snapshot V1',
            questions: [
                ['Pertanyaan snapshot lama?', 2],
                ['Pertanyaan pendukung lama?', 3],
            ],
        );

        $screening = $this->completeScreeningForUser($user, ['yes', 'no']);

        $questionnaireV2 = $this->createDraftQuestionnaire(
            'Kuesioner Snapshot V2',
            [
                ['Pertanyaan snapshot baru?', 4],
                ['Pertanyaan pendukung baru?', 1],
            ],
        );

        $riskRuleV2 = $this->createDraftRiskRule(
            'Aturan Snapshot V2',
            [
                ['Risiko Rendah', 'rendah', 0, 1, 'success'],
                ['Risiko Sedang', 'sedang', 2, 4, 'warning'],
                ['Risiko Tinggi', 'tinggi', 5, 99, 'danger'],
            ],
        );

        app(QuestionnairePublishingService::class)->publish($questionnaireV2, $publisher->id);
        app(RiskRulePublishingService::class)->publish($riskRuleV2, $publisher->id);

        $screening->refresh();
        $screening->load('answers');

        $this->assertSame('Kuesioner Snapshot V1', $screening->questionnaire_version_name_snapshot);
        $this->assertSame('Aturan Snapshot V1', $screening->risk_rule_version_name_snapshot);
        $this->assertSame('Pertanyaan snapshot lama?', $screening->answers->first()->question_text_snapshot);
    }

    public function test_users_cannot_access_other_user_screenings(): void
    {
        $progressOwner = User::factory()->create();
        $completedOwner = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->createPublishedInstrument();

        $this->actingAs($progressOwner)->post(route('screenings.start'));
        $inProgressScreening = Screening::query()->sole();

        $completedScreening = $this->completeScreeningForUser($completedOwner, ['yes', 'yes']);

        $this->actingAs($otherUser)
            ->get(route('screenings.questions.show', ['screening' => $inProgressScreening, 'step' => 1]))
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->get(route('screenings.result', $completedScreening))
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->get(route('history.show', $completedScreening))
            ->assertForbidden();
    }

    public function test_submission_rolls_back_when_failure_happens_inside_transaction(): void
    {
        Session::start();

        $user = User::factory()->create();
        $this->createPublishedInstrument();

        $service = app(ScreeningService::class);
        $screening = $service->startForUser($user);

        foreach ($service->questionsFor($screening) as $question) {
            $service->storeAnswer($screening, $question, 'yes');
        }

        $this->mock(PregnancyAgeService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('calculateFromHpht')
                ->once()
                ->andThrow(new RuntimeException('Simulasi gagal di tengah transaksi.'));
        });

        try {
            app(ScreeningService::class)->submit($screening->fresh(), $screening->submission_key);
            $this->fail('Submit seharusnya gagal.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulasi gagal di tengah transaksi.', $exception->getMessage());
        }

        $screening->refresh();

        $this->assertSame(ScreeningStatus::InProgress, $screening->status);
        $this->assertDatabaseCount('screening_answers', 0);
        $this->assertSame(0, $screening->total_score);
    }

    /**
     * @param  array<int, array{0:string,1:int}>  $questions
     * @param  array<int, array{0:string,1:string,2:int,3:int,4:string}>  $riskLevels
     * @return array{0:QuestionnaireVersion,1:RiskRuleVersion}
     */
    protected function createPublishedInstrument(
        string $questionnaireTitle = 'Instrumen Demo',
        string $riskRuleTitle = 'Aturan Demo',
        array $questions = [
            ['Apakah gejala pertama muncul?', 2],
            ['Apakah gejala kedua muncul?', 3],
        ],
        array $riskLevels = [
            ['Risiko Rendah', 'rendah', 0, 2, 'success'],
            ['Risiko Sedang', 'sedang', 3, 4, 'warning'],
            ['Risiko Tinggi', 'tinggi', 5, 99, 'danger'],
        ],
    ): array {
        $questionnaire = QuestionnaireVersion::query()->create([
            'version_number' => 1,
            'title' => $questionnaireTitle,
            'status' => VersionStatus::Published,
            'published_at' => now()->subMinute(),
            'max_score_snapshot' => array_sum(array_column($questions, 1)),
            'is_demo_data' => true,
            'medical_approval_required' => true,
        ]);

        foreach ($questions as $index => $questionData) {
            Question::query()->create([
                'questionnaire_version_id' => $questionnaire->id,
                'text' => $questionData[0],
                'score_yes' => $questionData[1],
                'score_no' => 0,
                'display_order' => $index + 1,
                'is_active' => true,
            ]);
        }

        $riskRule = RiskRuleVersion::query()->create([
            'version_number' => 1,
            'title' => $riskRuleTitle,
            'status' => VersionStatus::Published,
            'published_at' => now()->subMinute(),
            'max_score_covered' => $riskLevels[array_key_last($riskLevels)][3],
            'is_demo_data' => true,
            'medical_approval_required' => true,
        ]);

        foreach ($riskLevels as $index => $riskLevelData) {
            RiskLevel::query()->create([
                'risk_rule_version_id' => $riskRule->id,
                'name' => $riskLevelData[0],
                'slug' => $riskLevelData[1],
                'min_score' => $riskLevelData[2],
                'max_score' => $riskLevelData[3],
                'semantic_color' => $riskLevelData[4],
                'description' => 'DEMO DATA - NOT FOR MEDICAL USE',
                'recommendation' => 'DEMO DATA - NOT FOR MEDICAL USE',
                'display_priority' => $index + 1,
                'is_active' => true,
            ]);
        }

        return [$questionnaire, $riskRule];
    }

    /**
     * @param  array<int, array{0:string,1:int}>  $questions
     */
    protected function createDraftQuestionnaire(
        string $title,
        array $questions = [
            ['Pertanyaan draft?', 2],
            ['Pertanyaan draft kedua?', 2],
        ],
    ): QuestionnaireVersion {
        $questionnaire = QuestionnaireVersion::query()->create([
            'version_number' => 2,
            'title' => $title,
            'status' => VersionStatus::Draft,
            'is_demo_data' => true,
            'medical_approval_required' => true,
        ]);

        foreach ($questions as $index => $questionData) {
            Question::query()->create([
                'questionnaire_version_id' => $questionnaire->id,
                'text' => $questionData[0],
                'score_yes' => $questionData[1],
                'score_no' => 0,
                'display_order' => $index + 1,
                'is_active' => true,
            ]);
        }

        return $questionnaire;
    }

    /**
     * @param  array<int, array{0:string,1:string,2:int,3:int,4:string}>  $riskLevels
     */
    protected function createDraftRiskRule(
        string $title,
        array $riskLevels = [
            ['Risiko Rendah', 'rendah', 0, 3, 'success'],
            ['Risiko Sedang', 'sedang', 4, 6, 'warning'],
            ['Risiko Tinggi', 'tinggi', 7, 99, 'danger'],
        ],
    ): RiskRuleVersion {
        $riskRule = RiskRuleVersion::query()->create([
            'version_number' => 2,
            'title' => $title,
            'status' => VersionStatus::Draft,
            'is_demo_data' => true,
            'medical_approval_required' => true,
        ]);

        foreach ($riskLevels as $index => $riskLevelData) {
            RiskLevel::query()->create([
                'risk_rule_version_id' => $riskRule->id,
                'name' => $riskLevelData[0],
                'slug' => $riskLevelData[1],
                'min_score' => $riskLevelData[2],
                'max_score' => $riskLevelData[3],
                'semantic_color' => $riskLevelData[4],
                'description' => 'DEMO DATA - NOT FOR MEDICAL USE',
                'recommendation' => 'DEMO DATA - NOT FOR MEDICAL USE',
                'display_priority' => $index + 1,
                'is_active' => true,
            ]);
        }

        return $riskRule;
    }

    /**
     * @param  array<int, string>  $answers
     */
    protected function completeScreeningForUser(User $user, array $answers): Screening
    {
        $this->actingAs($user)->post(route('screenings.start'));

        $screening = Screening::query()
            ->whereBelongsTo($user)
            ->latest('id')
            ->firstOrFail();

        foreach (array_values($answers) as $index => $answer) {
            $this->actingAs($user)->put(
                route('screenings.questions.update', [
                    'screening' => $screening,
                    'step' => $index + 1,
                ]),
                ['answer' => $answer],
            );
        }

        $this->actingAs($user)->post(route('screenings.submit', $screening), [
            'submission_key' => $screening->submission_key,
        ]);

        return $screening->fresh();
    }
}
