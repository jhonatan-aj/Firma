<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persona_perfil_asignacion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')
                  ->constrained('usuarios')
                  ->onDelete('cascade');

            $table->foreignId('perfil_id')
                  ->constrained('perfiles')
                  ->onDelete('cascade');

            $table->foreignId('nivel_id')
                  ->nullable()
                  ->constrained('niveles')
                  ->onDelete('cascade');

            $table->json('menciones')->nullable();

            $table->foreignId('oficina_id')
                  ->nullable()
                  ->constrained('oficinas')
                  ->onDelete('set null');

            $table->foreignId('puesto_id')
                  ->nullable()
                  ->constrained('puestos')
                  ->onDelete('set null');

            $table->boolean('estado')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persona_perfil_asignacion');
    }
};
