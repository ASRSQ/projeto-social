<?php

namespace App\Filament\Resources\Disciplinas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DisciplinaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->required(),
            ]);
    }
}
