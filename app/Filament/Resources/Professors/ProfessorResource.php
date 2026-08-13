<?php

namespace App\Filament\Resources\Professors;

use App\Filament\Resources\Professors\Pages;
use App\Models\Professor;
use App\Models\Serie;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ProfessorResource extends Resource
{
    protected static ?string $model = Professor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Professores';

    protected static ?string $modelLabel = 'Professor';

    protected static ?string $pluralModelLabel = 'Professores';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('nome')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('disciplinas')
                    ->label('Disciplinas')
                    ->relationship('disciplinas', 'nome')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('series')
                    ->label('Séries')
                    ->relationship('series', 'curso')
                    ->multiple()
                    ->searchable(['curso', 'ano'])
                    ->preload()
                    ->required()
                    ->getOptionLabelFromRecordUsing(
                        fn (Serie $record): string =>
                            "{$record->ano} - {$record->curso}"
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('disciplinas.nome')
                    ->label('Disciplinas')
                    ->badge(),

                Tables\Columns\TextColumn::make('series')
                    ->label('Séries')
                    ->badge()
                    ->getStateUsing(function (Professor $record): array {
                        return $record->series
                            ->map(
                                fn (Serie $serie) =>
                                    "{$serie->ano} - {$serie->curso}"
                            )
                            ->toArray();
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfessors::route('/'),
            'create' => Pages\CreateProfessor::route('/create'),
            'edit' => Pages\EditProfessor::route('/{record}/edit'),
        ];
    }
}