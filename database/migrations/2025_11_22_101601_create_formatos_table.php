<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('formatos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('estado')->default(true);
            $table->foreignId('membrete_id')->constrained('membretes');
            $table->boolean('utilizado')->default(false);
            $table->string('tipo');
            $table->text('contenido')->comment('Contenido HTML del formato');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formatos');
    }
};
