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
        Schema::create('menciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 4)->unique()->comment('Código único de la mención');
            $table->string('facultad', 5)->default('09')->comment('Código de facultad');
            $table->unsignedBigInteger('nivel')->comment('ID del nivel académico');
            $table->string('mencion', 255)->comment('Nombre de la mención');
            $table->string('especialidad', 255)->nullable()->comment('Especialidad asociada');
            $table->boolean('estado')->default(true)->comment('Estado activo/inactivo');
            $table->timestamps();
            
            $table->foreign('nivel')->references('id')->on('niveles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menciones');
    }
};
