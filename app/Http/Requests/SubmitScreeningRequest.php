<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitScreeningRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'submission_key' => ['required', 'string', 'uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'submission_key.required' => 'Kunci pengiriman tidak tersedia.',
            'submission_key.uuid' => 'Kunci pengiriman tidak valid.',
        ];
    }
}
