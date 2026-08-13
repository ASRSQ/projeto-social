<?php

namespace App\Filament\Resources\Series\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SerieForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('curso')
                    ->required(),
                TextInput::make('ano')
                    ->required(),
            ]);
    }
}
