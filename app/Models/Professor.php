<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Professor extends Model
{
    use HasFactory;

    protected $table = 'professores';

    protected $fillable = [
        'nome',
        'disciplina',
    ];

    public function series(): BelongsToMany
    {
        return $this->belongsToMany(
            Serie::class,
            'professor_serie',
            'professor_id',
            'serie_id'
        )->withTimestamps();
    }
}