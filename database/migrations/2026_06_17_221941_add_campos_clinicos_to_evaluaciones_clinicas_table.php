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
    Schema::table('evaluaciones_clinicas', function (Blueprint $table) {
        // --- Fase 1: Síntomas actuales ---
        $table->text('motivo_consulta')->nullable();
        $table->string('duracion_lesion')->nullable();
        $table->text('otros_sintomas_sistemicos')->nullable(); // Fiebre, fatiga...
        
        // --- Fase 2: Exploración Física (Semiología) ---
        $table->string('tipo_lesion_morfologia')->nullable(); // Mácula, pápula, nódulo...
        $table->string('coloracion_lesion')->nullable(); // Ictericia, rojo, cianótica...
        $table->string('consistencia_palpacion')->nullable();
        $table->string('distribucion_lesiones')->nullable(); // Agrupada, diseminada...
        
        // --- Fase 3: Pruebas y Plan ---
        $table->string('prueba_diagnostica')->default('NINGUNA'); // Biopsia, Lámpara de Wood...
        $table->text('plan_tratamiento')->nullable();
        
        // Nota: ia_diagnostico e ia_porcentaje ya deben existir de pasos anteriores
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluaciones_clinicas', function (Blueprint $table) {
            //
        });
    }
};
