<?php

namespace App\Filament\Resources\Alunos\Pages;

use App\Filament\Resources\Alunos\AlunoResource;
use App\Models\Aluno;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateAluno extends CreateRecord
{
    protected static string $resource = AlunoResource::class;

    protected bool $confirmandoExcessoDeAlunos = false;

    public function create(bool $another = false): void
    {
        if (! $this->confirmandoExcessoDeAlunos) {
            $serieId = $this->form->getRawState()['serie_id'] ?? null;

            if (filled($serieId) && Aluno::where('serie_id', $serieId)->count() >= 45) {
                $this->mountAction('confirmarExcessoDeAlunos');

                return;
            }
        }

        $this->confirmandoExcessoDeAlunos = false;

        parent::create($another);
    }

    protected function confirmarExcessoDeAlunosAction(): Action
    {
        return Action::make('confirmarExcessoDeAlunos')
            ->requiresConfirmation()
            ->modalHeading('Sala com 45 alunos')
            ->modalDescription('Essa sala já é ocupada 45 alunos, deseja mesmo adicionar esse aluno?')
            ->modalSubmitActionLabel('Sim')
            ->modalCancelActionLabel('Não')
            ->action(function (): void {
                $this->confirmandoExcessoDeAlunos = true;
                $this->create();
            });
    }
}
