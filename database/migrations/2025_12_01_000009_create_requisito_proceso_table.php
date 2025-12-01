<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisito_proceso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisito_id')->constrained('requisitos')->onDelete('cascade');
            $table->foreignId('proceso_id')->constrained('procesos')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'cargado', 'aprobado', 'rechazado'])->default('cargado');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisito_proceso');
    }
};
