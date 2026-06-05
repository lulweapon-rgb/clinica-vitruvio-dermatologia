<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pacientes', function (Blueprint $table) {
            // Añadimos la columna 'estado', por defecto será 'ACTIVO' y se colocará después de 'correo'
            $table->string('estado', 15)->default('ACTIVO')->after('correo');
        });
    }

    public function down()
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};