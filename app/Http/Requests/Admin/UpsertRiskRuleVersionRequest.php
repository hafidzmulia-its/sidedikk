<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

class UpsertRiskRuleVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'is_demo_data' => ['nullable', 'boolean'],
            'medical_approval_required' => ['nullable', 'boolean'],
            'risk_levels' => ['required', 'array', 'min:1'],
            'risk_levels.*.name' => ['required', 'string', 'max:255'],
            'risk_levels.*.slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/'],
            'risk_levels.*.min_score' => ['required', 'integer', 'min:0'],
            'risk_levels.*.max_score' => ['required', 'integer', 'min:0'],
            'risk_levels.*.semantic_color' => ['required', 'string', 'in:success,warning,danger,info,primary'],
            'risk_levels.*.description' => ['required', 'string'],
            'risk_levels.*.recommendation' => ['required', 'string'],
            'risk_levels.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $levels = collect($this->input('risk_levels', []))
                    ->filter(fn (mixed $level): bool => is_array($level))
                    ->sortBy(fn (array $level): int => (int) ($level['min_score'] ?? 0))
                    ->values();

                if ($levels->isEmpty()) {
                    return;
                }

                if ($levels->pluck('slug')->duplicates()->isNotEmpty()) {
                    $validator->errors()->add('risk_levels', 'Slug level risiko wajib unik dalam satu versi.');
                }

                $this->validateContiguousRanges($levels, $validator);
            },
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $levels
     */
    protected function validateContiguousRanges(Collection $levels, Validator $validator): void
    {
        $expectedMin = 0;

        foreach ($levels as $index => $level) {
            $minScore = (int) ($level['min_score'] ?? 0);
            $maxScore = (int) ($level['max_score'] ?? 0);

            if ($maxScore < $minScore) {
                $validator->errors()->add(
                    "risk_levels.$index.max_score",
                    'Nilai skor maksimum tidak boleh lebih kecil dari skor minimum.'
                );
            }

            if ($minScore !== $expectedMin) {
                $validator->errors()->add(
                    'risk_levels',
                    'Rentang level risiko wajib berurutan, dimulai dari 0, dan tidak boleh memiliki celah.'
                );

                break;
            }

            $expectedMin = $maxScore + 1;
        }
    }
}
