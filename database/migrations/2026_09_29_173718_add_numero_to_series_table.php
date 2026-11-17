<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = Schema::hasTable('series') ? 'series' : 'turmas';

        if (Schema::hasColumn($tableName, 'numero')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) {
            $table->enum('numero', ['1º', '2º', '3º'])
                ->nullable()
                ->after('curso');
        });
    }

    public function down(): void
    {
        $tableName = Schema::hasTable('series') ? 'series' : 'turmas';

        if (! Schema::hasColumn($tableName, 'numero')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('numero');
        });
    }
};
