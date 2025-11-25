<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\TratamientoController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\HistorialClinicoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicoPacienteController;
use App\Http\Controllers\CatalogoDiagnosticoController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\MensajesController;
use App\Http\Controllers\NotificacionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ruta de prueba para CSS
Route::get('/test', function () {
    return view('test');
})->name('test');

// Rutas de autenticación
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        // Redirigir según el rol
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isMedico()) {
            return redirect()->route('medico.dashboard');
        } else {
            return redirect()->route('paciente.dashboard');
        }
    }
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Recuperación de contraseña
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas para manejo de sesión
Route::get('/check-session', function() {
    if (Auth::check()) {
        return response()->json(['active' => true]);
    }
    return response()->json(['active' => false], 401);
})->name('session.check');

Route::post('/extend-session', function() {
    if (Auth::check()) {
        // Actualizar la última actividad
        Session::put('last_activity', time());
        
        // Forzar el guardado de la sesión
        Session::save();
        
        return response()->json([
            'success' => true,
            'message' => 'Sesión extendida exitosamente',
            'timestamp' => time()
        ]);
    }
    return response()->json(['success' => false, 'message' => 'Usuario no autenticado'], 401);
})->name('session.extend');

// Rutas para enviar recordatorio de cita
Route::get('/enviar-recordatorio-cita', [CitaController::class, 'enviarRecordatorioCita'])->name('enviar-recordatorio-cita');
// Rutas protegidas
Route::middleware(['auth', 'session.timeout'])->group(function () {
    
    // Dashboard según rol
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rutas de administrador
    Route::middleware(['admin.access'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Gestión médica para administradores
        Route::resource('citas', CitaController::class);
        Route::resource('tratamientos', TratamientoController::class);
        Route::post('/tratamientos/{id}/toggle-status', [TratamientoController::class, 'toggleStatus'])->name('tratamientos.toggle-status');
        Route::post('/tratamientos/{id}/finalizar', [TratamientoController::class, 'finalizar'])->name('tratamientos.finalizar');
        Route::resource('actividades', ActividadController::class);
        Route::post('/actividades/{id}/toggle-completada', [ActividadController::class, 'toggleCompletada'])->name('actividades.toggle-completada');
        Route::get('/actividades/por-paciente/{idPaciente}', [ActividadController::class, 'porPaciente'])->name('actividades.por-paciente');
        Route::resource('diagnosticos', DiagnosticoController::class);
        Route::resource('catalogo-diagnosticos', CatalogoDiagnosticoController::class);
        Route::resource('preguntas', PreguntaController::class);
        Route::get('/preguntas/reporte/{id_paciente?}', [PreguntaController::class, 'reporte'])->name('preguntas.reporte');
        Route::resource('analisis', AnalisisController::class);
        Route::resource('historial-clinico', HistorialClinicoController::class);
        Route::post('/historial-clinico/{id}/cerrar', [HistorialClinicoController::class, 'cerrar'])->name('historial-clinico.cerrar');
        Route::get('/historial-clinico/reporte/{id_paciente?}', [HistorialClinicoController::class, 'reporte'])->name('historial-clinico.reporte');
        Route::get('/seguimiento/{id_paciente?}', [SeguimientoController::class, 'index'])->name('seguimiento.index');
        Route::get('/seguimiento/{id_paciente}/observaciones/create', [SeguimientoController::class, 'createObservacion'])->name('seguimiento.observaciones.create');
        Route::post('/seguimiento/{id_paciente}/observaciones', [SeguimientoController::class, 'storeObservacion'])->name('seguimiento.observaciones.store');
        Route::get('/seguimiento/observaciones/{id}/edit', [SeguimientoController::class, 'editObservacion'])->name('seguimiento.observaciones.edit');
        Route::put('/seguimiento/observaciones/{id}', [SeguimientoController::class, 'updateObservacion'])->name('seguimiento.observaciones.update');
        Route::delete('/seguimiento/observaciones/{id}', [SeguimientoController::class, 'destroyObservacion'])->name('seguimiento.observaciones.destroy');
        Route::get('/seguimiento/{id_paciente}/reporte/pdf', [SeguimientoController::class, 'reportePDF'])->name('seguimiento.reporte.pdf');
        Route::get('/seguimiento/{id_paciente}/reporte/excel', [SeguimientoController::class, 'reporteExcel'])->name('seguimiento.reporte.excel');
    });
    
    // Rutas de respaldo y restauración
    Route::middleware(['admin.access'])->prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/restore', [BackupController::class, 'restore'])->name('restore');
        Route::get('/respaldo', [BackupController::class, 'respaldo'])->name('respaldo');
    });
    
    // Rutas de médico
    Route::middleware(['role:medico'])->prefix('medico')->name('medico.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'medico'])->name('dashboard');
        Route::resource('citas', CitaController::class)->middleware('cita.availability');
        Route::post('/citas/{id}/confirmar', [CitaController::class, 'confirmar'])->name('citas.confirmar');
        Route::post('/citas/{id}/completar', [CitaController::class, 'completar'])->name('citas.completar');
        
        Route::resource('tratamientos', TratamientoController::class);
        Route::post('/tratamientos/{id}/toggle-status', [TratamientoController::class, 'toggleStatus'])->name('tratamientos.toggle-status');
        Route::post('/tratamientos/{id}/finalizar', [TratamientoController::class, 'finalizar'])->name('tratamientos.finalizar');
        Route::resource('actividades', ActividadController::class);
        Route::post('/actividades/{id}/toggle-completada', [ActividadController::class, 'toggleCompletada'])->name('actividades.toggle-completada');
        Route::get('/actividades/por-paciente/{idPaciente}', [ActividadController::class, 'porPaciente'])->name('actividades.por-paciente');
        Route::resource('diagnosticos', DiagnosticoController::class);
        Route::resource('catalogo-diagnosticos', CatalogoDiagnosticoController::class);
        Route::resource('preguntas', PreguntaController::class);
        Route::get('/preguntas/reporte/{id_paciente?}', [PreguntaController::class, 'reporte'])->name('preguntas.reporte');
        Route::resource('analisis', AnalisisController::class);
        Route::resource('pacientes', MedicoPacienteController::class);
        Route::post('/pacientes/{id}/toggle-status', [MedicoPacienteController::class, 'toggleStatus'])->name('pacientes.toggle-status');
        Route::get('/seguimiento/{id_paciente?}', [SeguimientoController::class, 'index'])->name('seguimiento.index');
        Route::get('/seguimiento/{id_paciente}/observaciones/create', [SeguimientoController::class, 'createObservacion'])->name('seguimiento.observaciones.create');
        Route::post('/seguimiento/{id_paciente}/observaciones', [SeguimientoController::class, 'storeObservacion'])->name('seguimiento.observaciones.store');
        Route::get('/seguimiento/observaciones/{id}/edit', [SeguimientoController::class, 'editObservacion'])->name('seguimiento.observaciones.edit');
        Route::put('/seguimiento/observaciones/{id}', [SeguimientoController::class, 'updateObservacion'])->name('seguimiento.observaciones.update');
        Route::delete('/seguimiento/observaciones/{id}', [SeguimientoController::class, 'destroyObservacion'])->name('seguimiento.observaciones.destroy');
        Route::get('/seguimiento/{id_paciente}/reporte/pdf', [SeguimientoController::class, 'reportePDF'])->name('seguimiento.reporte.pdf');
        Route::get('/seguimiento/{id_paciente}/reporte/excel', [SeguimientoController::class, 'reporteExcel'])->name('seguimiento.reporte.excel');
    });
    
    // Rutas de paciente
    Route::middleware(['role:paciente'])->prefix('paciente')->name('paciente.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'paciente'])->name('dashboard');
        Route::get('/citas', [CitaController::class, 'paciente'])->name('citas.index');
        Route::resource('citas', CitaController::class)->except(['index'])->middleware('cita.availability');
        Route::get('/historial-clinico', [HistorialClinicoController::class, 'paciente'])->name('historial-clinico.index');
        Route::get('/tratamientos', [TratamientoController::class, 'paciente'])->name('tratamientos.index');
        Route::get('/tratamientos/{id}', [TratamientoController::class, 'pacienteShow'])->name('tratamientos.show');
        Route::get('/actividades', [ActividadController::class, 'paciente'])->name('actividades.index');
        Route::get('/actividades/{id}', [ActividadController::class, 'pacienteShow'])->name('actividades.show');
        Route::post('/actividades/{id}/marcar-completada', [ActividadController::class, 'marcarCompletada'])->name('actividades.marcar-completada');
        Route::post('/actividades/{id}/agregar-comentario', [ActividadController::class, 'agregarComentario'])->name('actividades.agregar-comentario');
        Route::get('/preguntas', [PreguntaController::class, 'paciente'])->name('preguntas.index');
        Route::get('/preguntas/{id}', [PreguntaController::class, 'pacienteShow'])->name('preguntas.show');
        Route::post('/preguntas/{id}/responder', [PreguntaController::class, 'responder'])->name('preguntas.responder');
        Route::get('/analisis', [AnalisisController::class, 'paciente'])->name('analisis.index');
        Route::get('/analisis/{id}', [AnalisisController::class, 'pacienteShow'])->name('analisis.show');
        Route::get('/seguimiento', [SeguimientoController::class, 'paciente'])->name('seguimiento.index');
    });
    
    // Rutas compartidas
    Route::resource('citas', CitaController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);
    Route::get('/citas/disponibilidad', [CitaController::class, 'disponibilidad'])->name('citas.disponibilidad');
    
    // Rutas compartidas de historial clínico (para médicos y administradores)
    Route::middleware(['role:medico,admin'])->group(function () {
        Route::resource('historial-clinico', HistorialClinicoController::class);
        Route::post('/historial-clinico/{id}/cerrar', [HistorialClinicoController::class, 'cerrar'])->name('historial-clinico.cerrar');
        Route::get('/historial-clinico/reporte/{id_paciente?}', [HistorialClinicoController::class, 'reporte'])->name('historial-clinico.reporte');
    });
    
    // Rutas de mensajes (dentro del middleware auth)
    Route::get('/mensajes', [MensajesController::class, 'index'])->name('mensajes.index');
    Route::get('/mensajes/paciente', [MensajesController::class, 'mensajesPaciente'])->name('mensajes.paciente');
    Route::get('/mensajes/medico', [MensajesController::class, 'mensajesMedico'])->name('mensajes.medico');
    Route::get('/mensajes/iniciar-chat/{id_usuario}', [MensajesController::class, 'iniciarChat'])->name('mensajes.iniciar-chat');
    Route::post('/mensajes', [MensajesController::class, 'store'])->name('mensajes.store');
    Route::post('/mensajes/post', [MensajesController::class, 'postMensajes'])->name('mensajes.post');
    Route::get('/mensajes/mensajesPaciente', [MensajesController::class, 'mensajesPaciente'])->name('mensajes.mensajesPaciente');
    Route::get('/mensajes/mensajesMedico', [MensajesController::class, 'mensajesMedico'])->name('mensajes.mensajesMedico');
    Route::get('/mensajes/getMensajes/{id_chat}', [MensajesController::class, 'getMensajes'])->name('mensajes.getMensajes');    
    Route::post('/mensajes/postMensajes', [MensajesController::class, 'postMensajes'])->name('mensajes.postMensajes');
    
    // Rutas de notificaciones
    Route::middleware(['role:medico'])->prefix('medico')->name('medico.')->group(function () {
        Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::get('/notificaciones/tabla', [NotificacionController::class, 'actualizarTabla'])->name('notificaciones.tabla');
    });

    Route::middleware(['role:paciente'])->prefix('paciente')->name('paciente.')->group(function () {
        Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::get('/notificaciones/tabla', [NotificacionController::class, 'actualizarTabla'])->name('notificaciones.tabla');
    });
});

// Rutas de API para AJAX
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    Route::get('/medicos', function () {
        return \App\Models\Medico::with('usuario')->get();
    })->name('medicos');
    
    Route::get('/especialidades', function () {
        return \App\Models\Medico::distinct()->pluck('especialidad');
    })->name('especialidades');
    
    // API para obtener mensajes de un chat
    Route::get('/mensajes/{id_chat}', [MensajesController::class, 'getMensajes'])->name('mensajes.get');
});
