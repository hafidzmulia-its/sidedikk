<?php

namespace App\Http\Requests\Admin;

use App\Enums\EducationPostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpsertEducationPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'status' => ['required', 'string', 'in:'.implode(',', array_column(EducationPostStatus::cases(), 'value'))],
            'cover_image' => [
                'nullable',
                File::image()->max(4 * 1024),
            ],
        ];
    }
}
