<?php

namespace App\Filament\Resources\Ocorrencias\Pages;

use App\Filament\Resources\Ocorrencias\OcorrenciaResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateOcorrencia extends CreateRecord
{
    protected static string $resource = OcorrenciaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        OcorrenciaResource::assertUserCanSave($data);
        return $data;
    }

    protected function afterCreate(): void
    {
        $diretorProfessorId = $this->record->turma->diretor_turma_id;
        $diretor = User::query()
            ->where('role', 'professor_dt')
            ->where('professor_id', $diretorProfessorId)
            ->first();

        if (! $diretor) {
            return;
        }

        Notification::make()
            ->title('Nova ocorrência para analisar')
            ->body("{$this->record->aluno->nome} — {$this->record->turma->serie} - {$this->record->turma->curso}")
            ->actions([
                Action::make('analisar')
                    ->label('Analisar ocorrência')
                    ->url(OcorrenciaResource::getUrl('edit', ['record' => $this->record])),
            ])
            ->sendToDatabase($diretor);
    }
}
