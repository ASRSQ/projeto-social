<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('series', function (Blueprint $table) {
            $table->foreignId('diretor_turma_id')
                ->nullable()
                ->after('ano')
                ->constrained('professores')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('series', function (Blueprint $table) {
            $table->dropConstrainedForeignId('diretor_turma_id');
        });
    }
};
