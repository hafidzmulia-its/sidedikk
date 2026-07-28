<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAdminUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_command_creates_administrator_account(): void
    {
        $this->artisan('app:create-admin', [
            '--name' => 'Admin Demo',
            '--email' => 'admin@example.com',
            '--password' => 'password',
            '--age' => 30,
        ])->assertExitCode(0);

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame(UserRole::Admin, $admin->role);
        $this->assertSame(30, $admin->age);
    }
}
