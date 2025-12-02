<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_requisito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisito_id')->constrained('requisitos')->onDelete('cascade');
            $table->string('nombre'); // Ej: "Formato Solicitud v1", "Plantilla Carta"
            $table->string('ruta_archivo'); // storage/plantillas/formato_solicitud.docx
            $table->enum('tipo_archivo', ['docx', 'pdf']); // Solo Word y PDF como plantillas
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_requisito');
    }
};
