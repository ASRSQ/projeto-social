<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->dropForeign(['serie_id']);
        });

        Schema::table('professor_serie', function (Blueprint $table) {
            $table->dropForeign(['professor_id']);
            $table->dropForeign(['serie_id']);
            $table->dropUnique(['professor_id', 'serie_id']);
        });

        Schema::table('series', function (Blueprint $table) {
            $table->dropForeign(['diretor_turma_id']);
        });

        Schema::rename('series', 'turmas');
        Schema::rename('professor_serie', 'professor_turma');

        Schema::table('alunos', function (Blueprint $table) {
            $table->renameColumn('serie_id', 'turma_id');
            $table->foreign('turma_id')->references('id')->on('turmas')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('professor_turma', function (Blueprint $table) {
            $table->renameColumn('serie_id', 'turma_id');
            $table->foreign('professor_id')->references('id')->on('professores')->cascadeOnDelete();
            $table->foreign('turma_id')->references('id')->on('turmas')->cascadeOnDelete();
            $table->unique(['professor_id', 'turma_id']);
        });

        Schema::table('turmas', function (Blueprint $table) {
            $table->foreign('diretor_turma_id')->references('id')->on('professores')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->dropForeign(['turma_id']);
        });

        Schema::table('professor_turma', function (Blueprint $table) {
            $table->dropForeign(['professor_id']);
            $table->dropForeign(['turma_id']);
            $table->dropUnique(['professor_id', 'turma_id']);
        });

        Schema::table('turmas', function (Blueprint $table) {
            $table->dropForeign(['diretor_turma_id']);
        });

        Schema::rename('turmas', 'series');
        Schema::rename('professor_turma', 'professor_serie');

        Schema::table('alunos', function (Blueprint $table) {
            $table->renameColumn('turma_id', 'serie_id');
            $table->foreign('serie_id')->references('id')->on('series')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('professor_serie', function (Blueprint $table) {
            $table->renameColumn('turma_id', 'serie_id');
            $table->foreign('professor_id')->references('id')->on('professores')->cascadeOnDelete();
            $table->foreign('serie_id')->references('id')->on('series')->cascadeOnDelete();
            $table->unique(['professor_id', 'serie_id']);
        });

        Schema::table('series', function (Blueprint $table) {
            $table->foreign('diretor_turma_id')->references('id')->on('professores')->nullOnDelete();
        });
    }
};
