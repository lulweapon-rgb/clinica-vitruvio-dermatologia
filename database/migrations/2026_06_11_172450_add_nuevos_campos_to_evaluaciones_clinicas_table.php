<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluaciones_clinicas', function (Blueprint $table) {
            // Agregamos las nuevas columnas justo después del médico
            $table->string('ubicacion_anatomica')->nullable()->after('medico_id');
            
        });
    }

    public function down(): void
    {
        Schema::table('evaluaciones_clinicas', function (Blueprint $table) {
            // Si alguna vez necesitamos revertir, esto borrará solo las columnas nuevas
            $table->dropColumn(['ubicacion_anatomica', 'tiempo_evolucion']);
        });
    }
};