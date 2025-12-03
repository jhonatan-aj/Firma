<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisito_proceso_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisito_proceso_id')->constrained('requisito_proceso')->onDelete('cascade');
            $table->foreignId('historial_proceso_id')->constrained('historial_procesos')->onDelete('cascade');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Índice único para evitar duplicados
            $table->unique(['requisito_proceso_id', 'historial_proceso_id'], 'requisito_historial_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisito_proceso_historial');
    }
};
