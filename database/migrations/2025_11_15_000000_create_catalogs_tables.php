<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create perfiles table
        if (!Schema::hasTable('perfiles')) {
            Schema::create('perfiles', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('descripcion')->nullable();
                $table->boolean('estado')->default(true);
                $table->timestamps();
            });
        }

        // Create oficinas table
        if (!Schema::hasTable('oficinas')) {
            Schema::create('oficinas', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('descripcion')->nullable();
                $table->boolean('estado')->default(true);
                $table->timestamps();
            });
        }

        // Create puestos table
        if (!Schema::hasTable('puestos')) {
            Schema::create('puestos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('descripcion')->nullable();
                $table->boolean('estado')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('puestos');
        Schema::dropIfExists('oficinas');
        Schema::dropIfExists('perfiles');
    }
};
