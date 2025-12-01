<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisito_firma', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_requisito_id')->constrained('documento_requisito')->onDelete('cascade');
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios');
            $table->enum('tipo_firma', ['digital', 'manual']);
            $table->boolean('firmado')->default(false);
            $table->timestamp('fecha_firma')->nullable();
            $table->string('pdf_firmado_path')->nullable(); // Para firma manual
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisito_firma');
    }
};
