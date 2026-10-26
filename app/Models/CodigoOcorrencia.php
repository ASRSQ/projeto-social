<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CodigoOcorrencia extends Model
{
    protected $table = 'codigos_ocorrencias';

    protected $fillable = ['ocorrencia_id', 'codigo'];

    public static function para(Ocorrencia $ocorrencia): self
    {
        return DB::transaction(function () use ($ocorrencia): self {
            // Serializa a criação do código para esta ocorrência e evita códigos duplicados.
            Ocorrencia::query()->whereKey($ocorrencia->getKey())->lockForUpdate()->firstOrFail();

            $existente = self::query()->where('ocorrencia_id', $ocorrencia->getKey())->first();
            if ($existente) {
                return $existente;
            }

            do {
                $codigo = str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            } while (self::query()->where('codigo', $codigo)->exists());

            return self::query()->create([
                'ocorrencia_id' => $ocorrencia->getKey(),
                'codigo' => $codigo,
            ]);
        });
    }
}
