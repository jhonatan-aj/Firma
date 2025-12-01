<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinatarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained('procesos')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('historial_proceso_id')->nullable()->constrained('historial_procesos');
            $table->enum('estado', ['pendiente', 'recibido', 'procesado'])->default('pendiente');
            $table->timestamp('fecha_recepcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinatarios');
    }
};
