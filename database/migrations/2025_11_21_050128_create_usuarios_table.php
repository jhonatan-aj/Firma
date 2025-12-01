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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->onDelete('cascade');
            // $table->foreignId('perfil_id')->constrained('perfiles')->onDelete('cascade'); // Removed to fix migration error
            $table->string('usuario')->unique();   // nombre de usuario (ej. jlopez)
            $table->string('password');            // hash de contraseña
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
