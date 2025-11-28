<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formato_tramite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formato_id')->constrained('formatos')->onDelete('cascade');
            $table->foreignId('tramite_id')->constrained('tramites')->onDelete('cascade');
            $table->timestamps();

            // Evitar duplicados
            $table->unique(['formato_id', 'tramite_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formato_tramite');
    }
};
