<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ocorrencias', function (Blueprint $table) {
            $table->text('encaminhamento_diretor')->nullable();
            $table->enum('gravidade', ['leve', 'media', 'grave', 'gravissima'])->nullable();
            $table->timestamp('classificado_em')->nullable();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');

        Schema::table('ocorrencias', function (Blueprint $table) {
            $table->dropColumn(['encaminhamento_diretor', 'gravidade', 'classificado_em']);
        });
    }
};
