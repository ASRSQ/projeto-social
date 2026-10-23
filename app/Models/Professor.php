<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professor extends Model
{
    use HasFactory;

    protected $table = 'professores';

    protected $fillable = [
        'nome',
    ];

    public function turmas(): BelongsToMany
    {
        return $this->belongsToMany(
            Turma::class,
            'professor_turma',
            'professor_id',
            'turma_id'
        )->withTimestamps();
    }

    public function disciplinas(): BelongsToMany
    {
        return $this->belongsToMany(
            Disciplina::class,
            'disciplina_professor',
            'professor_id',
            'disciplina_id'
        )->withTimestamps();
    }

    public function turmasComoDiretor(): HasMany
    {
        return $this->hasMany(Turma::class, 'diretor_turma_id');
    }
}
