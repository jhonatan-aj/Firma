<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('firmas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('historial_proceso_id')->constrained('historial_procesos');
            $table->enum('tipo_firma', ['digital', 'manual'])->default('digital');

            // Para firma digital
            $table->text('firma_hash')->nullable(); // Hash de la firma digital
            $table->string('certificado_path')->nullable();

            // Para firma manual
            $table->string('pdf_firmado_path')->nullable(); // PDF escaneado firmado

            $table->boolean('valido')->default(true);
            $table->timestamp('fecha_firma');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firmas');
    }
};
