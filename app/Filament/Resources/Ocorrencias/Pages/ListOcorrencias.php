<?php

namespace App\Filament\Resources\Ocorrencias\Pages;

use App\Filament\Resources\Ocorrencias\OcorrenciaResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListOcorrencias extends ListRecords
{
    protected static string $resource = OcorrenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('imprimirMaisDeUmaOcorrencia')
                ->label('Imprimir mais de uma ocorrência')
                ->icon('heroicon-o-printer')
                ->url(route('ocorrencias.selecionar-lote'))
                ->visible(fn () => Auth::user()?->isAdmin() || Auth::user()?->isProfessorDt()),
            CreateAction::make()->label('Registrar ocorrência'),
        ];
    }
}
