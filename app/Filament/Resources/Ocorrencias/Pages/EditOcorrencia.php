<?php

namespace App\Filament\Resources\Ocorrencias\Pages;

use App\Filament\Resources\Ocorrencias\OcorrenciaResource;
use App\Models\Ocorrencia;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOcorrencia extends EditRecord
{
    protected static string $resource = OcorrenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (Auth::user()?->isProfessorDt()) {
            /** @var Ocorrencia $record */
            $record = $this->record;
            OcorrenciaResource::assertDtCanClassify($record, $data);

            return [
                'encaminhamento_diretor' => $data['encaminhamento_diretor'],
                'gravidade' => $data['gravidade'],
                'classificado_em' => $record->classificado_em ?? now(),
            ];
        }

        if (! Auth::user()?->isAdmin()) {
            unset($data['encaminhamento_diretor'], $data['gravidade'], $data['classificado_em']);
        }
        OcorrenciaResource::assertUserCanSave($data);
        return $data;
    }
}
