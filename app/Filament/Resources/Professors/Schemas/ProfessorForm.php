<?php

namespace App\Filament\Resources\Professors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProfessorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->required(),
                TextInput::make('disciplina')
                    ->required(),
            ]);
    }
}
