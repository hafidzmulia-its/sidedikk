<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScreeningAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answer' => ['nullable', 'string', 'in:yes,no'],
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'string', 'in:yes,no'],
        ];
    }

    public function messages(): array
    {
        return [
            'answer.required' => 'Pertanyaan ini belum dijawab.',
            'answer.in' => 'Jawaban yang dipilih tidak valid.',
            'answers.array' => 'Format jawaban tidak valid.',
            'answers.*.in' => 'Jawaban yang dipilih tidak valid.',
        ];
    }
}
