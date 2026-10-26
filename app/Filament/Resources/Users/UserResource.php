<?php

namespace App\Filament\Resources\Users;

use App\Models\Aluno;
use App\Models\Professor;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Contas de usuário';
    protected static ?string $modelLabel = 'Conta de usuário';
    protected static ?string $pluralModelLabel = 'Contas de usuário';
    protected static ?int $navigationSort = 99;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nome')->required()->maxLength(255),
            TextInput::make('email')->label('E-mail')->email()->required()->unique(ignoreRecord: true),
            Select::make('role')->label('Tipo de conta')->options([
                'admin' => 'Administrador',
                'professor' => 'Professor',
                'professor_dt' => 'Professor (DT)',
                'aluno' => 'Aluno',
            ])->live()->afterStateUpdated(function (Set $set, ?string $state): void {
                if ($state === 'aluno') {
                    $set('professor_id', null);
                } elseif (in_array($state, ['professor', 'professor_dt'], true)) {
                    $set('aluno_id', null);
                } else {
                    $set('professor_id', null);
                    $set('aluno_id', null);
                }
            })->required(),
            Select::make('professor_id')->label('Professor vinculado')->options(fn (Get $get) => Professor::query()
                ->when($get('role') === 'professor_dt', fn ($query) => $query->whereHas('turmasComoDiretor'))
                ->orderBy('nome')->pluck('nome', 'id'))
                ->searchable()->preload()->visible(fn (Get $get) => in_array($get('role'), ['professor', 'professor_dt'], true))
                ->required(fn (Get $get) => in_array($get('role'), ['professor', 'professor_dt'], true))
                ->unique(ignoreRecord: true),
            Select::make('aluno_id')->label('Aluno vinculado')->options(Aluno::query()->orderBy('nome')->pluck('nome', 'id'))
                ->searchable()->preload()->visible(fn (Get $get) => $get('role') === 'aluno')->required(fn (Get $get) => $get('role') === 'aluno')
                ->unique(ignoreRecord: true),
            TextInput::make('password')->label('Senha')->password()->revealable()->required(fn (string $operation) => $operation === 'create')
                ->rules(fn (Get $get, string $operation) => (filled($get('password')) || $operation === 'create')
                    ? [Password::default()]
                    : [])
                ->dehydrated(fn ($state) => filled($state)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('email')->label('E-mail')->searchable(),
            Tables\Columns\TextColumn::make('role')->label('Tipo de conta')->formatStateUsing(fn (string $state) => [
                'admin' => 'Administrador', 'professor' => 'Professor', 'professor_dt' => 'Professor (DT)', 'aluno' => 'Aluno',
            ][$state] ?? $state),
            Tables\Columns\TextColumn::make('professor.nome')->label('Professor vinculado')->placeholder('—'),
            Tables\Columns\TextColumn::make('aluno.nome')->label('Aluno vinculado')->placeholder('—'),
        ])->recordActions([Actions\EditAction::make(), Actions\DeleteAction::make()]);
    }

    public static function canViewAny(): bool { return Auth::user()?->isAdmin() ?? false; }
    public static function canCreate(): bool { return Auth::user()?->isAdmin() ?? false; }
    public static function canEdit(Model $record): bool { return Auth::user()?->isAdmin() ?? false; }
    public static function canDelete(Model $record): bool
    {
        return (Auth::user()?->isAdmin() ?? false)
            && $record->getKey() !== Auth::id()
            && ($record->role !== 'admin' || User::query()->where('role', 'admin')->count() > 1);
    }

    public static function canDeleteAny(): bool { return false; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
