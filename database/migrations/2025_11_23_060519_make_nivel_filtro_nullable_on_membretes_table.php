<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('membretes', function (Blueprint $table) {
            $table->string('nivel_filtro')
                  ->nullable()
                  ->comment('Descripción del filtro, ej: Pregrado o Posgrado')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('membretes', function (Blueprint $table) {
            $table->string('nivel_filtro')
                  ->comment('Descripción del filtro, ej: Pregrado o Posgrado')
                  ->change();
        });
    }
};
