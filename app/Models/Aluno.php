<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aluno extends Model
{
    protected $table = 'alunos';

    protected $fillable = [
        'nome',
        'matricula',
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

    public function serie(): BelongsTo
    {
        return $this->belongsTo(Serie::class);
    }
}
