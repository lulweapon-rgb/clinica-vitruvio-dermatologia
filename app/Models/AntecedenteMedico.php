<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AntecedenteMedico extends Model
{
    // Es buena práctica especificar el nombre exacto de la tabla en español
    protected $table = 'antecedentes_medicos';

   protected $fillable = [
        'paciente_id',
        
        // Anamnesis / Factores de Riesgo
        'exposicion_sol',
        'enfermedades_piel_previas',
        'otras_patologias_familiares',
        'quemaduras_previas',
        'historial_familiar_melanoma',
    ];

    // Relación Inversa: Un antecedente pertenece a un Paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}