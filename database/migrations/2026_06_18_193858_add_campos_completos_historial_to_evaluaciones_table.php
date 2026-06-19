<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Añadimos los campos a EVALUACIONES de forma "inteligente"
        Schema::table('evaluaciones_clinicas', function (Blueprint $table) {
            
            // Campos de consultas pasadas (Verifica si existen antes de crearlos)
            if (!Schema::hasColumn('evaluaciones_clinicas', 'motivo_consulta')) {
                $table->text('motivo_consulta')->nullable();
            }
            if (!Schema::hasColumn('evaluaciones_clinicas', 'duracion_lesion')) {
                $table->string('duracion_lesion', 100)->nullable();
            }
            if (!Schema::hasColumn('evaluaciones_clinicas', 'sintoma_prurito')) {
                $table->string('sintoma_prurito', 10)->nullable();
            }
            if (!Schema::hasColumn('evaluaciones_clinicas', 'sintoma_dolor')) {
                $table->string('sintoma_dolor', 10)->nullable();
            }
            if (!Schema::hasColumn('evaluaciones_clinicas', 'sintoma_sangrado')) {
                $table->string('sintoma_sangrado', 10)->nullable();
            }
            if (!Schema::hasColumn('evaluaciones_clinicas', 'sintoma_parestesia')) {
                $table->string('sintoma_parestesia', 10)->nullable();
            }
            if (!Schema::hasColumn('evaluaciones_clinicas', 'otros_sintomas_sistemicos')) {
                $table->text('otros_sintomas_sistemicos')->nullable();
            }
            if (!Schema::hasColumn('evaluaciones_clinicas', 'tipo_lesion_morfologia')) {
                $table->string('tipo_lesion_morfologia', 100)->nullable();
            }
            if (!Schema::hasColumn('evaluaciones_clinicas', 'coloracion_lesion')) {
                $table->string('coloracion_lesion', 100)->nullable();
            }
            if (!Schema::hasColumn('evaluaciones_clinicas', 'consistencia_palpacion')) {
                $table->string('consistencia_palpacion', 255)->nullable();
            }
            if (!Schema::hasColumn('evaluaciones_clinicas', 'distribucion_lesiones')) {
                $table->string('distribucion_lesiones', 255)->nullable();
            }

            // NUEVOS CAMPOS OBLIGATORIOS SEGÚN TU DOCUMENTO HISTORIAL.DOCX
            if (!Schema::hasColumn('evaluaciones_clinicas', 'factores_externos')) {
                $table->string('factores_externos', 255)->nullable();
            }
            if (!Schema::hasColumn('evaluaciones_clinicas', 'aspecto_general')) {
                $table->string('aspecto_general', 255)->nullable();
            }
            if (!Schema::hasColumn('evaluaciones_clinicas', 'disposicion_lesiones')) {
                $table->string('disposicion_lesiones', 100)->nullable();
            }
        });

        // 2. Añadimos los campos a los ANTECEDENTES
        Schema::table('antecedentes_medicos', function (Blueprint $table) {
            if (!Schema::hasColumn('antecedentes_medicos', 'exposicion_sol')) {
                $table->text('exposicion_sol')->nullable();
            }
            if (!Schema::hasColumn('antecedentes_medicos', 'enfermedades_piel_previas')) {
                $table->text('enfermedades_piel_previas')->nullable();
            }
            if (!Schema::hasColumn('antecedentes_medicos', 'otras_patologias_familiares')) {
                $table->text('otras_patologias_familiares')->nullable();
            }
            if (!Schema::hasColumn('antecedentes_medicos', 'quemaduras_previas')) {
                $table->boolean('quemaduras_previas')->default(false);
            }
            if (!Schema::hasColumn('antecedentes_medicos', 'historial_familiar_melanoma')) {
                $table->boolean('historial_familiar_melanoma')->default(false);
            }
        });
    }

    public function down(): void
    {
        // En el down no hacemos nada crítico para no perder datos por accidente
    }
};