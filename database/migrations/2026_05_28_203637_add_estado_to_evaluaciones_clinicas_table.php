<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('evaluaciones_clinicas', function (Blueprint $table) {
            // Agregamos el campo estado justo después de estado_validacion
            $table->string('estado', 15)->default('ACTIVO')->after('estado_validacion');
        });
    }
    public function down() {
        Schema::table('evaluaciones_clinicas', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};