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
    }

    public function down(): void
    {
        if (! Schema::hasColumn('professores', 'disciplina')) {
            Schema::table('professores', function (Blueprint $table) {
                $table->string('disciplina');
            });
        }
    }
};
