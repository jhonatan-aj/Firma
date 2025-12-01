<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento_requisito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisito_proceso_id')->constrained('requisito_proceso')->onDelete('cascade');
            $table->string('nombre_original');
            $table->string('nombre_almacenado');
            $table->string('path_archivo');
            $table->enum('tipo_archivo', ['pdf', 'docx']); // Solo PDF y Word
            $table->string('mime_type');
            $table->integer('tamano_bytes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_requisito');
    }
};
