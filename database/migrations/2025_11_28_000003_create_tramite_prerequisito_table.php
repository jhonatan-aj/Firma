<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tramite_prerequisito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained('tramites')->onDelete('cascade');
            $table->foreignId('prerequisito_id')->constrained('tramites')->onDelete('cascade');
            $table->timestamps();

            // Evitar duplicados
            $table->unique(['tramite_id', 'prerequisito_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramite_prerequisito');
    }
};
