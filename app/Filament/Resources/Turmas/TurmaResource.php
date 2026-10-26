<?php

namespace App\Filament\Resources\Turmas;

use App\Filament\Resources\Turmas\Pages;
use App\Models\Turma;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TurmaResource extends Resource
{
    protected static ?string $model = Turma::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Turmas';

    protected static ?string $modelLabel = 'Turma';

    protected static ?string $pluralModelLabel = 'Turmas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('curso')
                    ->label('Curso')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('serie')
                    ->label('Série')
                    ->required()
                    ->maxLength(50),

                Forms\Components\Select::make('diretor_turma_id')
                    ->label('Diretor(a) de Turma')
                    ->relationship('diretorTurma', 'nome')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        return $user?->isAdmin()
            ? parent::getEloquentQuery()
            : parent::getEloquentQuery()->where('diretor_turma_id', $user?->professor_id ?? 0);
    }

    public static function canViewAny(): bool { return Auth::user()?->isAdmin() || Auth::user()?->isProfessorDt(); }
    public static function canCreate(): bool { return Auth::user()?->isAdmin() ?? false; }
    public static function canEdit(Model $record): bool { return Auth::user()?->isAdmin() ?? false; }
    public static function canDelete(Model $record): bool { return Auth::user()?->isAdmin() ?? false; }
    public static function canDeleteAny(): bool { return Auth::user()?->isAdmin() ?? false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('curso')
                    ->label('Curso')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('serie')
                    ->label('Série')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('diretorTurma.nome')
                    ->label('Diretor(a) de Turma')
                    ->searchable()
                    ->placeholder('Não definido'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Actions\Action::make('alunos')
                    ->label('Alunos')
                    ->icon('heroicon-o-users')
                    ->modalHeading(fn (Turma $record): string => "Alunos da {$record->serie} - {$record->curso}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar')
                    ->modalContent(fn (Turma $record) => view('filament.turmas.alunos-modal', [
                        'alunos' => $record->alunos()
                            ->orderBy('numero')
                            ->orderBy('nome')
                            ->get(),
                    ])),
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
            'index' => Pages\ListTurmas::route('/'),
            'create' => Pages\CreateTurma::route('/create'),
            'edit' => Pages\EditTurma::route('/{record}/edit'),
        ];
    }
}
