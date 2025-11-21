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
        Schema::create('signed_documents', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('file_path')->nullable(); // Nullable hasta que se complete la firma
            $table->string('original_path')->nullable(); // Ruta del archivo temporal original
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable(); // Mensaje de error si falla
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signed_documents');
    }
};
