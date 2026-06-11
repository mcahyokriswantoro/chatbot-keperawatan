<?php

namespace App\Console\Commands;

use App\Services\AdminAccessService;
use Illuminate\Console\Command;

class MakeAdminUser extends Command
{
    protected $signature = 'make:admin {email : Email akun yang akan dijadikan admin}';

    protected $description = 'Jadikan user terdaftar sebagai admin (akses panel /admin)';

    public function handle(AdminAccessService $access): int
    {
        $email = $this->argument('email');

        try {
            $user = $access->grantByEmail($email);
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("{$user->name} ({$email}) siap akses admin. Login via /login lalu buka /admin.");

        return self::SUCCESS;
    }
}
