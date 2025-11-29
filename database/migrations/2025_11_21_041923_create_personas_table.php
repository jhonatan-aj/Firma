<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();       // código interno
            $table->string('dni', 15)->unique();      // documento
            $table->string('paterno');                // apellido paterno
            $table->string('materno');                // apellido materno
            $table->string('nombres');                // nombres
            $table->date('fecha_nacimiento')->nullable();
            $table->string('correo')->unique();
            $table->string('celular')->nullable();
            $table->string('direccion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
