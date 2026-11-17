<?php

namespace App\Filament\Resources\Ocorrencias\Pages;

use App\Filament\Resources\Ocorrencias\OcorrenciaResource;
use App\Models\Ocorrencia;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOcorrencia extends EditRecord
{
    protected static string $resource = OcorrenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('imprimir')
                ->label('Imprimir ocorrência')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('ocorrencias.imprimir', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->classificada()
                    && (Auth::user()?->isAdmin()
                        || Auth::user()?->isProfessorDt()
                        || (Auth::user()?->isProfessor() && (int) $this->record->professor_id === (int) Auth::user()->professor_id))),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        if (Auth::user()?->isProfessorDt() && $this->record->classificada()) {
            return OcorrenciaResource::getUrl('index');
        }

        return parent::getRedirectUrl();
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

        if (Auth::user()?->isAdmin() && filled($data['encaminhamento_diretor'] ?? null) && filled($data['gravidade'] ?? null)) {
            $data['classificado_em'] = $this->record->classificado_em ?? now();
        }

        return $data;
    }
}
