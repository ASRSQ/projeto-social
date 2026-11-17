<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('professores', 'disciplina')) {
            Schema::table('professores', function (Blueprint $table) {
                $table->dropColumn('disciplina');
            });
        }

        if (! Schema::hasColumn('professores', 'disciplina_id')) {
            Schema::table('professores', function (Blueprint $table) {
                $table->foreignId('disciplina_id')
                    ->nullable()
                    ->after('nome')
                    ->constrained('disciplinas')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('professores', 'disciplina_id')) {
            Schema::table('professores', function (Blueprint $table) {
                $table->dropConstrainedForeignId('disciplina_id');
            });
        }

        if (! Schema::hasColumn('professores', 'disciplina')) {
            Schema::table('professores', function (Blueprint $table) {
                $table->string('disciplina')
                    ->nullable()
                    ->after('nome');
            });
        }
    }
};
