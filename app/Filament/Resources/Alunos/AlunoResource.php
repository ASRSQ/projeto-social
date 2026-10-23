<?php

namespace App\Filament\Resources\Alunos;

use App\Filament\Resources\Alunos\Pages\CreateAluno;
use App\Filament\Resources\Alunos\Pages\EditAluno;
use App\Filament\Resources\Alunos\Pages\ListAlunos;
use App\Models\Aluno;
use App\Models\Turma;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set, ?Aluno $record) => $set(
                        'numero',
                        Aluno::numeroDaChamadaPara($get('turma_id'), $get('nome'), $record?->id),
                    )),

                TextInput::make('matricula')
                    ->label('Matrícula')
                    ->required()
                    ->numeric()
                    ->minLength(7)
                    ->maxLength(7)
                    ->unique(ignoreRecord: true),

                TextInput::make('numero')
                    ->label('Número da chamada')
                    ->disabled()
                    ->dehydrated(false),

                Select::make('turma_id')
                    ->label('Turma')
                    ->options(
                        Turma::all()->mapWithKeys(function ($turma) {
                            return [
                                $turma->id => "{$turma->curso} - {$turma->serie}",
                            ];
                        })
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set, ?Aluno $record) => $set(
                        'numero',
                        Aluno::numeroDaChamadaPara($get('turma_id'), $get('nome'), $record?->id),
                    )),
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

                Tables\Columns\TextColumn::make('numero')
                    ->label('Nº chamada')
                    ->sortable(),

                Tables\Columns\TextColumn::make('turma.curso')
                    ->label('Turma')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('conclusao')
                    ->label('Conclusão')
                    ->state(fn (Aluno $record): ?string => $record->concluinte
                        ? "Concluiu a 3ª série do curso de {$record->turma->curso} em {$record->concluido_em?->format('Y')}"
                        : null)
                    ->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('concluinte')
                    ->label('Situação')
                    ->options([
                        '0' => 'Alunos ativos',
                        '1' => 'Concluintes',
                    ])
                    ->default('0'),
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
