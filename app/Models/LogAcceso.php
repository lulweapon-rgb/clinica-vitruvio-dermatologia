<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LogAcceso extends Model
{
    protected $table = 'logs_acceso';
    
    // Desactivamos los timestamps por defecto porque tu tabla usa 'fecha_acceso'
    public $timestamps = false; 

    protected $fillable = [
        'usuario_id',
        'fecha_acceso',
        'direccion_ip',
        'accion_realizada',
        'eliminado_at'
    ];

    // Relación con el Usuario que hizo la acción
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id'); 
    }

    /**
     * MÉTODO GLOBAL DE AUDITORÍA
     * Llama a LogAcceso::registrar('El usuario hizo X cosa') desde cualquier controlador.
     */
    public static function registrar($accion_realizada)
    {
        if (Auth::check()) {
            self::create([
                'usuario_id' => Auth::id(),
                'fecha_acceso' => now(),
                'direccion_ip' => request()->ip(),
                'accion_realizada' => $accion_realizada
            ]);
        }
    }
}