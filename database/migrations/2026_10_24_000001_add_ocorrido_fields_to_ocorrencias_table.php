<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ocorrencias', function (Blueprint $table) {
            $table->date('data_ocorrido')->nullable();
            $table->time('hora_ocorrido')->nullable();
            $table->json('atitudes_tomadas')->nullable();
        });

        DB::table('ocorrencias')->orderBy('id')->chunkById(100, function ($ocorrencias): void {
            foreach ($ocorrencias as $ocorrencia) {
                DB::table('ocorrencias')->where('id', $ocorrencia->id)->update([
                    'data_ocorrido' => $ocorrencia->data_ocorrencia,
                    'atitudes_tomadas' => json_encode([$ocorrencia->atitude_tomada]),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('ocorrencias', function (Blueprint $table) {
            $table->dropColumn(['data_ocorrido', 'hora_ocorrido', 'atitudes_tomadas']);
        });
    }
};
