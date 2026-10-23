<?php

namespace App\Filament\Resources\Turmas\Pages;

use App\Filament\Resources\Turmas\TurmaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTurmas extends ListRecords
{
    protected static string $resource = TurmaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nova Turma'),
        ];
    }
}
