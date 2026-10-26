<?php

namespace App\Filament\Resources\Ocorrencias;

use App\Models\Aluno;
use App\Models\Disciplina;
use App\Models\Ocorrencia;
use App\Models\Professor;
use App\Models\Turma;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Components\TextEntry;

class OcorrenciaResource extends Resource
{
    protected static ?string $model = Ocorrencia::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Ocorrências';
    protected static ?string $modelLabel = 'Ocorrência';
    protected static ?string $pluralModelLabel = 'Ocorrências';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        $user = Auth::user();
        $professorId = $user?->professor_id;

        if ($user?->isProfessorDt() && ! $user?->isAdmin()) {
            return $schema->components([
                Section::make('Ocorrência recebida')->columns(2)->schema([
                    Placeholder::make('aluno_resumo')->label('Aluno(a)')->content(fn (?Ocorrencia $record) => $record?->aluno?->nome ?? '—'),
                    Placeholder::make('turma_resumo')->label('Turma')->content(fn (?Ocorrencia $record) => $record ? "{$record->turma->serie} - {$record->turma->curso}" : '—'),
                    Placeholder::make('professor_resumo')->label('Professor(a)')->content(fn (?Ocorrencia $record) => $record?->professor?->nome ?? '—'),
                    Placeholder::make('disciplina_resumo')->label('Disciplina')->content(fn (?Ocorrencia $record) => $record?->disciplina?->nome ?? '—'),
                    Placeholder::make('data_resumo')->label('Data e hora')->content(fn (?Ocorrencia $record) => $record ? $record->data_ocorrido?->format('d/m/Y').' às '.substr((string) $record->hora_ocorrido, 0, 5) : '—'),
                    Placeholder::make('local_resumo')->label('Local do ocorrido')->content(fn (?Ocorrencia $record) => $record?->local_ocorrido ?? '—'),
                    Placeholder::make('atitudes_resumo')->label('Atitude(s) tomada(s)')->content(fn (?Ocorrencia $record) => $record ? collect($record->atitudes_tomadas)->map(fn ($item) => $item === 'outro' ? ($record->outra_atitude ?: 'Outro') : ['ordem_saida' => 'Ordem de saída da sala', 'advertencia' => 'Advertência'][$item] ?? $item)->join(', ') : '—')->columnSpanFull(),
                    Placeholder::make('descricao_resumo')->label('Descrição da situação')->content(fn (?Ocorrencia $record) => $record?->descricao_situacao ?? '—')->columnSpanFull(),
                    Placeholder::make('frequencia_resumo')->label('Comportamento observado')->content(fn (?Ocorrencia $record) => ['primeira_vez' => '1ª vez', 'reincidente_pouco_frequente' => 'Reincidente (pouco frequente)', 'reincidente_frequente' => 'Reincidente (frequente)'][$record?->frequencia] ?? '—'),
                    Placeholder::make('medidas_resumo')->label('Medidas sugeridas pelo professor')->content(fn (?Ocorrencia $record) => $record?->medidas_sugeridas ?? '—')->columnSpanFull(),
                ]),
                Section::make('Análise do(a) Diretor(a) de Turma')->schema([
                    Textarea::make('encaminhamento_diretor')->label('Encaminhamento do(a) Diretor(a) de Turma')->rows(4)->required()->columnSpanFull(),
                    Select::make('gravidade')->label('Classificação da ocorrência')->options([
                        'leve' => 'Leve',
                        'media' => 'Média',
                        'grave' => 'Grave',
                        'gravissima' => 'Gravíssima',
                    ])->required(),
                ]),
            ]);
        }

        return $schema->components([
            Section::make('Identificação')->columns(2)->schema([
                Select::make('professor_id')->label('Professor(a)')->options(Professor::query()->when(! $user?->isAdmin(), fn ($query) => $query->whereKey($professorId))->orderBy('nome')->pluck('nome', 'id'))->default($professorId)->disabled(! $user?->isAdmin())->dehydrated()->searchable()->preload()->live()->afterStateUpdated(function (Set $set): void { $set('turma_id', null); $set('disciplina_id', null); $set('aluno_id', null); })->required(),
                Select::make('turma_id')->label('Turma (série e curso)')->options(fn (Get $get) => Turma::query()->when($get('professor_id'), function ($query, $selectedProfessorId) use ($user) {
                    if ($user?->isProfessorDt()) {
                        $query->where('diretor_turma_id', $selectedProfessorId);
                    }
                })->orderBy('serie')->get()->mapWithKeys(fn (Turma $turma) => [$turma->id => "{$turma->serie} - {$turma->curso}"]))->searchable()->preload()->live()->disabled(fn (Get $get) => blank($get('professor_id')))->afterStateUpdated(fn (Set $set) => $set('aluno_id', null))->required(),
                Select::make('disciplina_id')->label('Disciplina')->options(fn (Get $get) => Disciplina::query()->when($get('professor_id'), fn ($query, $selectedProfessorId) => $query->whereHas('professores', fn ($query) => $query->whereKey($selectedProfessorId)))->orderBy('nome')->pluck('nome', 'id'))->searchable()->preload()->disabled(fn (Get $get) => blank($get('professor_id')))->required(),
                Select::make('aluno_id')->label('Aluno(a)')->options(fn (Get $get) => Aluno::query()->when($get('turma_id'), fn ($query, $turmaId) => $query->where('turma_id', $turmaId))->orderBy('nome')->pluck('nome', 'id'))->searchable()->preload()->disabled(fn (Get $get) => blank($get('professor_id')) || blank($get('turma_id')))->required(),
                DatePicker::make('data_ocorrido')->label('Data do ocorrido')->required()->native(false),
                TimePicker::make('hora_ocorrido')->label('Hora do ocorrido')->seconds(false)->required(),
                TextInput::make('local_ocorrido')->label('Local onde o(a) aluno(a) se encontrava')->required()->maxLength(255),
            ]),
            Section::make('Registro de intervenção disciplinar')->schema([
                CheckboxList::make('atitudes_tomadas')->label('Atitude(s) tomada(s)')->options(['ordem_saida' => 'Ordem de saída da sala de aula', 'advertencia' => 'Advertência', 'outro' => 'Outro'])->columns(1)->live()->required(),
                Textarea::make('outra_atitude')->label('Descreva a outra atitude')->visible(fn (Get $get) => in_array('outro', $get('atitudes_tomadas') ?? [], true))->required(fn (Get $get) => in_array('outro', $get('atitudes_tomadas') ?? [], true))->rows(2),
                Textarea::make('descricao_situacao')->label('Descrição da situação')->rows(4)->required()->columnSpanFull(),
                Select::make('frequencia')->label('Comportamento observado')->options(['primeira_vez' => '1ª vez', 'reincidente_pouco_frequente' => 'Reincidente (pouco frequente)', 'reincidente_frequente' => 'Reincidente (frequente)'])->required(),
                Textarea::make('medidas_sugeridas')->label('Medidas sugeridas ao(à) Diretor(a) de Turma')->rows(3)->required()->columnSpanFull(),
            ]),
            Section::make('Encaminhamento')->schema([
                DatePicker::make('data_encaminhamento')->label('Encaminhado em')->default(now())->disabled()->dehydrated()->native(false)->visible(fn () => Auth::user()?->isAdmin()),
            ]),
            Section::make('Análise do(a) Diretor(a) de Turma')->columns(2)->schema([
                Textarea::make('encaminhamento_diretor')->label('Encaminhamento do(a) Diretor(a) de Turma')->disabled(fn () => ! Auth::user()?->isAdmin())->dehydrated(fn () => Auth::user()?->isAdmin())->columnSpanFull(),
                Select::make('gravidade')->label('Classificação')->options(['leve' => 'Leve', 'media' => 'Média', 'grave' => 'Grave', 'gravissima' => 'Gravíssima'])->disabled(fn () => ! Auth::user()?->isAdmin())->dehydrated(fn () => Auth::user()?->isAdmin()),
            ])->visible(fn (string $operation) => $operation === 'edit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('data_ocorrido')->label('Data do ocorrido')->date('d/m/Y')->sortable(),
            Tables\Columns\TextColumn::make('aluno.nome')->label('Aluno(a)')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('turma')->label('Turma')->state(fn (Ocorrencia $record) => "{$record->turma->serie} - {$record->turma->curso}")->searchable(),
            Tables\Columns\TextColumn::make('professor.nome')->label('Professor(a)')->searchable(),
            Tables\Columns\TextColumn::make('gravidade')->label('Classificação')->formatStateUsing(fn (?string $state) => ['leve' => 'Leve', 'media' => 'Média', 'grave' => 'Grave', 'gravissima' => 'Gravíssima'][$state] ?? 'Aguardando DT')->badge(),
            Tables\Columns\TextColumn::make('atitudes_tomadas')->label('Atitude(s)')->state(fn (Ocorrencia $record) => collect($record->atitudes_tomadas)->map(fn ($item) => $item === 'outro' ? ($record->outra_atitude ?: 'Outro') : ['ordem_saida' => 'Ordem de saída', 'advertencia' => 'Advertência'][$item] ?? $item)->join(', '))->wrap(),
        ])->defaultSort('data_ocorrido', 'desc')->recordActions([
            Actions\ViewAction::make(),
            Actions\EditAction::make(),
            Actions\Action::make('imprimir')
                ->label('Imprimir')
                ->icon('heroicon-o-printer')
                ->url(fn (Ocorrencia $record) => route('ocorrencias.imprimir', $record))
                ->openUrlInNewTab()
                ->visible(fn (Ocorrencia $record) => filled($record->classificado_em) && (Auth::user()?->isAdmin() || Auth::user()?->isProfessorDt())),
        ])->toolbarActions([
            Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()->visible(fn () => Auth::user()?->isAdmin())])->visible(fn () => Auth::user()?->isAdmin()),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('data_ocorrido')->label('Data do ocorrido')->date('d/m/Y'),
            TextEntry::make('hora_ocorrido')->label('Hora do ocorrido'),
            TextEntry::make('professor.nome')->label('Professor(a)'),
            TextEntry::make('turma')->label('Turma')->state(fn (Ocorrencia $record) => "{$record->turma->serie} - {$record->turma->curso}"),
            TextEntry::make('aluno.nome')->label('Aluno(a)'),
            TextEntry::make('atitudes_tomadas')->label('Atitude(s)')->formatStateUsing(fn ($state, Ocorrencia $record) => collect($state)->map(fn ($item) => $item === 'outro' ? ($record->outra_atitude ?: 'Outro') : ['ordem_saida' => 'Ordem de saída', 'advertencia' => 'Advertência'][$item] ?? $item)->join(', ')),
            TextEntry::make('descricao_situacao')->label('Descrição da situação')->columnSpanFull(),
            TextEntry::make('frequencia')->label('Comportamento observado')->formatStateUsing(fn ($state) => ['primeira_vez' => '1ª vez', 'reincidente_pouco_frequente' => 'Reincidente (pouco frequente)', 'reincidente_frequente' => 'Reincidente (frequente)'][$state] ?? $state),
            TextEntry::make('medidas_sugeridas')->label('Medidas sugeridas')->columnSpanFull(),
            TextEntry::make('encaminhamento_diretor')->label('Encaminhamento do(a) Diretor(a) de Turma')->placeholder('Aguardando análise do DT')->columnSpanFull(),
            TextEntry::make('gravidade')->label('Classificação')->formatStateUsing(fn ($state) => ['leve' => 'Leve', 'media' => 'Média', 'grave' => 'Grave', 'gravissima' => 'Gravíssima'][$state] ?? 'Aguardando análise'),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery();

        return match (true) {
            $user?->isAdmin() => $query,
            $user?->isProfessor() => $query->where('professor_id', $user->professor_id),
            $user?->isProfessorDt() => $query->whereIn('turma_id', $user->turmasComoDiretor()->select('id')),
            $user?->isAluno() => $query->where('aluno_id', $user->aluno_id),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public static function canViewAny(): bool
    {
        return Auth::user() !== null;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->isAdmin() || Auth::user()?->isProfessor();
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();
        return $user?->isAdmin()
            || ($user?->isProfessor() && (int) $record->professor_id === (int) $user->professor_id)
            || ($user?->isProfessorDt() && $user->turmasComoDiretor()->whereKey($record->turma_id)->exists());
    }

    public static function canDelete(Model $record): bool { return Auth::user()?->isAdmin() ?? false; }
    public static function canDeleteAny(): bool { return Auth::user()?->isAdmin() ?? false; }

    public static function assertUserCanSave(array $data): void
    {
        $user = Auth::user();
        abort_unless($user, 403);

        abort_unless(Disciplina::query()->whereKey($data['disciplina_id'] ?? null)
            ->whereHas('professores', fn ($query) => $query->whereKey($data['professor_id'] ?? null))
            ->exists(), 422);

        if ($user->isAdmin()) {
            return;
        }

        abort_unless((int) $data['professor_id'] === (int) $user->professor_id, 403);
        $turmaValida = $user->isProfessorDt()
            ? $user->turmasComoDiretor()->whereKey($data['turma_id'])->exists()
            : Turma::query()->whereKey($data['turma_id'])->exists();

        abort_unless($turmaValida, 403);
        abort_unless(Aluno::query()->whereKey($data['aluno_id'])->where('turma_id', $data['turma_id'])->exists(), 403);
    }

    public static function assertDtCanClassify(Ocorrencia $record, array $data): void
    {
        $user = Auth::user();
        abort_unless($user?->isProfessorDt(), 403);
        abort_unless($user->turmasComoDiretor()->whereKey($record->turma_id)->exists(), 403);
        abort_unless(filled($data['encaminhamento_diretor'] ?? null), 422);
        abort_unless(in_array($data['gravidade'] ?? null, ['leve', 'media', 'grave', 'gravissima'], true), 422);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOcorrencias::route('/'),
            'create' => Pages\CreateOcorrencia::route('/create'),
            'edit' => Pages\EditOcorrencia::route('/{record}/edit'),
        ];
    }
}
