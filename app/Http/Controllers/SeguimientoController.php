<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Tratamiento;
use App\Models\Diagnostico;
use App\Models\Actividad;
use App\Models\Analisis;
use App\Models\ObservacionSeguimiento;
use App\Models\Pregunta;
use App\Models\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SeguimientoExport;

class SeguimientoController extends Controller
{
    /**
     * Mostrar el seguimiento completo de un paciente
     */
    public function index(Request $request, $id_paciente = null)
    {
        $user = Auth::user();
        
        // Si no se especifica un paciente y el usuario es médico o administrador, mostrar lista de selección
        if (!$id_paciente) {
            if ($user->isPaciente()) {
                // Los pacientes siempre ven su propio seguimiento
                $paciente = $user->paciente;
            } else {
                // Médicos y administradores: mostrar lista de pacientes para seleccionar
                $query = Paciente::with('usuario');
                
                // Filtro de búsqueda
                if ($request->filled('buscar')) {
                    $search = $request->buscar;
                    $query->whereHas('usuario', function($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%")
                          ->orWhere('apPaterno', 'like', "%{$search}%")
                          ->orWhere('apMaterno', 'like', "%{$search}%")
                          ->orWhere('correo', 'like', "%{$search}%");
                    });
                }
                
                $pacientes = $query->orderBy('id_paciente', 'desc')->paginate(15);
                
                return view('seguimiento.seleccionar-paciente', compact('pacientes'));
            }
        } else {
            // Se especificó un paciente
            $paciente = Paciente::with('usuario')->findOrFail($id_paciente);
            
            // Verificar permisos
            if ($user->isPaciente() && $paciente->id_paciente !== $user->paciente->id_paciente) {
                return redirect()->route('paciente.seguimiento.index')
                                ->with('error', 'No tienes permisos para ver este seguimiento.');
            }
        }

        // Aplicar filtros
        $filtros = $this->aplicarFiltros($request);

        // Consolidar todos los datos del paciente
        $datosConsolidados = $this->consolidarDatos($paciente, $filtros);

        // Calcular estadísticas e indicadores
        $estadisticas = $this->calcularEstadisticas($paciente, $filtros);

        // Obtener observaciones médicas
        $observaciones = $this->obtenerObservaciones($paciente, $filtros);

        // Obtener alertas y eventos recientes
        $alertas = $this->obtenerAlertas($paciente);

        // Opciones para filtros
        $opcionesFiltros = $this->obtenerOpcionesFiltros($paciente);

        // Datos para gráficas
        $datosGraficas = $this->prepararDatosGraficas($paciente, $filtros);

        return view('seguimiento.index', compact(
            'paciente',
            'datosConsolidados',
            'estadisticas',
            'observaciones',
            'alertas',
            'opcionesFiltros',
            'filtros',
            'datosGraficas'
        ));
    }

    /**
     * Consolidar todos los datos del paciente
     */
    private function consolidarDatos($paciente, $filtros)
    {
        $datos = [];

        // Citas
        $queryCitas = Cita::with(['medico.usuario'])
            ->where('id_paciente', $paciente->id_paciente);

        if ($filtros['fecha_desde']) {
            $queryCitas->whereDate('fecha', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $queryCitas->whereDate('fecha', '<=', $filtros['fecha_hasta']);
        }

        $datos['citas'] = $queryCitas->orderBy('fecha', 'desc')->get();

        // Tratamientos
        $queryTratamientos = Tratamiento::with(['medico.usuario', 'diagnostico'])
            ->where('id_paciente', $paciente->id_paciente);

        if ($filtros['fecha_desde']) {
            $queryTratamientos->whereDate('fecha_inicio', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $queryTratamientos->whereDate('fecha_inicio', '<=', $filtros['fecha_hasta']);
        }
        if ($filtros['id_tratamiento']) {
            $queryTratamientos->where('id_tratamiento', $filtros['id_tratamiento']);
        }

        $datos['tratamientos'] = $queryTratamientos->orderBy('fecha_inicio', 'desc')->get();

        // Diagnósticos
        $queryDiagnosticos = Diagnostico::with(['medico.usuario', 'catalogoDiagnostico'])
            ->where('id_paciente', $paciente->id_paciente);

        if ($filtros['fecha_desde']) {
            $queryDiagnosticos->whereDate('fecha', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $queryDiagnosticos->whereDate('fecha', '<=', $filtros['fecha_hasta']);
        }
        if ($filtros['id_diagnostico']) {
            $queryDiagnosticos->where('id_diagnostico', $filtros['id_diagnostico']);
        }

        $datos['diagnosticos'] = $queryDiagnosticos->orderBy('fecha', 'desc')->get();

        // Actividades
        $queryActividades = Actividad::with(['medico.usuario'])
            ->where('id_paciente', $paciente->id_paciente);

        if ($filtros['fecha_desde']) {
            $queryActividades->whereDate('fecha_asignacion', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $queryActividades->whereDate('fecha_asignacion', '<=', $filtros['fecha_hasta']);
        }

        $datos['actividades'] = $queryActividades->orderBy('fecha_asignacion', 'desc')->get();

        // Análisis clínicos
        $queryAnalisis = Analisis::with(['medico.usuario'])
            ->where('id_paciente', $paciente->id_paciente);

        if ($filtros['fecha_desde']) {
            $queryAnalisis->whereDate('fecha_analisis', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $queryAnalisis->whereDate('fecha_analisis', '<=', $filtros['fecha_hasta']);
        }

        $datos['analisis'] = $queryAnalisis->orderBy('fecha_analisis', 'desc')->get();

        // Preguntas y respuestas
        $queryPreguntas = Pregunta::with(['medico.usuario', 'respuestas.usuario'])
            ->where('id_paciente', $paciente->id_paciente)
            ->where('activa', true);

        if ($filtros['fecha_desde']) {
            $queryPreguntas->whereDate('fecha_asignacion', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $queryPreguntas->whereDate('fecha_asignacion', '<=', $filtros['fecha_hasta']);
        }

        $datos['preguntas'] = $queryPreguntas->orderBy('fecha_asignacion', 'desc')->get();

        // Combinar todos los datos en una línea de tiempo ordenada por fecha
        $datos['timeline'] = $this->crearTimeline($datos);

        return $datos;
    }

    /**
     * Crear línea de tiempo combinada de todos los eventos
     */
    private function crearTimeline($datos)
    {
        $timeline = [];

        // Agregar citas
        foreach ($datos['citas'] ?? [] as $cita) {
            $medicoNombre = 'N/A';
            if ($cita->medico && $cita->medico->usuario) {
                $medicoNombre = trim($cita->medico->usuario->nombre . ' ' . ($cita->medico->usuario->apPaterno ?? ''));
            }
            
            $timeline[] = [
                'tipo' => 'cita',
                'fecha' => $cita->fecha,
                'titulo' => 'Cita médica',
                'descripcion' => $cita->motivo ?? 'Sin motivo especificado',
                'estado' => $cita->estado ?? 'pendiente',
                'medico' => $medicoNombre,
                'datos' => $cita,
            ];
        }

        // Agregar diagnósticos
        foreach ($datos['diagnosticos'] ?? [] as $diagnostico) {
            $medicoNombre = 'N/A';
            if ($diagnostico->medico && $diagnostico->medico->usuario) {
                $medicoNombre = trim($diagnostico->medico->usuario->nombre . ' ' . ($diagnostico->medico->usuario->apPaterno ?? ''));
            }
            
            $descripcion = $diagnostico->descripcion ?? 'Sin descripción';
            if ($diagnostico->catalogoDiagnostico && $diagnostico->catalogoDiagnostico->descripcion_clinica) {
                $descripcion = $diagnostico->catalogoDiagnostico->descripcion_clinica;
            }
            
            $timeline[] = [
                'tipo' => 'diagnostico',
                'fecha' => $diagnostico->fecha,
                'titulo' => 'Diagnóstico',
                'descripcion' => $descripcion,
                'medico' => $medicoNombre,
                'datos' => $diagnostico,
            ];
        }

        // Agregar tratamientos
        foreach ($datos['tratamientos'] ?? [] as $tratamiento) {
            $medicoNombre = 'N/A';
            if ($tratamiento->medico && $tratamiento->medico->usuario) {
                $medicoNombre = trim($tratamiento->medico->usuario->nombre . ' ' . ($tratamiento->medico->usuario->apPaterno ?? ''));
            }
            
            $descripcion = trim(($tratamiento->dosis ?? '') . ' - ' . ($tratamiento->frecuencia ?? ''));
            if (empty($descripcion) || $descripcion === ' - ') {
                $descripcion = $tratamiento->nombre ?? 'Sin descripción';
            }
            
            $timeline[] = [
                'tipo' => 'tratamiento',
                'fecha' => $tratamiento->fecha_inicio,
                'titulo' => 'Tratamiento: ' . ($tratamiento->nombre ?? 'Sin nombre'),
                'descripcion' => $descripcion,
                'estado' => $tratamiento->activo ? 'activo' : 'finalizado',
                'medico' => $medicoNombre,
                'datos' => $tratamiento,
            ];
        }

        // Agregar actividades
        foreach ($datos['actividades'] ?? [] as $actividad) {
            $medicoNombre = 'N/A';
            if ($actividad->medico && $actividad->medico->usuario) {
                $medicoNombre = trim($actividad->medico->usuario->nombre . ' ' . ($actividad->medico->usuario->apPaterno ?? ''));
            }
            
            $timeline[] = [
                'tipo' => 'actividad',
                'fecha' => $actividad->fecha_asignacion,
                'titulo' => 'Actividad: ' . ($actividad->nombre ?? 'Sin nombre'),
                'descripcion' => $actividad->descripcion ?? 'Sin descripción',
                'estado' => $actividad->completada ? 'completada' : 'pendiente',
                'medico' => $medicoNombre,
                'datos' => $actividad,
            ];
        }

        // Agregar análisis
        foreach ($datos['analisis'] ?? [] as $analisis) {
            $medicoNombre = 'N/A';
            if ($analisis->medico && $analisis->medico->usuario) {
                $medicoNombre = trim($analisis->medico->usuario->nombre . ' ' . ($analisis->medico->usuario->apPaterno ?? ''));
            }
            
            $timeline[] = [
                'tipo' => 'analisis',
                'fecha' => $analisis->fecha_analisis,
                'titulo' => 'Análisis: ' . ($analisis->tipo_estudio ?? 'Sin tipo'),
                'descripcion' => $analisis->descripcion ?? 'Sin descripción',
                'medico' => $medicoNombre,
                'datos' => $analisis,
            ];
        }

        // Ordenar por fecha descendente
        usort($timeline, function($a, $b) {
            return $b['fecha'] <=> $a['fecha'];
        });

        return $timeline;
    }

    /**
     * Calcular estadísticas e indicadores
     */
    private function calcularEstadisticas($paciente, $filtros)
    {
        $stats = [];

        // Estadísticas de citas
        $queryCitas = Cita::where('id_paciente', $paciente->id_paciente);
        if ($filtros['fecha_desde']) {
            $queryCitas->whereDate('fecha', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $queryCitas->whereDate('fecha', '<=', $filtros['fecha_hasta']);
        }

        $stats['citas'] = [
            'total' => $queryCitas->count(),
            'completadas' => (clone $queryCitas)->where('estado', 'completada')->count(),
            'confirmadas' => (clone $queryCitas)->where('estado', 'confirmada')->count(),
            'pendientes' => (clone $queryCitas)->where('estado', 'pendiente')->count(),
            'canceladas' => (clone $queryCitas)->where('estado', 'cancelada')->count(),
        ];

        // Estadísticas de tratamientos
        $queryTratamientos = Tratamiento::where('id_paciente', $paciente->id_paciente);
        if ($filtros['fecha_desde']) {
            $queryTratamientos->whereDate('fecha_inicio', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $queryTratamientos->whereDate('fecha_inicio', '<=', $filtros['fecha_hasta']);
        }

        $stats['tratamientos'] = [
            'total' => $queryTratamientos->count(),
            'activos' => (clone $queryTratamientos)->where('activo', true)->count(),
            'finalizados' => (clone $queryTratamientos)->where('activo', false)->count(),
        ];

        // Estadísticas de actividades
        $queryActividades = Actividad::where('id_paciente', $paciente->id_paciente);
        if ($filtros['fecha_desde']) {
            $queryActividades->whereDate('fecha_asignacion', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $queryActividades->whereDate('fecha_asignacion', '<=', $filtros['fecha_hasta']);
        }

        $stats['actividades'] = [
            'total' => $queryActividades->count(),
            'completadas' => (clone $queryActividades)->where('completada', true)->count(),
            'pendientes' => (clone $queryActividades)->where('completada', false)->count(),
            'por_vencer' => (clone $queryActividades)->where('completada', false)
                ->where('fecha_limite', '>=', now())
                ->where('fecha_limite', '<=', now()->addDays(7))
                ->count(),
        ];

        // Estadísticas de análisis
        $queryAnalisis = Analisis::where('id_paciente', $paciente->id_paciente);
        if ($filtros['fecha_desde']) {
            $queryAnalisis->whereDate('fecha_analisis', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $queryAnalisis->whereDate('fecha_analisis', '<=', $filtros['fecha_hasta']);
        }

        $stats['analisis'] = [
            'total' => $queryAnalisis->count(),
            'con_valores' => (clone $queryAnalisis)->whereNotNull('valores_obtenidos')->count(),
            'este_mes' => (clone $queryAnalisis)->whereMonth('fecha_analisis', now()->month)
                ->whereYear('fecha_analisis', now()->year)
                ->count(),
        ];

        // Estadísticas de diagnósticos
        $queryDiagnosticos = Diagnostico::where('id_paciente', $paciente->id_paciente);
        if ($filtros['fecha_desde']) {
            $queryDiagnosticos->whereDate('fecha', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $queryDiagnosticos->whereDate('fecha', '<=', $filtros['fecha_hasta']);
        }

        $stats['diagnosticos'] = [
            'total' => $queryDiagnosticos->count(),
        ];

        // Indicadores de cumplimiento
        $totalCitas = $stats['citas']['total'];
        $citasCompletadas = $stats['citas']['completadas'];
        $stats['cumplimiento_citas'] = $totalCitas > 0 
            ? round(($citasCompletadas / $totalCitas) * 100, 2) 
            : 0;

        $totalActividades = $stats['actividades']['total'];
        $actividadesCompletadas = $stats['actividades']['completadas'];
        $stats['cumplimiento_actividades'] = $totalActividades > 0 
            ? round(($actividadesCompletadas / $totalActividades) * 100, 2) 
            : 0;

        return $stats;
    }

    /**
     * Obtener observaciones médicas
     */
    private function obtenerObservaciones($paciente, $filtros)
    {
        $query = ObservacionSeguimiento::with(['medico.usuario'])
            ->where('id_paciente', $paciente->id_paciente);

        if ($filtros['fecha_desde']) {
            $query->whereDate('fecha_observacion', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $query->whereDate('fecha_observacion', '<=', $filtros['fecha_hasta']);
        }
        if ($filtros['tipo_observacion']) {
            $query->where('tipo', $filtros['tipo_observacion']);
        }

        return $query->orderBy('fecha_observacion', 'desc')
                    ->orderBy('fecha_creacion', 'desc')
                    ->get();
    }

    /**
     * Obtener alertas y eventos recientes
     */
    private function obtenerAlertas($paciente)
    {
        $alertas = [];

        // 1. Citas próximas (próximos 7 días)
        $citasProximas = Cita::with(['medico.usuario'])
            ->where('id_paciente', $paciente->id_paciente)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->whereBetween('fecha', [now(), now()->addDays(7)])
            ->orderBy('fecha', 'asc')
            ->get();

        foreach ($citasProximas as $cita) {
            $fechaFormato = $cita->fecha ? $cita->fecha->format('d/m/Y H:i') : 'Fecha no especificada';
            $diasRestantes = $cita->fecha ? now()->diffInDays($cita->fecha, false) : 0;
            $nivel = $diasRestantes <= 1 ? 'warning' : 'info';
            $alertas[] = [
                'tipo' => 'cita_proxima',
                'nivel' => $nivel,
                'mensaje' => 'Cita programada: ' . $fechaFormato . ($diasRestantes >= 0 ? ' (En ' . $diasRestantes . ' día(s))' : ''),
                'fecha' => $cita->fecha ?? now(),
                'datos' => $cita,
            ];
        }

        // 2. Citas vencidas sin completar
        $citasVencidas = Cita::with(['medico.usuario'])
            ->where('id_paciente', $paciente->id_paciente)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->where('fecha', '<', now())
            ->orderBy('fecha', 'desc')
            ->limit(5)
            ->get();

        foreach ($citasVencidas as $cita) {
            $fechaFormato = $cita->fecha ? $cita->fecha->format('d/m/Y H:i') : 'Fecha no especificada';
            $alertas[] = [
                'tipo' => 'cita_vencida',
                'nivel' => 'danger',
                'mensaje' => 'Cita vencida sin completar: ' . $fechaFormato,
                'fecha' => $cita->fecha ?? now(),
                'datos' => $cita,
            ];
        }

        // 3. Actividades por vencer (próximos 3 días)
        $actividadesPorVencer = Actividad::with(['medico.usuario'])
            ->where('id_paciente', $paciente->id_paciente)
            ->where('completada', false)
            ->where('fecha_limite', '>=', now())
            ->where('fecha_limite', '<=', now()->addDays(3))
            ->orderBy('fecha_limite', 'asc')
            ->get();

        foreach ($actividadesPorVencer as $actividad) {
            $fechaLimite = $actividad->fecha_limite ? $actividad->fecha_limite->format('d/m/Y') : 'Fecha no especificada';
            $diasRestantes = $actividad->fecha_limite ? now()->diffInDays($actividad->fecha_limite, false) : 0;
            $nivel = $diasRestantes <= 1 ? 'danger' : 'warning';
            $alertas[] = [
                'tipo' => 'actividad_por_vencer',
                'nivel' => $nivel,
                'mensaje' => 'Actividad por vencer: ' . ($actividad->nombre ?? 'Sin nombre') . ' (Vence: ' . $fechaLimite . ' - ' . $diasRestantes . ' día(s) restante(s))',
                'fecha' => $actividad->fecha_limite ?? now(),
                'datos' => $actividad,
            ];
        }

        // 4. Actividades vencidas sin completar
        $actividadesVencidas = Actividad::with(['medico.usuario'])
            ->where('id_paciente', $paciente->id_paciente)
            ->where('completada', false)
            ->where('fecha_limite', '<', now())
            ->orderBy('fecha_limite', 'desc')
            ->limit(5)
            ->get();

        foreach ($actividadesVencidas as $actividad) {
            $fechaLimite = $actividad->fecha_limite ? $actividad->fecha_limite->format('d/m/Y') : 'Fecha no especificada';
            $alertas[] = [
                'tipo' => 'actividad_vencida',
                'nivel' => 'danger',
                'mensaje' => 'Actividad vencida sin completar: ' . ($actividad->nombre ?? 'Sin nombre') . ' (Vencía: ' . $fechaLimite . ')',
                'fecha' => $actividad->fecha_limite ?? now(),
                'datos' => $actividad,
            ];
        }

        // 5. Tratamientos activos
        $tratamientosActivos = Tratamiento::with(['medico.usuario'])
            ->where('id_paciente', $paciente->id_paciente)
            ->where('activo', true)
            ->get();

        if ($tratamientosActivos->count() > 0) {
            $alertas[] = [
                'tipo' => 'tratamientos_activos',
                'nivel' => 'info',
                'mensaje' => $tratamientosActivos->count() . ' tratamiento(s) activo(s) en curso',
                'fecha' => now(),
                'datos' => $tratamientosActivos,
            ];
        }

        // 6. Preguntas pendientes de responder
        $preguntasPendientes = Pregunta::where('id_paciente', $paciente->id_paciente)
            ->where('activa', true)
            ->where('fecha_asignacion', '<=', now())
            ->whereDoesntHave('respuestas', function($query) use ($paciente) {
                $query->where('id_usuario', $paciente->id_usuario);
            })
            ->count();

        if ($preguntasPendientes > 0) {
            $alertas[] = [
                'tipo' => 'preguntas_pendientes',
                'nivel' => 'warning',
                'mensaje' => $preguntasPendientes . ' pregunta(s) pendiente(s) de responder',
                'fecha' => now(),
                'datos' => ['count' => $preguntasPendientes],
            ];
        }

        // 7. Análisis recientes (últimos 7 días)
        $analisisRecientes = Analisis::with(['medico.usuario'])
            ->where('id_paciente', $paciente->id_paciente)
            ->where('fecha_analisis', '>=', now()->subDays(7))
            ->count();

        if ($analisisRecientes > 0) {
            $alertas[] = [
                'tipo' => 'analisis_recientes',
                'nivel' => 'info',
                'mensaje' => $analisisRecientes . ' análisis clínico(s) realizado(s) en los últimos 7 días',
                'fecha' => now(),
                'datos' => ['count' => $analisisRecientes],
            ];
        }

        // Ordenar alertas por fecha (más recientes primero) y por nivel de prioridad
        usort($alertas, function($a, $b) {
            // Prioridad: danger > warning > info
            $prioridad = ['danger' => 3, 'warning' => 2, 'info' => 1];
            $prioridadA = $prioridad[$a['nivel']] ?? 0;
            $prioridadB = $prioridad[$b['nivel']] ?? 0;
            
            if ($prioridadA !== $prioridadB) {
                return $prioridadB <=> $prioridadA; // Mayor prioridad primero
            }
            
            return $b['fecha'] <=> $a['fecha']; // Más recientes primero
        });

        return $alertas;
    }

    /**
     * Aplicar filtros del request
     */
    private function aplicarFiltros(Request $request)
    {
        return [
            'fecha_desde' => $request->filled('fecha_desde') ? $request->fecha_desde : null,
            'fecha_hasta' => $request->filled('fecha_hasta') ? $request->fecha_hasta : null,
            'id_diagnostico' => $request->filled('id_diagnostico') ? $request->id_diagnostico : null,
            'id_tratamiento' => $request->filled('id_tratamiento') ? $request->id_tratamiento : null,
            'tipo_informacion' => $request->filled('tipo_informacion') ? $request->tipo_informacion : null,
            'tipo_observacion' => $request->filled('tipo_observacion') ? $request->tipo_observacion : null,
            'estado_paciente' => $request->filled('estado_paciente') ? $request->estado_paciente : null,
        ];
    }

    /**
     * Obtener opciones para los filtros
     */
    private function obtenerOpcionesFiltros($paciente)
    {
        return [
            'diagnosticos' => Diagnostico::with('catalogoDiagnostico')
                ->where('id_paciente', $paciente->id_paciente)
                ->get(),
            'tratamientos' => Tratamiento::where('id_paciente', $paciente->id_paciente)->get(),
            'tipos_observacion' => ObservacionSeguimiento::where('id_paciente', $paciente->id_paciente)
                ->distinct()
                ->pluck('tipo')
                ->filter(),
        ];
    }

    /**
     * Vista de seguimiento para pacientes
     */
    public function paciente(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        $paciente = $user->paciente()->with('usuario')->firstOrFail();

        // Aplicar filtros
        $filtros = $this->aplicarFiltros($request);

        // Consolidar datos (versión limitada para pacientes)
        $datosConsolidados = $this->consolidarDatos($paciente, $filtros);

        // Calcular estadísticas
        $estadisticas = $this->calcularEstadisticas($paciente, $filtros);

        // Obtener observaciones (solo las que el médico permita ver)
        $observaciones = $this->obtenerObservaciones($paciente, $filtros);

        // Obtener alertas
        $alertas = $this->obtenerAlertas($paciente);

        // Opciones para filtros
        $opcionesFiltros = $this->obtenerOpcionesFiltros($paciente);

        // Datos para gráficas
        $datosGraficas = $this->prepararDatosGraficas($paciente, $filtros);

        return view('paciente.seguimiento.index', compact(
            'paciente',
            'datosConsolidados',
            'estadisticas',
            'observaciones',
            'alertas',
            'opcionesFiltros',
            'filtros',
            'datosGraficas'
        ));
    }

    /**
     * Preparar datos para las gráficas
     */
    private function prepararDatosGraficas($paciente, $filtros)
    {
        $datos = [];

        // Gráfica de barras: Cumplimiento de citas por mes (últimos 6 meses)
        $fechaDesdeGrafica = $filtros['fecha_desde'] 
            ? \Carbon\Carbon::parse($filtros['fecha_desde'])
            : now()->subMonths(6);
        $fechaHastaGrafica = $filtros['fecha_hasta'] 
            ? \Carbon\Carbon::parse($filtros['fecha_hasta'])
            : now();

        // Obtener todas las citas en el rango de fechas
        $citasPorMes = Cita::where('id_paciente', $paciente->id_paciente)
            ->whereDate('fecha', '>=', $fechaDesdeGrafica)
            ->whereDate('fecha', '<=', $fechaHastaGrafica)
            ->selectRaw('DATE_FORMAT(fecha, "%Y-%m") as mes, 
                        COUNT(*) as total,
                        SUM(CASE WHEN estado = "completada" THEN 1 ELSE 0 END) as completadas,
                        SUM(CASE WHEN estado = "confirmada" THEN 1 ELSE 0 END) as confirmadas,
                        SUM(CASE WHEN estado = "pendiente" THEN 1 ELSE 0 END) as pendientes,
                        SUM(CASE WHEN estado = "cancelada" THEN 1 ELSE 0 END) as canceladas')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Crear un array con todos los meses en el rango para asegurar continuidad
        $mesesCompletos = [];
        $fechaActual = $fechaDesdeGrafica->copy()->startOfMonth();
        while ($fechaActual <= $fechaHastaGrafica) {
            $mesKey = $fechaActual->format('Y-m');
            $mesesCompletos[$mesKey] = [
                'mes' => $mesKey,
                'total' => 0,
                'completadas' => 0,
                'confirmadas' => 0,
                'pendientes' => 0,
                'canceladas' => 0,
            ];
            $fechaActual->addMonth();
        }

        // Llenar con los datos reales
        foreach ($citasPorMes as $citaMes) {
            $mesKey = $citaMes->mes;
            if (isset($mesesCompletos[$mesKey])) {
                $mesesCompletos[$mesKey]['total'] = (int)$citaMes->total;
                $mesesCompletos[$mesKey]['completadas'] = (int)$citaMes->completadas;
                $mesesCompletos[$mesKey]['confirmadas'] = (int)$citaMes->confirmadas;
                $mesesCompletos[$mesKey]['pendientes'] = (int)$citaMes->pendientes;
                $mesesCompletos[$mesKey]['canceladas'] = (int)$citaMes->canceladas;
            }
        }

        // Convertir a arrays para la gráfica
        $datos['citas_por_mes'] = [
            'labels' => array_map(function($mes) {
                return \Carbon\Carbon::createFromFormat('Y-m', $mes)->format('M Y');
            }, array_keys($mesesCompletos)),
            'total' => array_column($mesesCompletos, 'total'),
            'completadas' => array_column($mesesCompletos, 'completadas'),
            'confirmadas' => array_column($mesesCompletos, 'confirmadas'),
            'pendientes' => array_column($mesesCompletos, 'pendientes'),
            'canceladas' => array_column($mesesCompletos, 'canceladas'),
        ];

        // Gráfica de líneas: Evolución de actividades (últimos 6 meses)
        $actividadesPorMes = Actividad::where('id_paciente', $paciente->id_paciente)
            ->whereDate('fecha_asignacion', '>=', $fechaDesdeGrafica)
            ->whereDate('fecha_asignacion', '<=', $fechaHastaGrafica)
            ->selectRaw('DATE_FORMAT(fecha_asignacion, "%Y-%m") as mes,
                        COUNT(*) as total,
                        SUM(CASE WHEN completada = 1 THEN 1 ELSE 0 END) as completadas')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $datos['actividades_por_mes'] = [
            'labels' => $actividadesPorMes->pluck('mes')->map(function($mes) {
                return \Carbon\Carbon::createFromFormat('Y-m', $mes)->format('M Y');
            })->toArray(),
            'total' => $actividadesPorMes->pluck('total')->toArray(),
            'completadas' => $actividadesPorMes->pluck('completadas')->toArray(),
        ];

        // Gráfica circular (pie): Distribución de tipos de eventos
        $totalCitas = Cita::where('id_paciente', $paciente->id_paciente)
            ->when($filtros['fecha_desde'], fn($q) => $q->whereDate('fecha', '>=', $filtros['fecha_desde']))
            ->when($filtros['fecha_hasta'], fn($q) => $q->whereDate('fecha', '<=', $filtros['fecha_hasta']))
            ->count();
        
        $totalDiagnosticos = Diagnostico::where('id_paciente', $paciente->id_paciente)
            ->when($filtros['fecha_desde'], fn($q) => $q->whereDate('fecha', '>=', $filtros['fecha_desde']))
            ->when($filtros['fecha_hasta'], fn($q) => $q->whereDate('fecha', '<=', $filtros['fecha_hasta']))
            ->count();
        
        $totalTratamientos = Tratamiento::where('id_paciente', $paciente->id_paciente)
            ->when($filtros['fecha_desde'], fn($q) => $q->whereDate('fecha_inicio', '>=', $filtros['fecha_desde']))
            ->when($filtros['fecha_hasta'], fn($q) => $q->whereDate('fecha_inicio', '<=', $filtros['fecha_hasta']))
            ->count();
        
        $totalActividades = Actividad::where('id_paciente', $paciente->id_paciente)
            ->when($filtros['fecha_desde'], fn($q) => $q->whereDate('fecha_asignacion', '>=', $filtros['fecha_desde']))
            ->when($filtros['fecha_hasta'], fn($q) => $q->whereDate('fecha_asignacion', '<=', $filtros['fecha_hasta']))
            ->count();
        
        $totalAnalisis = Analisis::where('id_paciente', $paciente->id_paciente)
            ->when($filtros['fecha_desde'], fn($q) => $q->whereDate('fecha_analisis', '>=', $filtros['fecha_desde']))
            ->when($filtros['fecha_hasta'], fn($q) => $q->whereDate('fecha_analisis', '<=', $filtros['fecha_hasta']))
            ->count();

        $datos['distribucion_eventos'] = [
            'labels' => ['Citas', 'Diagnósticos', 'Tratamientos', 'Actividades', 'Análisis'],
            'data' => [$totalCitas, $totalDiagnosticos, $totalTratamientos, $totalActividades, $totalAnalisis],
        ];

        // Gráfica de barras: Cumplimiento de actividades por tipo
        $actividadesPorTipo = Actividad::where('id_paciente', $paciente->id_paciente)
            ->when($filtros['fecha_desde'], fn($q) => $q->whereDate('fecha_asignacion', '>=', $filtros['fecha_desde']))
            ->when($filtros['fecha_hasta'], fn($q) => $q->whereDate('fecha_asignacion', '<=', $filtros['fecha_hasta']))
            ->selectRaw('nombre,
                        COUNT(*) as total,
                        SUM(CASE WHEN completada = 1 THEN 1 ELSE 0 END) as completadas')
            ->groupBy('nombre')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $datos['actividades_por_tipo'] = [
            'labels' => $actividadesPorTipo->pluck('nombre')->toArray(),
            'total' => $actividadesPorTipo->pluck('total')->toArray(),
            'completadas' => $actividadesPorTipo->pluck('completadas')->toArray(),
        ];

        return $datos;
    }

    /**
     * Mostrar formulario para crear observación
     */
    public function createObservacion($id_paciente)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos pueden crear observaciones.');
        }

        $paciente = Paciente::with('usuario')->findOrFail($id_paciente);

        return view('seguimiento.observaciones.create', compact('paciente'));
    }

    /**
     * Guardar nueva observación
     */
    public function storeObservacion(Request $request, $id_paciente)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos pueden crear observaciones.');
        }

        $paciente = Paciente::findOrFail($id_paciente);

        $request->validate([
            'observacion' => 'required|string|max:2000',
            'fecha_observacion' => 'required|date',
            'tipo' => 'nullable|string|max:50',
        ], [
            'observacion.required' => 'La observación es obligatoria.',
            'fecha_observacion.required' => 'La fecha de observación es obligatoria.',
            'fecha_observacion.date' => 'La fecha debe ser válida.',
        ]);

        $id_medico = $user->medico->id_medico ?? null;
        if (!$id_medico && $user->isAdmin()) {
            // Si es admin, usar el primer médico disponible o permitir seleccionar
            $primerMedico = \App\Models\Medico::first();
            if ($primerMedico) {
                $id_medico = $primerMedico->id_medico;
            }
        }

        ObservacionSeguimiento::create([
            'id_paciente' => $paciente->id_paciente,
            'id_medico' => $id_medico,
            'observacion' => $request->observacion,
            'fecha_observacion' => $request->fecha_observacion,
            'tipo' => $request->tipo,
            'fecha_creacion' => now(),
        ]);

        $route = $user->isAdmin() ? 'admin.seguimiento.index' : 'medico.seguimiento.index';
        return redirect()->route($route, $paciente->id_paciente)
                        ->with('success', 'Observación médica creada exitosamente.');
    }

    /**
     * Mostrar formulario para editar observación
     */
    public function editObservacion($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos pueden editar observaciones.');
        }

        $observacion = ObservacionSeguimiento::with(['paciente.usuario', 'medico.usuario'])->findOrFail($id);

        // Verificar permisos: médicos solo pueden editar sus propias observaciones
        if ($user->isMedico() && $user->medico && $observacion->id_medico !== $user->medico->id_medico) {
            return redirect()->route('medico.seguimiento.index', $observacion->id_paciente)
                            ->with('error', 'No tienes permisos para editar esta observación.');
        }

        return view('seguimiento.observaciones.edit', compact('observacion'));
    }

    /**
     * Actualizar observación
     */
    public function updateObservacion(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos pueden editar observaciones.');
        }

        $observacion = ObservacionSeguimiento::findOrFail($id);

        // Verificar permisos
        if ($user->isMedico() && $user->medico && $observacion->id_medico !== $user->medico->id_medico) {
            return redirect()->route('medico.seguimiento.index', $observacion->id_paciente)
                            ->with('error', 'No tienes permisos para editar esta observación.');
        }

        $request->validate([
            'observacion' => 'required|string|max:2000',
            'fecha_observacion' => 'required|date',
            'tipo' => 'nullable|string|max:50',
        ], [
            'observacion.required' => 'La observación es obligatoria.',
            'fecha_observacion.required' => 'La fecha de observación es obligatoria.',
            'fecha_observacion.date' => 'La fecha debe ser válida.',
        ]);

        $observacion->update([
            'observacion' => $request->observacion,
            'fecha_observacion' => $request->fecha_observacion,
            'tipo' => $request->tipo,
        ]);

        $route = $user->isAdmin() ? 'admin.seguimiento.index' : 'medico.seguimiento.index';
        return redirect()->route($route, $observacion->id_paciente)
                        ->with('success', 'Observación médica actualizada exitosamente.');
    }

    /**
     * Eliminar observación
     */
    public function destroyObservacion($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos pueden eliminar observaciones.');
        }

        $observacion = ObservacionSeguimiento::findOrFail($id);

        // Verificar permisos
        if ($user->isMedico() && $user->medico && $observacion->id_medico !== $user->medico->id_medico) {
            return redirect()->route('medico.seguimiento.index', $observacion->id_paciente)
                            ->with('error', 'No tienes permisos para eliminar esta observación.');
        }

        $id_paciente = $observacion->id_paciente;
        $observacion->delete();

        $route = $user->isAdmin() ? 'admin.seguimiento.index' : 'medico.seguimiento.index';
        return redirect()->route($route, $id_paciente)
                        ->with('success', 'Observación médica eliminada exitosamente.');
    }

    /**
     * Generar reporte PDF del seguimiento
     */
    public function reportePDF(Request $request, $id_paciente)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para generar reportes.');
        }

        $paciente = Paciente::with('usuario')->findOrFail($id_paciente);

        // Aplicar filtros
        $filtros = $this->aplicarFiltros($request);

        // Consolidar datos
        $datosConsolidados = $this->consolidarDatos($paciente, $filtros);
        $estadisticas = $this->calcularEstadisticas($paciente, $filtros);
        $observaciones = $this->obtenerObservaciones($paciente, $filtros);

        $pdf = PDF::loadView('seguimiento.reporte-pdf', compact(
            'paciente',
            'datosConsolidados',
            'estadisticas',
            'observaciones',
            'filtros'
        ));

        $nombreArchivo = 'Seguimiento_' . str_replace(' ', '_', $paciente->usuario->nombre . '_' . $paciente->usuario->apPaterno) . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    /**
     * Generar reporte Excel del seguimiento
     */
    public function reporteExcel(Request $request, $id_paciente)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para generar reportes.');
        }

        $paciente = Paciente::with('usuario')->findOrFail($id_paciente);

        // Aplicar filtros
        $filtros = $this->aplicarFiltros($request);

        // Consolidar datos
        $datosConsolidados = $this->consolidarDatos($paciente, $filtros);
        $estadisticas = $this->calcularEstadisticas($paciente, $filtros);
        $observaciones = $this->obtenerObservaciones($paciente, $filtros);

        $nombreArchivo = 'Seguimiento_' . str_replace(' ', '_', $paciente->usuario->nombre . '_' . $paciente->usuario->apPaterno) . '_' . now()->format('Y-m-d') . '.xlsx';

        // Para la versión 3.1 de maatwebsite/excel, usar clases Export
        return Excel::download(
            new SeguimientoExport($paciente, $datosConsolidados, $estadisticas, $observaciones),
            $nombreArchivo
        );
    }
}
