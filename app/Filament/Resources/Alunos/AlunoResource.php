<?php

namespace App\Filament\Resources\Alunos;

use App\Filament\Resources\Alunos\Pages\CreateAluno;
use App\Filament\Resources\Alunos\Pages\EditAluno;
use App\Filament\Resources\Alunos\Pages\ListAlunos;
use App\Models\Aluno;
use App\Models\Serie;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AlunoResource extends Resource
{
    protected static ?string $model = Aluno::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Alunos';

    protected static ?string $modelLabel = 'Aluno';

    protected static ?string $pluralModelLabel = 'Alunos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                TextInput::make('matricula')
                    ->label('Matrícula')
                    ->required()
                    ->numeric()
                    ->minLength(7)
                    ->maxLength(7)
                    ->unique(ignoreRecord: true),

                Select::make('serie_id')
                    ->label('Série')
                    ->options(
                        Serie::all()->mapWithKeys(function ($serie) {
                            return [
                                $serie->id => "{$serie->curso} - {$serie->ano}",
                            ];
                        })
                    )
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('matricula')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('serie.curso')
                    ->label('Curso')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('serie.ano')
                    ->label('Ano')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlunos::route('/'),
            'create' => CreateAluno::route('/create'),
            'edit' => EditAluno::route('/{record}/edit'),
        ];
    }
}