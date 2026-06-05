<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EvaluacionClinicaController;
use App\Http\Controllers\AnalisisIAController;
use App\Http\Controllers\SeguimientoEvolutivoController;

Route::get('/', function () {
    return redirect('/login');
});

// Ruta especial de bypass para desarrollo
Route::get('/dev-login', [AuthController::class, 'devLogin'])->name('dev.login');

// Rutas para usuarios NO autenticados (Invitados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Rutas PRIVADAS (Solo usuarios autenticados)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // ==========================================
    // RUTA DEL DASHBOARD / PANEL PRINCIPAL
    // ==========================================
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // MÓDULO 1: PACIENTES
    // ==========================================
    Route::post('/pacientes/{id}/restore', [PacienteController::class, 'restore'])->name('pacientes.restore');
    Route::resource('pacientes', PacienteController::class);
    Route::delete('/pacientes/{id}/force', [PacienteController::class, 'forceDelete'])->name('pacientes.force_delete');

    // ==========================================
    // MÓDULO 2: ESPECIALIDADES
    // ==========================================
    Route::post('/especialidades/{id}/restore', [EspecialidadController::class, 'restore'])->name('especialidades.restore');
    Route::resource('especialidades', EspecialidadController::class);

    // ==========================================
    // MÓDULO 3: ROLES DE SISTEMA
    // ==========================================
    Route::post('/roles/{id}/restore', [RolController::class, 'restore'])->name('roles.restore');
    Route::resource('roles', RolController::class);
    Route::delete('/roles/{id}/force', [RolController::class, 'forceDelete'])->name('roles.force_delete');

    // ==========================================
    // MÓDULO 4: USUARIOS Y PERSONAL MÉDICO
    // ==========================================
    Route::post('/usuarios/{id}/restore', [UsuarioController::class, 'restore'])->name('usuarios.restore');
    Route::resource('usuarios', UsuarioController::class);
    Route::delete('/usuarios/{id}/force', [UsuarioController::class, 'forceDelete'])->name('usuarios.force_delete');

    // ==========================================
    // MÓDULO 5: EVALUACIONES CLÍNICAS (HISTORIAL)
    // ==========================================
    Route::post('/evaluaciones/{id}/restore', [EvaluacionClinicaController::class, 'restore'])->name('evaluaciones.restore');
    Route::resource('evaluaciones', EvaluacionClinicaController::class);
    Route::delete('/evaluaciones/{id}/force', [EvaluacionClinicaController::class, 'forceDelete'])->name('evaluaciones.force_delete');

    // ==========================================
    // MÓDULO 6: ANÁLISIS DE IA (CNN)
    // ==========================================
    Route::get('/analisis-ia', [AnalisisIAController::class, 'index'])->name('analisis.index');
    Route::post('/analisis-ia/{id}/ejecutar', [AnalisisIAController::class, 'ejecutarAnalisis'])->name('analisis.ejecutar');

    // ==========================================
    // MÓDULO 7: SEGUIMIENTO EVOLUTIVO (GEMELO DIGITAL)
    // ==========================================
    Route::get('/seguimientos', [SeguimientoEvolutivoController::class, 'index'])->name('seguimientos.index');
    Route::get('/seguimientos/{id}/timeline', [SeguimientoEvolutivoController::class, 'timeline'])->name('seguimientos.timeline');
    Route::post('/seguimientos/{id}/store', [SeguimientoEvolutivoController::class, 'store'])->name('seguimientos.store');
    // NUEVAS RUTAS PARA EDITAR Y ELIMINAR CONTROLES
    Route::put('/seguimientos/update/{id}', [SeguimientoEvolutivoController::class, 'update'])->name('seguimientos.update');
    Route::delete('/seguimientos/destroy/{id}', [SeguimientoEvolutivoController::class, 'destroy'])->name('seguimientos.destroy');
    // NUEVA RUTA PARA RESTAURAR
    Route::post('/seguimientos/restore/{id}', [SeguimientoEvolutivoController::class, 'restore'])->name('seguimientos.restore');

    // ==========================================
    // MÓDULO DE AUDITORÍA Y LOGS
    // ==========================================
    Route::get('/auditoria-logs', [\App\Http\Controllers\LogController::class, 'index'])->name('logs.index');

    // ==========================================
    // MÓDULO DE REPORTES COMBINADOS (HITO 4)
    // ==========================================
    Route::get('/reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.index');

    // Generador Interactivo (JOINs dinámicos)
    Route::get('/constructor-reportes', [\App\Http\Controllers\ConstructorReporteController::class, 'index'])->name('reportes.constructor');
});