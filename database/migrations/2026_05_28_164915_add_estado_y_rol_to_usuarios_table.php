<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('usuarios', function (Blueprint $table) {
            // Agregamos el estado y el nombre del rol en texto
            $table->string('estado', 15)->default('ACTIVO')->after('codigo_2fa');
            $table->string('rol_nombre', 100)->nullable()->after('estado');
        });
    }
    public function down() {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['estado', 'rol_nombre']);
        });
    }
};
