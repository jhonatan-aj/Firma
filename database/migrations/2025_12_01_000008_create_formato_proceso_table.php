<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formato_proceso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formato_id')->constrained('formatos')->onDelete('cascade');
            $table->foreignId('proceso_id')->constrained('procesos')->onDelete('cascade');
            $table->foreignId('historial_proceso_id')->nullable()->constrained('historial_procesos');

            // Datos para reemplazar variables
            $table->text('sumilla')->nullable();
            $table->text('fundamento')->nullable();

            // Documento generado
            $table->string('pdf_generado_path')->nullable(); // storage o temp según tipo de firma
            $table->enum('tipo_firma', ['digital', 'manual'])->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formato_proceso');
    }
};
