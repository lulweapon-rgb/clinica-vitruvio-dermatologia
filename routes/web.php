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
    
});
Route::get('/lanzamiento-produccion', function () {
    try {
        // 1. Ejecutar migraciones pendientes
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

        // 2. Crear los roles directamente (SIN forzar el ID para que PostgreSQL no bloquee)
        \Illuminate\Support\Facades\DB::table('roles')->updateOrInsert(['nombre' => 'Administrador']);
        \Illuminate\Support\Facades\DB::table('roles')->updateOrInsert(['nombre' => 'Medico']);

        // 3. Rescatar los IDs que PostgreSQL haya asignado automáticamente
        $idAdmin = \Illuminate\Support\Facades\DB::table('roles')->where('nombre', 'Administrador')->value('id');
        $idMedico = \Illuminate\Support\Facades\DB::table('roles')->where('nombre', 'Medico')->value('id');

        // 4. Crear los Usuarios de Prueba con esos IDs seguros
        \App\Models\Usuario::updateOrCreate(
            ['correo' => 'admin@ejemplo.com'],
            [
                'nombre' => 'Admin',
                'apellido_paterno' => 'Prueba',
                'contrasena' => \Illuminate\Support\Facades\Hash::make('Admin123'),
                'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
                'estado' => 'ACTIVO',
                'rol_id' => $idAdmin,
                'rol_nombre' => 'Administrador'
            ]
        );

        \App\Models\Usuario::updateOrCreate(
            ['correo' => 'user@ejemplo.com'],
            [
                'nombre' => 'Usuario',
                'apellido_paterno' => 'Regular',
                'contrasena' => \Illuminate\Support\Facades\Hash::make('User123'),
                'two_factor_secret' => 'KNRW24TMMJQXEZLJ',
                'estado' => 'ACTIVO',
                'rol_id' => $idMedico,
                'rol_nombre' => 'Medico'
            ]
        );

        return '¡ÉXITO TOTAL! Migraciones ejecutadas y Usuarios creados. Ya puedes iniciar sesión.';
    } catch (\Exception $e) {
        return 'Ocurrió un error: ' . $e->getMessage();
    }
});
Route::get('/claves-del-inge', function () {
    // 1. Forzar clave exacta del Administrador
    \Illuminate\Support\Facades\DB::table('usuarios')
        ->where('correo', 'admin@ejemplo.com')
        ->update([
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP', // La llave exacta del Inge
            'two_factor_enabled' => true // Saltamos la pantalla de escanear QR
        ]);

    // 2. Forzar clave exacta del Usuario Regular
    \Illuminate\Support\Facades\DB::table('usuarios')
        ->where('correo', 'user@ejemplo.com')
        ->update([
            'two_factor_secret' => 'KNRW24TMMJQXEZLJ', // La llave exacta del Inge
            'two_factor_enabled' => true
        ]);

    return '¡LISTO! Las claves estrictas del ingeniero han sido configuradas. Ya puede iniciar sesión.';
});