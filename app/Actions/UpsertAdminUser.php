<?php

namespace App\Actions;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpsertAdminUser
{
    /**
     * @param  array{name:string,email:string,password:string,age?:int}  $attributes
     */
    public function handle(array $attributes): User
    {
        $payload = [
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
            'age' => (int) ($attributes['age'] ?? 25),
        ];

        validator(
            $payload,
            [
                'name' => ['required', 'string', 'min:2', 'max:100'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', Password::defaults()],
                'age' => ['required', 'integer', 'between:15,60'],
            ],
        )->validate();

        return User::query()->updateOrCreate(
            ['email' => $payload['email']],
            [
                'name' => $payload['name'],
                'age' => $payload['age'],
                'role' => UserRole::Admin,
                'password' => Hash::make($payload['password']),
                'privacy_consent_at' => now(),
                'medical_disclaimer_consent_at' => now(),
                'hpht_date' => now()->toDateString(),
                'pregnancy_updated_at' => now(),
            ],
        );
    }
}
