<?php

namespace App\Filament\Auth\Pages;

use App\Models\Aluno;
use Filament\Auth\Pages\Register;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class RegisterAluno extends Register
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getNameFormComponent(),
            TextInput::make('matricula')->label('Matrícula escolar')->numeric()->minLength(7)->maxLength(7)->required(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
        ]);
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $aluno = Aluno::query()->where('matricula', $data['matricula'])->first();

        if (! $aluno || mb_strtolower(trim($aluno->nome)) !== mb_strtolower(trim($data['name']))) {
            throw ValidationException::withMessages([
                'data.matricula' => 'Não foi possível confirmar o aluno. Confira o nome completo e a matrícula cadastrados pela escola.',
            ]);
        }

        $data['name'] = $aluno->nome;
        $data['role'] = 'aluno';
        $data['aluno_id'] = $aluno->id;
        unset($data['matricula']);

        if (\App\Models\User::query()->where('aluno_id', $aluno->id)->exists()) {
            throw ValidationException::withMessages([
                'data.matricula' => 'Já existe uma conta vinculada a esta matrícula. Procure a administração da escola.',
            ]);
        }

        return $data;
    }
}
