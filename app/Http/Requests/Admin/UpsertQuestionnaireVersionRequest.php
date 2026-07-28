<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpsertQuestionnaireVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.text' => ['required', 'string'],
            'questions.*.help_text' => ['nullable', 'string'],
            'questions.*.score_yes' => ['required', 'integer', 'min:0', 'max:20'],
            'questions.*.score_no' => ['required', 'integer', 'min:0', 'max:20'],
            'questions.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $questions = collect($this->input('questions', []))
                    ->filter(fn (mixed $question): bool => is_array($question));

                $hasActiveQuestion = $questions->contains(
                    fn (array $question): bool => (bool) ($question['is_active'] ?? true)
                );

                if (! $hasActiveQuestion) {
                    $validator->errors()->add('questions', 'Minimal satu pertanyaan aktif wajib tersedia.');
                }
            },
        ];
    }
}
