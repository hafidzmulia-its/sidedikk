<?php

namespace Database\Seeders;

use App\Actions\UpsertAdminUser;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (! filter_var((string) env('SIDEDIKK_SEED_ADMIN', false), FILTER_VALIDATE_BOOL)) {
            return;
        }

        $name = env('SIDEDIKK_ADMIN_NAME');
        $email = env('SIDEDIKK_ADMIN_EMAIL');
        $password = env('SIDEDIKK_ADMIN_PASSWORD');

        if (! $name || ! $email || ! $password) {
            $this->command?->warn('SIDEDIKK_SEED_ADMIN aktif, tetapi SIDEDIKK_ADMIN_NAME, SIDEDIKK_ADMIN_EMAIL, atau SIDEDIKK_ADMIN_PASSWORD belum lengkap.');

            return;
        }

        app(UpsertAdminUser::class)->handle([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'age' => (int) env('SIDEDIKK_ADMIN_AGE', 25),
        ]);

        $this->command?->info("Admin seed siap: {$email}");
    }
}
