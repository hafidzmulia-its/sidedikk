<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'age' => 27,
            'hpht_date' => now()->subDays(70)->toDateString(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'privacy_consent' => '1',
            'medical_disclaimer_consent' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();

        $this->assertSame(UserRole::User, $user->role);
        $this->assertSame(27, $user->age);
        $this->assertNotNull($user->hpht_date);
        $this->assertNotNull($user->privacy_consent_at);
        $this->assertNotNull($user->medical_disclaimer_consent_at);
    }

    public function test_registration_requires_required_consents(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'age' => 27,
            'hpht_date' => now()->subDays(70)->toDateString(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'privacy_consent',
            'medical_disclaimer_consent',
        ]);
    }
}
