<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_procesos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained('procesos')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios'); // Quien realizó la acción
            $table->enum('accion', ['inicio', 'envio', 'recepcion', 'observacion', 'aprobacion', 'rechazo', 'firma', 'finalizacion', 'registro_firma']);
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_procesos');
    }
};
