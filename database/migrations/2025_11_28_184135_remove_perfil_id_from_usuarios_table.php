<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['perfil_id']);
            $table->dropColumn('perfil_id');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreignId('perfil_id')
                  ->constrained('perfiles')
                  ->onDelete('cascade');
        });
    }

};
