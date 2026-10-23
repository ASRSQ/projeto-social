<?php

namespace App\Filament\Resources\Professors;

use App\Filament\Resources\Professors\Pages;
use App\Models\Professor;
use App\Models\Turma;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

                Forms\Components\Select::make('turmas')
                    ->label('Turmas')
                    ->relationship('turmas', 'curso')
                    ->multiple()
                    ->searchable(['curso', 'serie'])
                    ->preload()
                    ->required()
                    ->getOptionLabelFromRecordUsing(
                        fn (Turma $record): string =>
                            "{$record->serie} - {$record->curso}"
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
                    ->formatStateUsing(function (string $state, Professor $record): string {
                        $turmasComoDiretor = $record->turmasComoDiretor
                            ->map(fn (Turma $turma): string => "DT da {$turma->serie} série de {$turma->curso}")
                            ->all();

                        return $turmasComoDiretor === []
                            ? $state
                            : $state.' - '.implode(', ', $turmasComoDiretor);
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('disciplinas.nome')
                    ->label('Disciplinas')
                    ->badge(),

                Tables\Columns\TextColumn::make('turmas')
                    ->label('Turmas')
                    ->badge()
                    ->getStateUsing(function (Professor $record): array {
                        return $record->turmas
                            ->map(
                                fn (Turma $turma) =>
                                    "{$turma->serie} - {$turma->curso}"
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['turmas', 'turmasComoDiretor']);
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
