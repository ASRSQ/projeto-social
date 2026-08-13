<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Disciplina extends Model
{
    use HasFactory;

    protected $table = 'disciplinas';

    protected $fillable = [
        'nome',
    ];

    public function professores(): BelongsToMany
    {
        return $this->belongsToMany(
            Professor::class,
            'disciplina_professor',
            'disciplina_id',
            'professor_id'
        )->withTimestamps();
    }
}