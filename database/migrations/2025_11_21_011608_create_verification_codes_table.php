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
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->string('email');              // correo destino
            $table->string('name')->nullable();   // nombre opcional
            $table->string('purpose');            // 'register' o 'reset'
            $table->string('code', 4);            // código de 4 dígitos
            $table->timestamp('expires_at');      // fecha de expiración
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
    }
};
