<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScreeningAnswerRequest;
use App\Http\Requests\SubmitScreeningRequest;
use App\Models\Screening;
use App\Services\ScreeningService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use LogicException;

class ScreeningController extends Controller
{
    public function __construct(private readonly ScreeningService $screeningService) {}

    public function start(Request $request): RedirectResponse
    {
        try {
            $screening = $this->screeningService->startForUser($request->user());
        } catch (LogicException $exception) {
            return back()->withErrors([
                'screening' => $exception->getMessage(),
            ]);
        }

        $nextStep = $this->screeningService->nextStepFor($screening);

        if ($nextStep === null) {
            return $this->finalizeAnsweredScreening($screening);
        }

        return redirect()->route('screenings.questions.show', [
            'screening' => $screening,
            'step' => $nextStep,
        ]);
    }

    public function showQuestion(Request $request, Screening $screening, int $step): View|RedirectResponse
    {
        if ($screening->status->value === 'completed') {
            return redirect()->route('screenings.result', $screening);
        }

        $this->authorize('answer', $screening);

        $questions = $this->screeningService->questionsFor($screening)->values();
        abort_if($step < 1 || $step > $questions->count(), 404);

        $question = $questions[$step - 1];
        $answers = $this->screeningService->answersFor($screening);
        $answeredCount = $questions
            ->filter(fn ($item): bool => in_array($answers[$item->id] ?? null, ['yes', 'no'], true))
            ->count();

        return view('screenings.question', [
            'screening' => $screening->loadMissing(['questionnaireVersion', 'riskRuleVersion']),
            'question' => $question,
            'questions' => $questions,
            'answers' => $answers,
            'step' => $step,
            'totalSteps' => $questions->count(),
            'selectedAnswer' => $this->screeningService->answerFor($screening, $question->id),
            'answeredCount' => $answeredCount,
            'progressPercent' => (int) round(($answeredCount / max(1, $questions->count())) * 100),
        ]);
    }

    public function updateQuestion(StoreScreeningAnswerRequest $request, Screening $screening, int $step): RedirectResponse
    {
        if ($screening->status->value === 'completed') {
            return redirect()->route('screenings.result', $screening);
        }

        $this->authorize('answer', $screening);

        $questions = $this->screeningService->questionsFor($screening)->values();
        abort_if($step < 1 || $step > $questions->count(), 404);

        $question = $questions[$step - 1];
        $validated = $request->validated();
        $validatedAnswers = collect($validated['answers'] ?? []);
        $questionMap = $questions->keyBy(fn ($item) => (string) $item->id);

        $validatedAnswers
            ->filter(fn ($answer): bool => in_array($answer, ['yes', 'no'], true))
            ->each(function (string $answer, int|string $questionId) use ($screening, $questionMap): void {
                $matchedQuestion = $questionMap->get((string) $questionId);

                if ($matchedQuestion) {
                    $this->screeningService->storeAnswer($screening, $matchedQuestion, $answer);
                }
            });

        $currentAnswer = $validated['answer']
            ?? $validatedAnswers->get($question->id)
            ?? $validatedAnswers->get((string) $question->id);

        if (! in_array($currentAnswer, ['yes', 'no'], true)) {
            $errorKey = $validatedAnswers->isNotEmpty() ? "answers.{$question->id}" : 'answer';

            throw ValidationException::withMessages([
                $errorKey => 'Pertanyaan ini belum dijawab.',
            ]);
        }

        if (($validated['answer'] ?? null) !== null) {
            $this->screeningService->storeAnswer($screening, $question, $currentAnswer);
        }

        $nextStep = $this->screeningService->nextStepFor($screening);

        if ($nextStep === null) {
            return $this->finalizeAnsweredScreening($screening);
        }

        return redirect()->route('screenings.questions.show', [
            'screening' => $screening,
            'step' => $nextStep,
        ]);
    }

    public function review(Request $request, Screening $screening): View|RedirectResponse
    {
        if ($screening->status->value === 'completed') {
            return redirect()->route('history.show', $screening);
        }

        $this->authorize('review', $screening);

        $nextStep = $this->screeningService->nextStepFor($screening);

        if ($nextStep !== null) {
            return redirect()->route('screenings.questions.show', [
                'screening' => $screening,
                'step' => $nextStep,
            ]);
        }

        return $this->finalizeAnsweredScreening($screening);
    }

    public function submit(SubmitScreeningRequest $request, Screening $screening): RedirectResponse
    {
        $this->authorize('submit', $screening);

        try {
            $submittedScreening = $this->screeningService->submit(
                $screening,
                $request->validated('submission_key'),
            );
        } catch (LogicException|ValidationException $exception) {
            throw $exception;
        }

        return redirect()
            ->route('screenings.result', $submittedScreening)
            ->with('status', 'Hasil screening berhasil disimpan.');
    }

    public function result(Request $request, Screening $screening): View|RedirectResponse
    {
        $this->authorize('view', $screening);

        if ($screening->status->value !== 'completed') {
            $nextStep = $this->screeningService->nextStepFor($screening);

            if ($nextStep !== null) {
                return redirect()->route('screenings.questions.show', [
                    'screening' => $screening,
                    'step' => $nextStep,
                ]);
            }

            return $this->finalizeAnsweredScreening($screening);
        }

        return view('screenings.result', [
            'screening' => $screening->load([
                'answers',
                'questionnaireVersion',
                'riskRuleVersion',
            ]),
        ]);
    }

    protected function finalizeAnsweredScreening(Screening $screening): RedirectResponse
    {
        $submittedScreening = $this->screeningService->submit(
            $screening->fresh(),
            $screening->submission_key,
        );

        return redirect()
            ->route('screenings.result', $submittedScreening)
            ->with('status', 'Hasil screening berhasil disimpan.');
    }
}
