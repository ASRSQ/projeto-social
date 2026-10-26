<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ocorrencia extends Model
{
    protected $table = 'ocorrencias';

    protected $fillable = [
        'aluno_id', 'professor_id', 'turma_id', 'disciplina_id', 'data_ocorrido', 'hora_ocorrido', 'local_ocorrido',
        'atitudes_tomadas', 'outra_atitude', 'descricao_situacao', 'frequencia',
        'medidas_sugeridas', 'data_encaminhamento', 'encaminhamento_diretor',
        'gravidade', 'classificado_em',
    ];

    protected function casts(): array
    {
        return [
            'data_ocorrencia' => 'date',
            'data_ocorrido' => 'date',
            'data_encaminhamento' => 'date',
            'atitudes_tomadas' => 'array',
            'classificado_em' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Ocorrencia $ocorrencia): void {
            $ocorrencia->data_encaminhamento ??= now()->toDateString();
        });

        // Mantém os campos legados preenchidos para instalações que já migraram.
        static::saving(function (Ocorrencia $ocorrencia): void {
            if ($ocorrencia->data_ocorrido) {
                $ocorrencia->data_ocorrencia = $ocorrencia->data_ocorrido;
            }

            $primeiraAtitude = $ocorrencia->atitudes_tomadas[0] ?? null;
            if ($primeiraAtitude) {
                $ocorrencia->atitude_tomada = $primeiraAtitude;
            }
        });
    }

    public function aluno(): BelongsTo { return $this->belongsTo(Aluno::class); }
    public function professor(): BelongsTo { return $this->belongsTo(Professor::class); }
    public function turma(): BelongsTo { return $this->belongsTo(Turma::class); }
    public function disciplina(): BelongsTo { return $this->belongsTo(Disciplina::class); }
}
