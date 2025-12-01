<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integrante_tesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis')->onDelete('cascade');
            $table->foreignId('persona_id')->constrained('personas')->onDelete('cascade');
            $table->enum('rol', ['tesista', 'asesor', 'jurado'])->default('tesista');
            $table->timestamps();

            // Un persona no puede tener el mismo rol dos veces en la misma tesis
            $table->unique(['tesis_id', 'persona_id', 'rol']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integrante_tesis');
    }
};
