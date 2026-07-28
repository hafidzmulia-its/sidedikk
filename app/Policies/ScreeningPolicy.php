<?php

namespace App\Policies;

use App\Enums\ScreeningStatus;
use App\Enums\UserRole;
use App\Models\Screening;
use App\Models\User;

class ScreeningPolicy
{
    public function view(User $user, Screening $screening): bool
    {
        return $user->role === UserRole::Admin || $screening->user_id === $user->id;
    }

    public function answer(User $user, Screening $screening): bool
    {
        return $screening->user_id === $user->id
            && $screening->status === ScreeningStatus::InProgress;
    }

    public function review(User $user, Screening $screening): bool
    {
        return $this->answer($user, $screening);
    }

    public function submit(User $user, Screening $screening): bool
    {
        return $screening->user_id === $user->id;
    }
}
