<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class CriarAdministrador extends Command
{
    protected $signature = 'app:criar-administrador';
    protected $description = 'Cria a primeira conta administradora do sistema';

    public function handle(): int
    {
        if (User::query()->where('role', 'admin')->exists()) {
            $this->error('Já existe uma conta administradora.');
            return self::FAILURE;
        }

        $name = $this->ask('Nome do administrador');
        $email = $this->ask('E-mail');
        $password = $this->secret('Senha (mínimo de 6 caracteres)');

        if (! $name || ! filter_var($email, FILTER_VALIDATE_EMAIL) || ! $password) {
            $this->error('Informe nome, e-mail válido e uma senha.');
            return self::FAILURE;
        }

        $validation = Validator::make(['password' => $password], ['password' => Password::default()]);
        if ($validation->fails()) {
            $this->error($validation->errors()->first('password'));
            return self::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->error('Este e-mail já está cadastrado.');
            return self::FAILURE;
        }

        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
        ]);

        $this->info('Conta administradora criada. Acesse /admin para entrar.');
        return self::SUCCESS;
    }
}
