<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professores', function (Blueprint $table) {
            $table->dropColumn('disciplina');

            $table->foreignId('disciplina_id')
                ->nullable()
                ->after('nome')
                ->constrained('disciplinas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('professores', function (Blueprint $table) {
            $table->dropForeign(['disciplina_id']);
            $table->dropColumn('disciplina');

            $table->string('disciplina')
                ->nullable()
                ->after('nome');
        });
    }
};