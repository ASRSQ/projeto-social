<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('aluno')->after('password');
            $table->foreignId('professor_id')->nullable()->after('role')->constrained('professores')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('aluno_id')->nullable()->after('professor_id')->constrained('alunos')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('professor_id');
            $table->dropConstrainedForeignId('aluno_id');
            $table->dropColumn('role');
        });
    }
};
