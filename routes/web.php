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
use App\Http\Controllers\LogController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ConstructorReporteController;
use Illuminate\Support\Facades\Artisan;
Route::get('/', function () {
    return redirect('/login');
});

// ==========================================
// Rutas para usuarios NO autenticados (Invitados)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ==========================================
// Rutas del flujo de Autenticación de Dos Pasos (TOTP)
// ==========================================
Route::get('/2fa', [AuthController::class, 'show2faForm'])->name('2fa.index');
Route::post('/2fa', [AuthController::class, 'verify2fa'])->name('2fa.verify');


// ==========================================
// RUTAS PRIVADAS (Solo usuarios autenticados)
// ==========================================
Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // RUTA DEL DASHBOARD / PANEL PRINCIPAL (Accesible para todos los logueados)
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ------------------------------------------
    // MÓDULOS CLÍNICOS (Acceso para Médicos y Administradores)
    // Permiten el registro, detección y seguimiento cronológico de lesiones
    // ------------------------------------------
    Route::post('/pacientes/{id}/restore', [PacienteController::class, 'restore'])->name('pacientes.restore');
    Route::resource('pacientes', PacienteController::class);
    Route::delete('/pacientes/{id}/force', [PacienteController::class, 'forceDelete'])->name('pacientes.force_delete');

    Route::post('/evaluaciones/{id}/restore', [EvaluacionClinicaController::class, 'restore'])->name('evaluaciones.restore');
    Route::resource('evaluaciones', EvaluacionClinicaController::class);
    Route::delete('/evaluaciones/{id}/force', [EvaluacionClinicaController::class, 'forceDelete'])->name('evaluaciones.force_delete');

    Route::get('/analisis-ia', [AnalisisIAController::class, 'index'])->name('analisis.index');
    Route::post('/analisis-ia/{id}/ejecutar', [AnalisisIAController::class, 'ejecutarAnalisis'])->name('analisis.ejecutar');

    Route::get('/seguimientos', [SeguimientoEvolutivoController::class, 'index'])->name('seguimientos.index');
    Route::get('/seguimientos/{id}/timeline', [SeguimientoEvolutivoController::class, 'timeline'])->name('seguimientos.timeline');
    Route::post('/seguimientos/{id}/store', [SeguimientoEvolutivoController::class, 'store'])->name('seguimientos.store');
    Route::put('/seguimientos/update/{id}', [SeguimientoEvolutivoController::class, 'update'])->name('seguimientos.update');
    Route::delete('/seguimientos/destroy/{id}', [SeguimientoEvolutivoController::class, 'destroy'])->name('seguimientos.destroy');
    Route::post('/seguimientos/restore/{id}', [SeguimientoEvolutivoController::class, 'restore'])->name('seguimientos.restore');


    // ------------------------------------------
    // MÓDULOS ADMINISTRATIVOS (Acceso EXCLUSIVO para Administradores)
    // Protegidos por nuestro middleware personalizado 'superadmin'
    // ------------------------------------------
    Route::middleware(['superadmin'])->group(function () {
        
        Route::post('/especialidades/{id}/restore', [EspecialidadController::class, 'restore'])->name('especialidades.restore');
        Route::resource('especialidades', EspecialidadController::class);

        Route::post('/roles/{id}/restore', [RolController::class, 'restore'])->name('roles.restore');
        Route::resource('roles', RolController::class);
        Route::delete('/roles/{id}/force', [RolController::class, 'forceDelete'])->name('roles.force_delete');

        Route::post('/usuarios/{id}/restore', [UsuarioController::class, 'restore'])->name('usuarios.restore');
        Route::resource('usuarios', UsuarioController::class);
        Route::delete('/usuarios/{id}/force', [UsuarioController::class, 'forceDelete'])->name('usuarios.force_delete');

        Route::get('/auditoria-logs', [LogController::class, 'index'])->name('logs.index');

        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/constructor-reportes', [ConstructorReporteController::class, 'index'])->name('reportes.constructor');
        
    });
    Route::get('/lanzamiento-produccion', function () {
    try {
        // 1. Ejecuta las migraciones pendientes
        Artisan::call('migrate', ['--force' => true]);
        
        // 2. Ejecuta el Seeder de usuarios
        Artisan::call('db:seed', ['--class' => 'UsuariosPruebaSeeder', '--force' => true]);
        
        return '¡ÉXITO! Base de datos actualizada y Usuarios de prueba creados en Render.';
    } catch (\Exception $e) {
        return 'Ocurrió un error: ' . $e->getMessage();
    }
});
});