<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('codigos_ocorrencias', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ocorrencia_id')->unique()->constrained('ocorrencias')->cascadeOnDelete();
            $table->string('codigo', 5)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('codigos_ocorrencias');
    }
};
