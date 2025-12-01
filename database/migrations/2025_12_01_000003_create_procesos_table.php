<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procesos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained('tramites');
            $table->string('numero_tramite')->unique(); // TR-2025-00001
            $table->enum('estado', ['iniciado', 'en_proceso', 'observado', 'aprobado', 'rechazado', 'finalizado'])->default('iniciado');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procesos');
    }
};
