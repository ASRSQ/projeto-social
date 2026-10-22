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
        'serie_id',
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
            $series = array_unique(array_filter([
                $aluno->getOriginal('serie_id'),
                $aluno->serie_id,
            ]));

            static::renumerarSeries($series);
        });

        static::deleted(function (Aluno $aluno): void {
            static::renumerarSeries([$aluno->serie_id]);
        });
    }

    public static function numeroDaChamadaPara(int|string|null $serieId, ?string $nome, int|string|null $alunoId = null): ?int
    {
        if (blank($serieId) || blank($nome)) {
            return null;
        }

        return static::query()
            ->where('serie_id', $serieId)
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

    /** @param array<int, int|string|null> $serieIds */
    protected static function renumerarSeries(array $serieIds): void
    {
        foreach (array_unique(array_filter($serieIds)) as $serieId) {
            DB::transaction(function () use ($serieId): void {
                $alunos = static::query()
                    ->where('serie_id', $serieId)
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

    public function serie(): BelongsTo
    {
        return $this->belongsTo(Serie::class);
    }
}
