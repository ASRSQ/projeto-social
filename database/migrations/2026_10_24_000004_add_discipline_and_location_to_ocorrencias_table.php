<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ocorrencias', function (Blueprint $table) {
            $table->foreignId('disciplina_id')->nullable()->after('turma_id')->constrained('disciplinas')->nullOnDelete();
            $table->string('local_ocorrido')->nullable()->after('hora_ocorrido');
        });
    }

    public function down(): void
    {
        Schema::table('ocorrencias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('disciplina_id');
            $table->dropColumn('local_ocorrido');
        });
    }
};
