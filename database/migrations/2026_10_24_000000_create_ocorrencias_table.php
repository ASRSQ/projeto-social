<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocorrencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('alunos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('data_ocorrencia');
            $table->enum('atitude_tomada', ['ordem_saida', 'advertencia', 'outro']);
            $table->text('outra_atitude')->nullable();
            $table->text('descricao_situacao');
            $table->enum('frequencia', ['primeira_vez', 'reincidente_pouco_frequente', 'reincidente_frequente']);
            $table->text('medidas_sugeridas');
            $table->text('encaminhamentos')->nullable();
            $table->date('data_encaminhamento')->nullable();
            $table->date('data_recebimento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocorrencias');
    }
};
