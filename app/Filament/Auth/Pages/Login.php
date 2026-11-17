<?php

namespace App\Filament\Auth\Pages;

use App\Models\Aluno;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Radio::make('tipo')->label('Acessar como')
                ->options(['equipe' => 'Equipe escolar', 'aluno' => 'Aluno'])
                ->default('equipe')->required()->in(['equipe', 'aluno'])->live(),
            $this->getEmailFormComponent()->visible(fn (Get $get) => $get('tipo') !== 'aluno'),
            $this->getPasswordFormComponent()->visible(fn (Get $get) => $get('tipo') !== 'aluno'),
            TextInput::make('cpf')->label('CPF')->mask('999.999.999-99')
                ->stripCharacters(['.', '-'])->length(11)->required()
                ->visible(fn (Get $get) => $get('tipo') === 'aluno'),
            DatePicker::make('data_nascimento')->label('Data de nascimento')
                ->required()->maxDate(today())->displayFormat('d/m/Y')
                ->visible(fn (Get $get) => $get('tipo') === 'aluno'),
            $this->getRememberFormComponent(),
        ]);
    }

    public function authenticate(): ?LoginResponse
    {
        if (($this->data['tipo'] ?? 'equipe') !== 'aluno') {
            return parent::authenticate();
        }

        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        $user = DB::transaction(function () use ($data): User {
            $aluno = Aluno::query()
                ->where('cpf', preg_replace('/\D/', '', $data['cpf']))
                ->whereDate('data_nascimento', $data['data_nascimento'])
                ->lockForUpdate()->first();

            if (! $aluno) {
                $this->failAlunoLogin();
            }

            $user = User::query()->where('aluno_id', $aluno->id)->first();

            if ($user && ! $user->isAluno()) {
                $this->failAlunoLogin();
            }

            $user ??= User::create([
                'name' => $aluno->nome,
                'email' => Str::uuid().'@alunos.invalid',
                'password' => Str::random(64),
                'role' => 'aluno',
                'aluno_id' => $aluno->id,
            ]);

            if (! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
                $this->failAlunoLogin();
            }

            return $user;
        });

        Filament::auth()->login($user, $data['remember'] ?? false);
        session()->regenerate();

        return app(LoginResponse::class);
    }

    private function failAlunoLogin(): never
    {
        throw ValidationException::withMessages([
            'data.cpf' => 'CPF ou data de nascimento inválidos. Confira os dados informados ou procure a escola.',
        ]);
    }
}
