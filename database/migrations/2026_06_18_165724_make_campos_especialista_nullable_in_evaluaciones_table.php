<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Usamos sentencias nativas de PostgreSQL para quitar la restricción "NOT NULL"
        // Esto permite que el personal inicial guarde la ficha sin llenar el diagnóstico final
        DB::statement('ALTER TABLE evaluaciones_clinicas ALTER COLUMN diagnostico_clinico DROP NOT NULL');
        
        // Si tienes estas columnas de la migración anterior, también las hacemos nullable
        DB::statement('ALTER TABLE evaluaciones_clinicas ALTER COLUMN prueba_diagnostica DROP NOT NULL');
        DB::statement('ALTER TABLE evaluaciones_clinicas ALTER COLUMN plan_tratamiento DROP NOT NULL');
    }

    public function down(): void
    {
        // Revertir en caso de emergencia
        DB::statement('ALTER TABLE evaluaciones_clinicas ALTER COLUMN diagnostico_clinico SET NOT NULL');
    }
};