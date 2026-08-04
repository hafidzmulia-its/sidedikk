<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VersionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertQuestionnaireVersionRequest;
use App\Models\QuestionnaireVersion;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QuestionnaireVersionController extends Controller
{
    public function index(): View
    {
        $questionnaire = $this->editableQuestionnaireQuery()
            ->with($this->questionnaireRelations())
            ->first();

        return view('admin.questionnaires.index', [
            'questionnaire' => $questionnaire,
            'summary' => $questionnaire
                ? $this->buildSummary($questionnaire->questions)
                : null,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        $existingQuestionnaire = $this->editableQuestionnaireQuery()->first();

        if ($existingQuestionnaire) {
            return redirect()
                ->route('admin.questionnaires.edit', $existingQuestionnaire)
                ->with('status', 'Kuesioner aktif sudah tersedia. Silakan ubah pertanyaannya langsung dari halaman edit.');
        }

        return $this->formView(
            new QuestionnaireVersion([
                'title' => 'Kuesioner Screening SIDEDIKK',
            ]),
            $this->defaultQuestionRows(),
            route('admin.questionnaires.store'),
            'POST',
            'Siapkan Kuesioner',
        );
    }

    public function store(
        UpsertQuestionnaireVersionRequest $request,
        AuditLogService $auditLogService,
    ): RedirectResponse {
        $existingQuestionnaire = $this->editableQuestionnaireQuery()->first();

        if ($existingQuestionnaire) {
            return redirect()
                ->route('admin.questionnaires.edit', $existingQuestionnaire)
                ->with('status', 'Kuesioner aktif sudah tersedia. Perubahan dapat dilakukan langsung pada set pertanyaan yang ada.');
        }

        $questionnaire = DB::transaction(function () use ($request): QuestionnaireVersion {
            $questionnaire = QuestionnaireVersion::query()->create([
                'version_number' => max(1, ((int) QuestionnaireVersion::query()->max('version_number')) + 1),
                'title' => $request->validated('title'),
                'status' => VersionStatus::Published,
                'published_at' => now(),
                'published_by' => $request->user()->id,
                'max_score_snapshot' => 0,
                'is_demo_data' => false,
                'medical_approval_required' => false,
            ]);

            $this->syncQuestions($questionnaire, $request->validated('questions'));

            return $questionnaire->fresh(['questions', 'publisher']);
        });

        $auditLogService->record(
            $request->user(),
            'admin.questionnaire.created',
            $questionnaire,
            ['title' => $questionnaire->title, 'status' => $questionnaire->status->value],
            $request->ip(),
        );

        return redirect()
            ->route('admin.questionnaires.show', $questionnaire)
            ->with('status', 'Kuesioner berhasil disimpan dan langsung aktif digunakan.');
    }

    public function show(QuestionnaireVersion $questionnaire): View
    {
        $questionnaire->load($this->questionnaireRelations());

        return view('admin.questionnaires.show', [
            'version' => $questionnaire,
            'summary' => $this->buildSummary($questionnaire->questions),
        ]);
    }

    public function edit(QuestionnaireVersion $questionnaire): View
    {
        $questionnaire->load($this->questionnaireRelations());

        return $this->formView(
            $questionnaire,
            $questionnaire->questions->map(fn ($question) => [
                'text' => $question->text,
                'help_text' => $question->help_text,
                'score_yes' => $question->score_yes,
                'score_no' => $question->score_no,
                'is_active' => $question->is_active,
            ])->values()->all(),
            route('admin.questionnaires.update', $questionnaire),
            'PUT',
            'Ubah Kuesioner',
        );
    }

    public function update(
        UpsertQuestionnaireVersionRequest $request,
        QuestionnaireVersion $questionnaire,
        AuditLogService $auditLogService,
    ): RedirectResponse {
        DB::transaction(function () use ($request, $questionnaire): void {
            $questionnaire->forceFill([
                'title' => $request->validated('title'),
                'status' => $questionnaire->status ?? VersionStatus::Published,
                'published_at' => $questionnaire->published_at ?? now(),
                'published_by' => $questionnaire->published_by ?? $request->user()->id,
                'is_demo_data' => false,
                'medical_approval_required' => false,
            ])->save();

            $this->syncQuestions($questionnaire, $request->validated('questions'));
        });

        $auditLogService->record(
            $request->user(),
            'admin.questionnaire.updated',
            $questionnaire,
            ['title' => $questionnaire->title],
            $request->ip(),
        );

        return redirect()
            ->route('admin.questionnaires.show', $questionnaire)
            ->with('status', 'Kuesioner berhasil diperbarui.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     */
    protected function syncQuestions(QuestionnaireVersion $version, array $questions): void
    {
        $version->questions()->delete();

        $maxScore = 0;

        foreach (array_values($questions) as $index => $question) {
            $isActive = (bool) ($question['is_active'] ?? false);
            $scoreYes = (int) $question['score_yes'];

            if ($isActive) {
                $maxScore += $scoreYes;
            }

            $version->questions()->create([
                'text' => $question['text'],
                'help_text' => $question['help_text'] ?: null,
                'score_yes' => $scoreYes,
                'score_no' => (int) $question['score_no'],
                'display_order' => $index + 1,
                'is_active' => $isActive,
            ]);
        }

        $version->forceFill([
            'max_score_snapshot' => $maxScore,
        ])->save();
    }

    /**
     * @return array{total_questions:int, active_questions:int, max_score:int}
     */
    protected function buildSummary(Collection $questions): array
    {
        $activeQuestions = $questions->where('is_active', true);

        return [
            'total_questions' => $questions->count(),
            'active_questions' => $activeQuestions->count(),
            'max_score' => (int) $activeQuestions->sum('score_yes'),
        ];
    }

    protected function editableQuestionnaireQuery(): Builder
    {
        return QuestionnaireVersion::query()
            ->whereIn('status', [VersionStatus::Published->value, VersionStatus::Draft->value])
            ->orderByRaw(
                'CASE
                    WHEN status = ? THEN 0
                    WHEN status = ? THEN 1
                    ELSE 2
                END',
                [VersionStatus::Published->value, VersionStatus::Draft->value],
            )
            ->latest('updated_at')
            ->latest('id');
    }

    /**
     * @return array<int, string>
     */
    protected function questionnaireRelations(): array
    {
        return ['questions', 'publisher'];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function defaultQuestionRows(): array
    {
        return [[
            'text' => '',
            'help_text' => '',
            'score_yes' => 0,
            'score_no' => 0,
            'is_active' => true,
        ]];
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     */
    protected function formView(
        QuestionnaireVersion $version,
        array $questions,
        string $formAction,
        string $formMethod,
        string $pageTitle,
    ): View {
        return view('admin.questionnaires.form', [
            'version' => $version,
            'questions' => $questions,
            'formAction' => $formAction,
            'formMethod' => $formMethod,
            'pageTitle' => $pageTitle,
        ]);
    }
}
