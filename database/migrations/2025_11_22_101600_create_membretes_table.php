<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('membretes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nivel_id')->constrained('niveles');
            $table->string('nivel_filtro')->comment('Descripción del filtro, ej: Pregrado o Posgrado');
            $table->string('nombre');
            $table->boolean('estado')->default(true);
            $table->string('derecha')->nullable()->comment('Ruta imagen derecha');
            $table->string('izquierda')->nullable()->comment('Ruta imagen izquierda');
            $table->text('centro')->comment('Contenido HTML central');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membretes');
    }
};
