<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cita;
use App\Models\Tratamiento;
use App\Models\Diagnostico;
use App\Models\User;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\HistorialClinico;
use App\Models\Analisis;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return $this->admin();
        } elseif ($user->isMedico()) {
            return $this->medico();
        } else {
            return $this->paciente();
        }
    }

    public function admin()
    {
        $stats = [
            'total_users' => User::count(),
            'total_medicos' => User::where('rol', 'medico')->count(),
            'total_pacientes' => User::where('rol', 'paciente')->count(),
            'active_users' => User::where('activo', 1)->count(),
            'recent_citas' => Cita::with(['paciente.usuario', 'medico.usuario'])
                                 ->orderBy('fecha', 'desc')
                                 ->limit(5)
                                 ->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function medico()
    {
        $user = Auth::user();
        $medico = $user->medico;

        // Si no existe el médico, crear uno temporal o manejar el caso
        if (!$medico) {
            // Crear un médico temporal para usuarios que no tienen registro en la tabla medicos
            $medico = new Medico();
            $medico->id_medico = 0; // ID temporal
            $medico->id_usuario = $user->id_usuario;
        }

        $stats = [
            'total_citas' => $medico->id_medico > 0 ? Cita::where('id_medico', $medico->id_medico)->count() : 0,
            'citas_pendientes' => $medico->id_medico > 0 ? Cita::where('id_medico', $medico->id_medico)
                                     ->where('estado', 'pendiente')
                                     ->count() : 0,
            'citas_hoy' => $medico->id_medico > 0 ? Cita::where('id_medico', $medico->id_medico)
                              ->whereDate('fecha', today())
                              ->count() : 0,
            'tratamientos_activos' => $medico->id_medico > 0 ? Tratamiento::where('id_medico', $medico->id_medico)
                                                ->where('activo', 1)
                                                ->count() : 0,
        ];

        $citasHoy = $medico->id_medico > 0 ? Cita::with(['paciente.usuario'])
                        ->where('id_medico', $medico->id_medico)
                        ->whereDate('fecha', today())
                        ->orderBy('fecha')
                        ->get() : collect();

        $citasPendientes = $medico->id_medico > 0 ? Cita::with(['paciente.usuario'])
                               ->where('id_medico', $medico->id_medico)
                               ->where('estado', 'pendiente')
                               ->orderBy('fecha')
                               ->limit(5)
                               ->get() : collect();

        return view('medico.dashboard', compact('stats', 'citasHoy', 'citasPendientes'));
    }

    public function paciente()
    {
        $user = Auth::user();
        $paciente = $user->paciente;

        // Si no existe el paciente, crear uno temporal o manejar el caso
        if (!$paciente) {
            // Crear un paciente temporal para usuarios que no tienen registro en la tabla pacientes
            $paciente = new Paciente();
            $paciente->id_paciente = 0; // ID temporal
            $paciente->id_usuario = $user->id_usuario;
        }

        $stats = [
            'total_citas' => $paciente->id_paciente > 0 ? Cita::where('id_paciente', $paciente->id_paciente)->count() : 0,
            'citas_pendientes' => $paciente->id_paciente > 0 ? Cita::with(['medico.usuario'])
                                     ->where('id_paciente', $paciente->id_paciente)
                                     ->where('estado', 'pendiente')
                                     ->count() : 0,
            'citas_proximas' => $paciente->id_paciente > 0 ? Cita::with(['medico.usuario'])
                                   ->where('id_paciente', $paciente->id_paciente)
                                   ->where('fecha', '>', now())
                                   ->where('estado', '!=', 'cancelada')
                                   ->count() : 0,
            'citas_completadas' => $paciente->id_paciente > 0 ? Cita::where('id_paciente', $paciente->id_paciente)
                                         ->where('estado', 'completada')
                                         ->count() : 0,
            'tratamientos_activos' => $paciente->id_paciente > 0 ? Tratamiento::where('id_paciente', $paciente->id_paciente)
                                                ->where('activo', 1)
                                                ->count() : 0,
            'tratamientos_completados' => $paciente->id_paciente > 0 ? Tratamiento::where('id_paciente', $paciente->id_paciente)
                                                      ->where('activo', 0)
                                                      ->count() : 0,
            'total_diagnosticos' => $paciente->id_paciente > 0 ? Diagnostico::where('id_paciente', $paciente->id_paciente)->count() : 0,
            'historial_clinico' => $paciente->id_paciente > 0 ? $paciente->historialClinico()->count() : 0,
            'total_analisis' => $paciente->id_paciente > 0 ? Analisis::where('id_paciente', $paciente->id_paciente)->count() : 0,
        ];

        $citasProximas = $paciente->id_paciente > 0 ? Cita::with(['medico.usuario'])
                             ->where('id_paciente', $paciente->id_paciente)
                             ->where('fecha', '>', now())
                             ->where('estado', '!=', 'cancelada')
                             ->orderBy('fecha')
                             ->limit(5)
                             ->get() : collect();

        $tratamientosActivos = $paciente->id_paciente > 0 ? Tratamiento::with(['medico.usuario'])
                                         ->where('id_paciente', $paciente->id_paciente)
                                         ->where('activo', 1)
                                         ->orderBy('fecha_inicio', 'desc')
                                         ->limit(5)
                                         ->get() : collect();

        // Datos para gráficas
        $datosGraficas = $this->obtenerDatosGraficas($paciente);

        return view('paciente.dashboard', compact('stats', 'citasProximas', 'tratamientosActivos', 'datosGraficas', 'paciente'));
    }

    private function obtenerDatosGraficas($paciente)
    {
        // Si no hay paciente real, retornar datos vacíos
        if ($paciente->id_paciente <= 0) {
            return $this->datosGraficasVacios();
        }

        // Obtener datos de los últimos 12 meses
        $fechaInicio = now()->subMonths(12);
        
        // Preparar meses para las gráficas
        $meses = [];
        $mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        for ($i = 11; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $meses[] = [
                'numero' => $fecha->month,
                'nombre' => $mesesNombres[$fecha->month - 1],
                'año' => $fecha->year,
                'fecha_completa' => $fecha->format('Y-m')
            ];
        }

        // 1. Citas por mes con estados
        $citasPorMes = Cita::where('id_paciente', $paciente->id_paciente)
                          ->where('fecha', '>=', $fechaInicio)
                          ->selectRaw('DATE_FORMAT(fecha, "%Y-%m") as mes, estado, COUNT(*) as total')
                          ->groupBy('mes', 'estado')
                          ->get()
                          ->groupBy('mes');

        $citasGrafica = [
            'labels' => array_column($meses, 'nombre'),
            'completadas' => [],
            'pendientes' => [],
            'canceladas' => [],
            'total' => []
        ];

        foreach ($meses as $mes) {
            $mesData = $citasPorMes->get($mes['fecha_completa'], collect());
            $citasGrafica['completadas'][] = $mesData->where('estado', 'completada')->sum('total');
            $citasGrafica['pendientes'][] = $mesData->where('estado', 'pendiente')->sum('total');
            $citasGrafica['canceladas'][] = $mesData->where('estado', 'cancelada')->sum('total');
            $citasGrafica['total'][] = $mesData->sum('total');
        }

        // 2. Diagnósticos por mes
        $diagnosticosPorMes = Diagnostico::where('id_paciente', $paciente->id_paciente)
                                        ->where('fecha', '>=', $fechaInicio)
                                        ->selectRaw('DATE_FORMAT(fecha, "%Y-%m") as mes, COUNT(*) as total')
                                        ->groupBy('mes')
                                        ->pluck('total', 'mes');

        $diagnosticosGrafica = [
            'labels' => array_column($meses, 'nombre'),
            'data' => []
        ];

        foreach ($meses as $mes) {
            $diagnosticosGrafica['data'][] = $diagnosticosPorMes->get($mes['fecha_completa'], 0);
        }

        // 3. Tratamientos activos vs completados
        $tratamientosActivos = Tratamiento::where('id_paciente', $paciente->id_paciente)
                                         ->where('activo', 1)
                                         ->count();
        $tratamientosCompletados = Tratamiento::where('id_paciente', $paciente->id_paciente)
                                               ->where('activo', 0)
                                               ->count();

        // 4. Tratamientos por tipo
        $tratamientosPorTipo = Tratamiento::where('id_paciente', $paciente->id_paciente)
                                         ->selectRaw('nombre, COUNT(*) as total')
                                         ->groupBy('nombre')
                                         ->orderBy('total', 'desc')
                                         ->limit(5)
                                         ->get();

        // 5. Análisis por tipo
        $analisisPorTipo = Analisis::where('id_paciente', $paciente->id_paciente)
                                  ->selectRaw('tipo_estudio, COUNT(*) as total')
                                  ->groupBy('tipo_estudio')
                                  ->orderBy('total', 'desc')
                                  ->limit(5)
                                  ->get();

        // 6. Historial clínico por mes
        $historialPorMes = HistorialClinico::where('id_paciente', $paciente->id_paciente)
                                           ->where('fecha_registro', '>=', $fechaInicio)
                                           ->selectRaw('DATE_FORMAT(fecha_registro, "%Y-%m") as mes, COUNT(*) as total')
                                           ->groupBy('mes')
                                           ->pluck('total', 'mes');

        $historialGrafica = [
            'labels' => array_column($meses, 'nombre'),
            'data' => []
        ];

        foreach ($meses as $mes) {
            $historialGrafica['data'][] = $historialPorMes->get($mes['fecha_completa'], 0);
        }

        // 7. Análisis por mes
        $analisisPorMes = Analisis::where('id_paciente', $paciente->id_paciente)
                                 ->where('fecha_analisis', '>=', $fechaInicio)
                                 ->selectRaw('DATE_FORMAT(fecha_analisis, "%Y-%m") as mes, COUNT(*) as total')
                                 ->groupBy('mes')
                                 ->pluck('total', 'mes');

        $analisisGrafica = [
            'labels' => array_column($meses, 'nombre'),
            'data' => []
        ];

        foreach ($meses as $mes) {
            $analisisGrafica['data'][] = $analisisPorMes->get($mes['fecha_completa'], 0);
        }

        return [
            'citas_por_mes' => $citasGrafica,
            'diagnosticos_por_mes' => $diagnosticosGrafica,
            'tratamientos_activos' => $tratamientosActivos,
            'tratamientos_completados' => $tratamientosCompletados,
            'tratamientos_por_tipo' => $tratamientosPorTipo,
            'analisis_por_tipo' => $analisisPorTipo,
            'historial_por_mes' => $historialGrafica,
            'analisis_por_mes' => $analisisGrafica,
        ];
    }

    private function datosGraficasVacios()
    {
        $mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $meses = [];
        for ($i = 11; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $meses[] = $mesesNombres[$fecha->month - 1];
        }

        return [
            'citas_por_mes' => [
                'labels' => $meses,
                'completadas' => array_fill(0, 12, 0),
                'pendientes' => array_fill(0, 12, 0),
                'canceladas' => array_fill(0, 12, 0),
                'total' => array_fill(0, 12, 0)
            ],
            'diagnosticos_por_mes' => [
                'labels' => $meses,
                'data' => array_fill(0, 12, 0)
            ],
            'tratamientos_activos' => 0,
            'tratamientos_completados' => 0,
            'tratamientos_por_tipo' => collect(),
            'analisis_por_tipo' => collect(),
            'historial_por_mes' => [
                'labels' => $meses,
                'data' => array_fill(0, 12, 0)
            ],
            'analisis_por_mes' => [
                'labels' => $meses,
                'data' => array_fill(0, 12, 0)
            ],
        ];
    }
} 