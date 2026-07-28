<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'age',
    'hpht_date',
    'pregnancy_updated_at',
    'privacy_consent_at',
    'medical_disclaimer_consent_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'hpht_date' => 'date',
            'pregnancy_updated_at' => 'datetime',
            'privacy_consent_at' => 'datetime',
            'medical_disclaimer_consent_at' => 'datetime',
        ];
    }

    #[Computed]
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function screenings(): HasMany
    {
        return $this->hasMany(Screening::class);
    }
}
