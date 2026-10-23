<?php

namespace App\Filament\Resources\Turmas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class TurmaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('curso')
                    ->required(),
                TextInput::make('serie')
                    ->label('Série')
                    ->required(),
                Select::make('diretor_turma_id')
                    ->label('Diretor(a) de Turma')
                    ->relationship('diretorTurma', 'nome')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }
}
