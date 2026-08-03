<?php

namespace Tests\Unit;

use App\Enums\ScreeningStatus;
use App\Enums\UserRole;
use App\Models\Screening;
use App\Models\User;
use App\Policies\ScreeningPolicy;
use PHPUnit\Framework\TestCase;

class ScreeningPolicyTest extends TestCase
{
    public function test_owner_can_answer_when_screening_user_id_is_loaded_as_string(): void
    {
        $user = new User();
        $user->forceFill([
            'id' => 1,
            'role' => UserRole::User,
        ]);

        $screening = new Screening([
            'user_id' => '1',
            'status' => ScreeningStatus::InProgress->value,
        ]);

        $policy = new ScreeningPolicy();

        $this->assertTrue($policy->answer($user, $screening));
        $this->assertTrue($policy->view($user, $screening));
        $this->assertTrue($policy->submit($user, $screening));
    }
}
