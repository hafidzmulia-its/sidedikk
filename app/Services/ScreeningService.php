<?php

namespace App\Services;

use App\Enums\ScreeningStatus;
use App\Models\Question;
use App\Models\QuestionnaireVersion;
use App\Models\RiskRuleVersion;
use App\Models\Screening;
use App\Models\ScreeningAnswer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use LogicException;

class ScreeningService
{
    public function __construct(
        private readonly PregnancyAgeService $pregnancyAgeService,
        private readonly RiskClassificationService $riskClassificationService,
        private readonly RiskRuleValidationService $riskRuleValidationService,
    ) {}

    public function startForUser(User $user): Screening
    {
        $existingScreening = Screening::query()
            ->whereBelongsTo($user)
            ->inProgress()
            ->latest('started_at')
            ->first();

        if ($existingScreening) {
            return $existingScreening;
        }

        $questionnaireVersion = QuestionnaireVersion::query()
            ->published()
            ->latest('published_at')
            ->first();

        if (! $questionnaireVersion) {
            throw new LogicException('Skrining belum tersedia. Silakan hubungi admin.');
        }

        if (! $questionnaireVersion->questions()->active()->exists()) {
            throw new LogicException('Kuesioner aktif belum memiliki pertanyaan.');
        }

        $riskRuleVersion = RiskRuleVersion::query()
            ->published()
            ->latest('published_at')
            ->first();

        if (! $riskRuleVersion) {
            throw new LogicException('Aturan risiko belum tersedia. Silakan hubungi admin.');
        }

        $maxScore = (int) $questionnaireVersion->questions()->active()->sum('score_yes');
        $this->riskRuleValidationService->assertCoverage($riskRuleVersion, $maxScore);

        return Screening::query()->create([
            'user_id' => $user->id,
            'questionnaire_version_id' => $questionnaireVersion->id,
            'risk_rule_version_id' => $riskRuleVersion->id,
            'status' => ScreeningStatus::InProgress,
            'submission_key' => (string) Str::uuid(),
            'started_at' => now(),
            'questionnaire_version_name_snapshot' => $questionnaireVersion->title,
            'risk_rule_version_name_snapshot' => $riskRuleVersion->title,
        ]);
    }

    /**
     * @return EloquentCollection<int, Question>
     */
    public function questionsFor(Screening $screening): EloquentCollection
    {
        return Question::query()
            ->where('questionnaire_version_id', $screening->questionnaire_version_id)
            ->active()
            ->orderBy('display_order')
            ->get();
    }

    public function nextStepFor(Screening $screening): ?int
    {
        $questions = $this->questionsFor($screening)->values();
        $answers = $this->answersFor($screening);

        foreach ($questions as $index => $question) {
            if (! array_key_exists($question->id, $answers)) {
                return $index + 1;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    public function answersFor(Screening $screening): array
    {
        return Session::get($this->sessionKey($screening), []);
    }

    public function answerFor(Screening $screening, int $questionId): ?string
    {
        return $this->answersFor($screening)[$questionId] ?? null;
    }

    public function storeAnswer(Screening $screening, Question $question, ?string $answer): void
    {
        if (! in_array($answer, ['yes', 'no'], true)) {
            throw ValidationException::withMessages([
                'answer' => 'Pertanyaan ini belum dijawab.',
            ]);
        }

        $answers = $this->answersFor($screening);
        $answers[$question->id] = $answer;
        Session::put($this->sessionKey($screening), $answers);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function reviewData(Screening $screening): Collection
    {
        $answers = $this->answersFor($screening);

        return $this->questionsFor($screening)->map(function (Question $question) use ($answers): array {
            return [
                'question' => $question,
                'answer' => $answers[$question->id] ?? null,
            ];
        });
    }

    public function ensureAllQuestionsAnswered(Screening $screening): void
    {
        $questions = $this->questionsFor($screening);
        $answers = $this->answersFor($screening);

        $missing = $questions->first(fn (Question $question) => ! array_key_exists($question->id, $answers));

        if ($missing) {
            throw ValidationException::withMessages([
                'screening' => 'Semua pertanyaan aktif wajib dijawab sebelum hasil dikirim.',
            ]);
        }
    }

    public function submit(Screening $screening, string $submissionKey): Screening
    {
        if ($screening->status === ScreeningStatus::Completed) {
            return $screening->fresh(['answers', 'user']);
        }

        if ($screening->submission_key !== $submissionKey) {
            throw ValidationException::withMessages([
                'submission_key' => 'Kunci pengiriman tidak valid.',
            ]);
        }

        $questions = $this->questionsFor($screening);
        $answers = $this->answersFor($screening);

        $this->ensureAllQuestionsAnswered($screening);

        return DB::transaction(function () use ($screening, $questions, $answers): Screening {
            /** @var Screening $lockedScreening */
            $lockedScreening = Screening::query()
                ->with(['user', 'riskRuleVersion.riskLevels'])
                ->lockForUpdate()
                ->findOrFail($screening->id);

            if ($lockedScreening->status === ScreeningStatus::Completed) {
                return $lockedScreening->fresh(['answers']);
            }

            $totalScore = 0;
            $maxScore = 0;
            $answerRows = [];

            foreach ($questions as $question) {
                $selectedAnswer = $answers[$question->id] ?? null;

                if (! in_array($selectedAnswer, ['yes', 'no'], true)) {
                    throw ValidationException::withMessages([
                        'screening' => 'Semua pertanyaan aktif wajib dijawab sebelum hasil dikirim.',
                    ]);
                }

                $awardedScore = $selectedAnswer === 'yes'
                    ? (int) $question->score_yes
                    : (int) $question->score_no;

                $totalScore += $awardedScore;
                $maxScore += (int) $question->score_yes;

                $answerRows[] = [
                    'screening_id' => $lockedScreening->id,
                    'question_id' => $question->id,
                    'questionnaire_version_id' => $question->questionnaire_version_id,
                    'question_text_snapshot' => $question->text,
                    'selected_answer' => $selectedAnswer,
                    'awarded_score' => $awardedScore,
                    'display_order_snapshot' => $question->display_order,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $riskLevel = $this->riskClassificationService->classify(
                $totalScore,
                $lockedScreening->riskRuleVersion,
            );

            if (! $riskLevel) {
                throw new LogicException('Aturan risiko tidak mencakup skor yang dihasilkan.');
            }

            ScreeningAnswer::query()->where('screening_id', $lockedScreening->id)->delete();
            ScreeningAnswer::query()->insert($answerRows);

            $pregnancyAge = $this->pregnancyAgeService->calculateFromHpht(
                $lockedScreening->user->hpht_date?->toDateString(),
            );

            $lockedScreening->forceFill([
                'status' => ScreeningStatus::Completed,
                'completed_at' => now(),
                'gestational_age_weeks_snapshot' => $pregnancyAge['weeks'],
                'gestational_age_days_snapshot' => $pregnancyAge['days'],
                'total_score' => $totalScore,
                'max_score' => $maxScore,
                'risk_label_snapshot' => $riskLevel->name,
                'risk_description_snapshot' => $riskLevel->description,
                'recommendation_snapshot' => $riskLevel->recommendation,
            ])->save();

            Session::forget($this->sessionKey($screening));

            return $lockedScreening->fresh(['answers', 'questionnaireVersion', 'riskRuleVersion']);
        });
    }

    public function sessionKey(Screening $screening): string
    {
        return 'screenings.answers.'.$screening->id;
    }
}
