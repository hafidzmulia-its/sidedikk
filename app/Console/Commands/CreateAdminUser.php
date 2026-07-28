<?php

namespace App\Console\Commands;

use App\Actions\UpsertAdminUser;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin
        {--name= : Nama administrator}
        {--email= : Email administrator}
        {--password= : Password administrator}
        {--age=25 : Umur administrator}';

    protected $description = 'Create or update an administrator account without exposing credentials in source code.';

    public function handle(): int
    {
        $name = $this->option('name') ?: env('SIDEDIKK_ADMIN_NAME');
        $email = $this->option('email') ?: env('SIDEDIKK_ADMIN_EMAIL');
        $password = $this->option('password') ?: env('SIDEDIKK_ADMIN_PASSWORD');
        $age = (int) ($this->option('age') ?: 25);

        if (! $name || ! $email || ! $password) {
            $this->components->error('Nama, email, dan password admin wajib disediakan melalui opsi command atau environment.');

            return self::FAILURE;
        }

        $user = app(UpsertAdminUser::class)->handle([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'age' => $age,
        ]);

        $this->components->info("Administrator siap: {$user->email}");

        return self::SUCCESS;
    }
}
