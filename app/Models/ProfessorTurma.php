<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessorTurma extends Model
{
    use HasFactory;

    protected $table = 'professor_turma';

    protected $fillable = [
        'professor_id',
        'turma_id',
    ];

    public function professor(): BelongsTo
    {
        return $this->belongsTo(
            Professor::class,
            'professor_id'
        );
    }

    public function turma(): BelongsTo
    {
        return $this->belongsTo(
            Turma::class,
            'turma_id'
        );
    }
}
