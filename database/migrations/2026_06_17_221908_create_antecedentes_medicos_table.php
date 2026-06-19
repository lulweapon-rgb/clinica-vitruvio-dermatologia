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
    Schema::create('antecedentes_medicos', function (Blueprint $table) {
        $table->id();
        // Relación 1 a 1 con el paciente
        $table->foreignId('paciente_id')->unique()->constrained('pacientes')->onDelete('cascade');
        
        // 1. Antecedentes Personales
        $table->text('exposicion_sol')->nullable();
        $table->boolean('quemaduras_previas')->default(false);
        $table->text('enfermedades_piel_previas')->nullable();
        
        // 2. Antecedentes Familiares
        $table->boolean('historial_familiar_melanoma')->default(false);
        $table->text('otras_patologias_familiares')->nullable(); // Diabetes, HTA, etc.
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antecedentes_medicos');
    }
};
