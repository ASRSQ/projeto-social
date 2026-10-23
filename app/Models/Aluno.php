<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Aluno extends Model
{
    protected $table = 'alunos';

    protected $fillable = [
        'nome',
        'matricula',
        'numero',
        'turma_id',
        'concluinte',
        'concluido_em',
    ];

    protected function casts(): array
    {
        return [
            'concluinte' => 'boolean',
            'concluido_em' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (Aluno $aluno): void {
            $turmas = array_unique(array_filter([
                $aluno->getOriginal('turma_id'),
                $aluno->turma_id,
            ]));

            static::renumerarTurmas($turmas);
        });

        static::deleted(function (Aluno $aluno): void {
            static::renumerarTurmas([$aluno->turma_id]);
        });
    }

    public static function numeroDaChamadaPara(int|string|null $turmaId, ?string $nome, int|string|null $alunoId = null): ?int
    {
        if (blank($turmaId) || blank($nome)) {
            return null;
        }

        return static::query()
            ->where('turma_id', $turmaId)
            ->when(filled($alunoId), fn ($query) => $query->whereKeyNot($alunoId))
            ->where(function ($query) use ($nome, $alunoId): void {
                $query->where('nome', '<', $nome)
                    ->orWhere(function ($query) use ($nome, $alunoId): void {
                        $query->where('nome', $nome);

                        if (filled($alunoId)) {
                            $query->where('id', '<', $alunoId);
                        }
                    });
            })
            ->count() + 1;
    }

    /** @param array<int, int|string|null> $turmaIds */
    protected static function renumerarTurmas(array $turmaIds): void
    {
        foreach (array_unique(array_filter($turmaIds)) as $turmaId) {
            DB::transaction(function () use ($turmaId): void {
                $alunos = static::query()
                    ->where('turma_id', $turmaId)
                    ->orderBy('nome')
                    ->orderBy('id')
                    ->get(['id', 'numero']);

                foreach ($alunos as $index => $aluno) {
                    $numero = $index + 1;

                    if ($aluno->numero !== $numero) {
                        DB::table('alunos')
                            ->where('id', $aluno->id)
                            ->update(['numero' => $numero]);
                    }
                }
            });
        }
    }

    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }
}
