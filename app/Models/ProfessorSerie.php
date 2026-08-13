<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessorSerie extends Model
{
    use HasFactory;

    protected $table = 'professor_serie';

    protected $fillable = [
        'professor_id',
        'serie_id',
    ];

    public function professor(): BelongsTo
    {
        return $this->belongsTo(
            Professor::class,
            'professor_id'
        );
    }

    public function serie(): BelongsTo
    {
        return $this->belongsTo(
            Serie::class,
            'serie_id'
        );
    }
}