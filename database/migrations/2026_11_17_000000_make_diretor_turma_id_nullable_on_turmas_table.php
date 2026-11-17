<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            $table->foreignId('diretor_turma_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // A migration original ja permitia nulos e a chave estrangeira usa nullOnDelete.
    }
};
