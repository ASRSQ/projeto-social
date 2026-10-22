<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execucoes_progressao', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('ano_letivo')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execucoes_progressao');
    }
};
