<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar foreign key a tesis que referencia a procesos
        Schema::table('tesis', function (Blueprint $table) {
            $table->foreignId('proceso_id')->nullable()->constrained('procesos')->after('mencion_id');
        });
    }

    public function down(): void
    {
        Schema::table('tesis', function (Blueprint $table) {
            $table->dropForeign(['proceso_id']);
            $table->dropColumn('proceso_id');
        });
    }
};
