<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'age' => ['required', 'integer', 'between:15,60'],
            'hpht_date' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.now()->subDays(294)->toDateString()],
            'password' => ['required', 'confirmed', Password::defaults()],
            'privacy_consent' => ['accepted'],
            'medical_disclaimer_consent' => ['accepted'],
        ];
    }
}
