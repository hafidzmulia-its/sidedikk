<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(CoreDataSeeder::class);

        if (filter_var((string) env('SIDEDIKK_SEED_DEMO_USER', false), FILTER_VALIDATE_BOOL)) {
            User::factory()->create([
                'name' => 'Pengguna Demo',
                'email' => 'demo@sidedikk.test',
            ]);
        }

        $this->call(AdminUserSeeder::class);
    }
}
