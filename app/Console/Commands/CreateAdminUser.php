<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\password;

class CreateAdminUser extends Command
{
    protected $signature = 'users:create {name : Nome dell\'utente} {email : Email di accesso}';

    protected $description = 'Crea un utente che può accedere alla gestione degli iscritti';

    public function handle(): int
    {
        $email = (string) $this->argument('email');

        if (User::where('email', $email)->exists()) {
            $this->error("Esiste già un utente con email {$email}.");

            return self::FAILURE;
        }

        $secret = password('Password', required: true);

        User::create([
            'name' => (string) $this->argument('name'),
            'email' => $email,
            'password' => $secret,
        ]);

        $this->info("Utente {$email} creato.");

        return self::SUCCESS;
    }
}
