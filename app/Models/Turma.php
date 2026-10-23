<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turma extends Model
{
    use HasFactory;

    protected $table = 'turmas';

    protected $fillable = [
        'curso',
        'serie',
        'diretor_turma_id',
    ];

    public function diretorTurma(): BelongsTo
    {
        return $this->belongsTo(Professor::class, 'diretor_turma_id');
    }

    public function professores(): BelongsToMany
    {
        return $this->belongsToMany(
            Professor::class,
            'professor_turma',
            'turma_id',
            'professor_id'
        )->withTimestamps();
    }

    public function alunos(): HasMany
    {
        return $this->hasMany(Aluno::class);
    }
}
