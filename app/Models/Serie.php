<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Serie extends Model
{
    use HasFactory;

    protected $table = 'series';

    protected $fillable = [
        'curso',
        'ano',
    ];

    public function professores(): BelongsToMany
    {
        return $this->belongsToMany(
            Professor::class,
            'professor_serie',
            'serie_id',
            'professor_id'
        )->withTimestamps();
    }
}