<?php

namespace App\Filament\Resources\Ocorrencias\Pages;

use App\Filament\Resources\Ocorrencias\OcorrenciaResource;
use Filament\Resources\Pages\ViewRecord;

class ViewOcorrencia extends ViewRecord
{
    protected static string $resource = OcorrenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
