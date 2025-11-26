<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Models\ObservacionSeguimiento;
use App\Models\Diagnostico;
use App\Models\Actividad;
use App\Models\AnalisisClinico;
use App\Models\CatalogoDiagnostico;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Mostrar el índice de reportes disponibles
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden acceder a los reportes.');
        }

        // Lista de reportes disponibles
        $reportes = [
            [
                'id' => 'citas',
                'titulo' => 'Reportes de Citas',
                'descripcion' => 'Genera reportes de citas médicas con gráfica de pastel mostrando distribución por estado, médico, especialidad, etc.',
                'icono' => 'calendar',
                'color' => 'blue',
                'ruta' => 'admin.reportes.citas'
            ],
            [
                'id' => 'tratamientos',
                'titulo' => 'Reportes de Tratamientos',
                'descripcion' => 'Reportes detallados de tratamientos médicos, incluyendo tratamientos activos, finalizados y estadísticas.',
                'icono' => 'pills',
                'color' => 'green',
                'ruta' => 'admin.reportes.tratamientos'
            ],
            [
                'id' => 'seguimiento',
                'titulo' => 'Reportes de Seguimiento',
                'descripcion' => 'Informes consolidados del seguimiento de pacientes con observaciones médicas y evolución.',
                'icono' => 'chart-line',
                'color' => 'purple',
                'ruta' => 'admin.reportes.seguimiento'
            ],
            [
                'id' => 'diagnosticos',
                'titulo' => 'Reportes de Diagnósticos por Período',
                'descripcion' => 'Reportes de diagnósticos con gráfica de barras mostrando distribución por período de tiempo.',
                'icono' => 'file-medical',
                'color' => 'red',
                'ruta' => 'admin.reportes.diagnosticos'
            ],
            [
                'id' => 'actividades',
                'titulo' => 'Reportes de Efectividad de Actividades',
                'descripcion' => 'Análisis de efectividad de actividades asignadas a pacientes, tasas de completitud y cumplimiento.',
                'icono' => 'tasks',
                'color' => 'yellow',
                'ruta' => 'admin.reportes.actividades'
            ],
            [
                'id' => 'analisis',
                'titulo' => 'Reportes de Análisis Clínicos Más Repetidos',
                'descripcion' => 'Reportes de los análisis clínicos más frecuentes realizados a los pacientes.',
                'icono' => 'flask',
                'color' => 'indigo',
                'ruta' => 'admin.reportes.analisis'
            ]
        ];

        return view('admin.reportes.index', compact('reportes'));
    }

    /**
     * Reporte de Citas - Vista con filtros
     */
    public function citas(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden acceder a los reportes.');
        }

        // Obtener especialidades para el filtro
        $especialidades = Cita::whereNotNull('especialidad_medica')
                             ->distinct()
                             ->pluck('especialidad_medica')
                             ->sort()
                             ->values();

        // Obtener médicos para el filtro
        $medicos = Medico::with('usuario')
                        ->get()
                        ->filter(function($medico) {
                            return $medico->usuario !== null;
                        })
                        ->map(function($medico) {
                            return [
                                'id' => $medico->id_medico,
                                'nombre' => trim(($medico->usuario->nombre ?? '') . ' ' . ($medico->usuario->apPaterno ?? '') . ' ' . ($medico->usuario->apMaterno ?? ''))
                            ];
                        })
                        ->values();

        return view('admin.reportes.citas', compact('especialidades', 'medicos'));
    }

    /**
     * Generar PDF del Reporte de Citas
     */
    public function citasPDF(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden acceder a los reportes.');
        }

        // Obtener filtros
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $tipoDistribucion = $request->input('tipo_distribucion', 'estado'); // estado, medico, especialidad
        $idMedico = $request->input('id_medico');
        $especialidad = $request->input('especialidad');

        // Construir query base
        $query = Cita::with(['paciente.usuario', 'medico.usuario']);

        // Aplicar filtros de fecha
        if ($fechaDesde) {
            $query->whereDate('fecha', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->whereDate('fecha', '<=', $fechaHasta);
        }
        if ($idMedico) {
            $query->where('id_medico', $idMedico);
        }
        if ($especialidad) {
            $query->where('especialidad_medica', $especialidad);
        }

        $citas = $query->get();
        $totalCitas = $citas->count();

        // Generar datos para la gráfica según el tipo de distribución
        $datosGrafica = [];
        $tituloGrafica = '';
        $colores = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'];

        switch ($tipoDistribucion) {
            case 'estado':
                $tituloGrafica = 'Distribución por Estado';
                $agrupacion = $citas->groupBy('estado');
                $estados = [
                    'pendiente' => 'Pendientes',
                    'confirmada' => 'Confirmadas',
                    'completada' => 'Completadas',
                    'cancelada' => 'Canceladas'
                ];
                foreach ($estados as $estado => $label) {
                    $cantidad = $agrupacion->get($estado, collect())->count();
                    if ($cantidad > 0) {
                        $porcentaje = ($cantidad / $totalCitas) * 100;
                        $datosGrafica[] = [
                            'label' => $label,
                            'cantidad' => $cantidad,
                            'porcentaje' => round($porcentaje, 2)
                        ];
                    }
                }
                break;

            case 'medico':
                $tituloGrafica = 'Distribución por Médico';
                $agrupacion = $citas->groupBy('id_medico');
                foreach ($agrupacion as $idMedico => $citasMedico) {
                    $medico = $citasMedico->first()->medico;
                    $nombreMedico = $medico ? ($medico->usuario->nombre . ' ' . $medico->usuario->apPaterno) : 'Sin médico';
                    $cantidad = $citasMedico->count();
                    $porcentaje = ($cantidad / $totalCitas) * 100;
                    $datosGrafica[] = [
                        'label' => $nombreMedico,
                        'cantidad' => $cantidad,
                        'porcentaje' => round($porcentaje, 2)
                    ];
                }
                // Ordenar por cantidad descendente
                usort($datosGrafica, function($a, $b) {
                    return $b['cantidad'] - $a['cantidad'];
                });
                break;

            case 'especialidad':
                $tituloGrafica = 'Distribución por Especialidad';
                $agrupacion = $citas->groupBy('especialidad_medica');
                foreach ($agrupacion as $especialidad => $citasEspecialidad) {
                    $label = $especialidad ?: 'Sin especialidad';
                    $cantidad = $citasEspecialidad->count();
                    $porcentaje = ($cantidad / $totalCitas) * 100;
                    $datosGrafica[] = [
                        'label' => $label,
                        'cantidad' => $cantidad,
                        'porcentaje' => round($porcentaje, 2)
                    ];
                }
                // Ordenar por cantidad descendente
                usort($datosGrafica, function($a, $b) {
                    return $b['cantidad'] - $a['cantidad'];
                });
                break;
        }

        // Estadísticas generales
        $estadisticas = [
            'total' => $totalCitas,
            'pendientes' => $citas->where('estado', 'pendiente')->count(),
            'confirmadas' => $citas->where('estado', 'confirmada')->count(),
            'completadas' => $citas->where('estado', 'completada')->count(),
            'canceladas' => $citas->where('estado', 'cancelada')->count(),
        ];

        // Generar gráfica de pastel como imagen base64
        $imagenGrafica = $this->generarGraficaPastelImagen($datosGrafica, $colores, $totalCitas);

        // Datos para el PDF
        $datos = [
            'citas' => $citas,
            'datosGrafica' => $datosGrafica,
            'tituloGrafica' => $tituloGrafica,
            'estadisticas' => $estadisticas,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'tipoDistribucion' => $tipoDistribucion,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
            'colores' => $colores,
            'imagenGrafica' => $imagenGrafica,
            'logo' => $this->obtenerLogoBase64()
        ];

        // Generar PDF
        $pdf = PDF::loadView('admin.reportes.citas-pdf', $datos);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-html5-parser', true);
        $pdf->setOption('isPhpEnabled', true);
        
        $nombreArchivo = 'Reporte_Citas_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }

    /**
     * Reporte de Tratamientos - Vista de filtros
     */
    public function tratamientos()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden acceder a los reportes.');
        }

        $medicos = Medico::with('usuario')
                        ->get()
                        ->map(function($medico) {
                            return [
                                'id' => $medico->id_medico,
                                'nombre' => $medico->usuario->nombre . ' ' . $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno
                            ];
                        });

        $pacientes = Paciente::with('usuario')
                        ->get()
                        ->map(function($paciente) {
                            return [
                                'id' => $paciente->id_paciente,
                                'nombre' => $paciente->usuario->nombre . ' ' . $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno
                            ];
                        });

        $nombresTratamientos = Tratamiento::distinct()
                                    ->pluck('nombre')
                                    ->sort()
                                    ->values();

        return view('admin.reportes.tratamientos', compact('medicos', 'pacientes', 'nombresTratamientos'));
    }

    /**
     * Generar PDF del Reporte de Tratamientos
     */
    public function tratamientosPDF(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden generar reportes.');
        }

        // Obtener filtros
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $estado = $request->input('estado'); // 'activo', 'inactivo', 'todos'
        $tipoDistribucion = $request->input('tipo_distribucion', 'estado'); // estado, medico, paciente, frecuencia
        $medicoId = $request->input('medico_id');
        $pacienteId = $request->input('paciente_id');
        $nombreTratamiento = $request->input('nombre_tratamiento');

        // Construir query base
        $query = Tratamiento::with(['paciente.usuario', 'medico.usuario', 'diagnostico.catalogoDiagnostico']);

        // Aplicar filtros
        if ($fechaDesde) {
            $query->whereDate('fecha_inicio', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->whereDate('fecha_inicio', '<=', $fechaHasta);
        }
        if ($estado && $estado !== 'todos') {
            $query->where('activo', $estado === 'activo');
        }
        if ($medicoId) {
            $query->where('id_medico', $medicoId);
        }
        if ($pacienteId) {
            $query->where('id_paciente', $pacienteId);
        }
        if ($nombreTratamiento) {
            $query->where('nombre', 'like', '%' . $nombreTratamiento . '%');
        }

        $tratamientos = $query->orderBy('fecha_inicio', 'desc')->get();
        $totalTratamientos = $tratamientos->count();

        // Estadísticas generales
        $estadisticas = [
            'total' => $totalTratamientos,
            'activos' => $tratamientos->where('activo', true)->count(),
            'inactivos' => $tratamientos->where('activo', false)->count(),
        ];

        // Generar estadísticas detalladas según el tipo de distribución
        $estadisticasDetalladas = [];
        
        switch ($tipoDistribucion) {
            case 'estado':
                $activos = $tratamientos->where('activo', true)->count();
                $inactivos = $tratamientos->where('activo', false)->count();
                $estadisticasDetalladas = [
                    'tipo' => 'estado',
                    'titulo' => 'Distribución por Estado',
                    'datos' => [
                        [
                            'label' => 'Activos',
                            'cantidad' => $activos,
                            'porcentaje' => $totalTratamientos > 0 ? round(($activos / $totalTratamientos) * 100, 2) : 0
                        ],
                        [
                            'label' => 'Inactivos',
                            'cantidad' => $inactivos,
                            'porcentaje' => $totalTratamientos > 0 ? round(($inactivos / $totalTratamientos) * 100, 2) : 0
                        ]
                    ]
                ];
                break;

            case 'medico':
                $porMedico = $tratamientos->groupBy('id_medico')->map(function ($items, $medicoId) {
                    $medico = $items->first()->medico;
                    return [
                        'nombre' => $medico->usuario->nombre,
                        'apellido' => $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno,
                        'label' => $medico->usuario->nombre . ' ' . $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno,
                        'cantidad' => $items->count(),
                        'activos' => $items->where('activo', true)->count(),
                        'inactivos' => $items->where('activo', false)->count()
                    ];
                })->values()->sortByDesc('cantidad');
                
                $estadisticasDetalladas = [
                    'tipo' => 'medico',
                    'titulo' => 'Distribución por Médico',
                    'datos' => $porMedico->map(function($item) use ($totalTratamientos) {
                        $item['porcentaje'] = $totalTratamientos > 0 ? round(($item['cantidad'] / $totalTratamientos) * 100, 2) : 0;
                        return $item;
                    })->toArray()
                ];
                break;

            case 'paciente':
                $porPaciente = $tratamientos->groupBy('id_paciente')->map(function ($items, $pacienteId) {
                    $paciente = $items->first()->paciente;
                    return [
                        'nombre' => $paciente->usuario->nombre,
                        'apellido' => $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno,
                        'label' => $paciente->usuario->nombre . ' ' . $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno,
                        'cantidad' => $items->count(),
                        'activos' => $items->where('activo', true)->count(),
                        'inactivos' => $items->where('activo', false)->count()
                    ];
                })->values()->sortByDesc('cantidad');
                
                $estadisticasDetalladas = [
                    'tipo' => 'paciente',
                    'titulo' => 'Distribución por Paciente',
                    'datos' => $porPaciente->map(function($item) use ($totalTratamientos) {
                        $item['porcentaje'] = $totalTratamientos > 0 ? round(($item['cantidad'] / $totalTratamientos) * 100, 2) : 0;
                        return $item;
                    })->toArray()
                ];
                break;

            case 'frecuencia':
                $porFrecuencia = $tratamientos->groupBy('frecuencia')->map(function ($items, $frecuencia) {
                    return [
                        'label' => $frecuencia ?: 'No especificada',
                        'cantidad' => $items->count(),
                        'activos' => $items->where('activo', true)->count(),
                        'inactivos' => $items->where('activo', false)->count()
                    ];
                })->values()->sortByDesc('cantidad');
                
                $estadisticasDetalladas = [
                    'tipo' => 'frecuencia',
                    'titulo' => 'Distribución por Frecuencia',
                    'datos' => $porFrecuencia->map(function($item) use ($totalTratamientos) {
                        $item['porcentaje'] = $totalTratamientos > 0 ? round(($item['cantidad'] / $totalTratamientos) * 100, 2) : 0;
                        return $item;
                    })->toArray()
                ];
                break;
        }

        // Estadísticas adicionales
        $tratamientosPorNombre = $tratamientos->groupBy('nombre')->map(function ($items) {
            return [
                'nombre' => $items->first()->nombre,
                'cantidad' => $items->count(),
                'activos' => $items->where('activo', true)->count(),
                'inactivos' => $items->where('activo', false)->count()
            ];
        })->values()->sortByDesc('cantidad')->take(10);

        $tratamientosPorDuracion = $tratamientos->groupBy('duracion')->map(function ($items) {
            return [
                'duracion' => $items->first()->duracion ?: 'No especificada',
                'cantidad' => $items->count(),
                'activos' => $items->where('activo', true)->count(),
                'inactivos' => $items->where('activo', false)->count()
            ];
        })->values()->sortByDesc('cantidad');

        // Obtener información de médico y paciente si se filtró
        $medicoFiltrado = null;
        if ($medicoId) {
            $medico = Medico::with('usuario')->find($medicoId);
            if ($medico) {
                $medicoFiltrado = $medico->usuario->nombre . ' ' . $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno;
            }
        }

        $pacienteFiltrado = null;
        if ($pacienteId) {
            $paciente = Paciente::with('usuario')->find($pacienteId);
            if ($paciente) {
                $pacienteFiltrado = $paciente->usuario->nombre . ' ' . $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno;
            }
        }

        // Datos para el PDF
        $datos = [
            'tratamientos' => $tratamientos,
            'estadisticas' => $estadisticas,
            'estadisticasDetalladas' => $estadisticasDetalladas,
            'tratamientosPorNombre' => $tratamientosPorNombre,
            'tratamientosPorDuracion' => $tratamientosPorDuracion,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'estado' => $estado,
            'tipoDistribucion' => $tipoDistribucion,
            'medicoFiltrado' => $medicoFiltrado,
            'pacienteFiltrado' => $pacienteFiltrado,
            'nombreTratamiento' => $nombreTratamiento,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
            'logo' => $this->obtenerLogoBase64()
        ];

        // Generar PDF
        $pdf = PDF::loadView('admin.reportes.tratamientos-pdf', $datos);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-html5-parser', true);
        $pdf->setOption('isPhpEnabled', true);
        
        $nombreArchivo = 'Reporte_Tratamientos_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }

    /**
     * Reporte de Seguimiento - Vista de filtros
     */
    public function seguimiento()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden acceder a los reportes.');
        }

        $medicos = Medico::with('usuario')
                        ->get()
                        ->map(function($medico) {
                            return [
                                'id' => $medico->id_medico,
                                'nombre' => $medico->usuario->nombre . ' ' . $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno
                            ];
                        });

        $pacientes = Paciente::with('usuario')
                        ->get()
                        ->map(function($paciente) {
                            return [
                                'id' => $paciente->id_paciente,
                                'nombre' => $paciente->usuario->nombre . ' ' . $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno
                            ];
                        });

        // Obtener tipos de observación únicos
        $tiposObservacion = ObservacionSeguimiento::distinct()
                                    ->whereNotNull('tipo')
                                    ->pluck('tipo')
                                    ->sort()
                                    ->values();

        return view('admin.reportes.seguimiento', compact('medicos', 'pacientes', 'tiposObservacion'));
    }

    /**
     * Generar PDF del Reporte de Seguimiento
     */
    public function seguimientoPDF(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden generar reportes.');
        }

        // Obtener filtros
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $tipoDistribucion = $request->input('tipo_distribucion', 'tipo'); // tipo, medico, paciente
        $medicoId = $request->input('medico_id');
        $pacienteId = $request->input('paciente_id');
        $tipoObservacion = $request->input('tipo_observacion');

        // Construir query base
        $query = ObservacionSeguimiento::with(['paciente.usuario', 'medico.usuario']);

        // Aplicar filtros
        if ($fechaDesde) {
            $query->whereDate('fecha_observacion', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->whereDate('fecha_observacion', '<=', $fechaHasta);
        }
        if ($medicoId) {
            $query->where('id_medico', $medicoId);
        }
        if ($pacienteId) {
            $query->where('id_paciente', $pacienteId);
        }
        if ($tipoObservacion) {
            $query->where('tipo', $tipoObservacion);
        }

        $observaciones = $query->orderBy('fecha_observacion', 'desc')->get();
        $totalObservaciones = $observaciones->count();

        // Estadísticas generales
        $estadisticas = [
            'total' => $totalObservaciones,
        ];

        // Generar estadísticas detalladas según el tipo de distribución
        $estadisticasDetalladas = [];
        
        switch ($tipoDistribucion) {
            case 'tipo':
                $porTipo = $observaciones->groupBy('tipo')->map(function ($items, $tipo) {
                    return [
                        'label' => $tipo ?: 'Sin tipo',
                        'cantidad' => $items->count(),
                    ];
                })->values()->sortByDesc('cantidad');
                
                $estadisticasDetalladas = [
                    'tipo' => 'tipo',
                    'titulo' => 'Distribución por Tipo de Observación',
                    'datos' => $porTipo->map(function($item) use ($totalObservaciones) {
                        $item['porcentaje'] = $totalObservaciones > 0 ? round(($item['cantidad'] / $totalObservaciones) * 100, 2) : 0;
                        return $item;
                    })->toArray()
                ];
                break;

            case 'medico':
                $porMedico = $observaciones->groupBy('id_medico')->map(function ($items, $medicoId) {
                    $medico = $items->first()->medico;
                    return [
                        'nombre' => $medico->usuario->nombre,
                        'apellido' => $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno,
                        'label' => $medico->usuario->nombre . ' ' . $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno,
                        'cantidad' => $items->count(),
                    ];
                })->values()->sortByDesc('cantidad');
                
                $estadisticasDetalladas = [
                    'tipo' => 'medico',
                    'titulo' => 'Distribución por Médico',
                    'datos' => $porMedico->map(function($item) use ($totalObservaciones) {
                        $item['porcentaje'] = $totalObservaciones > 0 ? round(($item['cantidad'] / $totalObservaciones) * 100, 2) : 0;
                        return $item;
                    })->toArray()
                ];
                break;

            case 'paciente':
                $porPaciente = $observaciones->groupBy('id_paciente')->map(function ($items, $pacienteId) {
                    $paciente = $items->first()->paciente;
                    return [
                        'nombre' => $paciente->usuario->nombre,
                        'apellido' => $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno,
                        'label' => $paciente->usuario->nombre . ' ' . $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno,
                        'cantidad' => $items->count(),
                    ];
                })->values()->sortByDesc('cantidad');
                
                $estadisticasDetalladas = [
                    'tipo' => 'paciente',
                    'titulo' => 'Distribución por Paciente',
                    'datos' => $porPaciente->map(function($item) use ($totalObservaciones) {
                        $item['porcentaje'] = $totalObservaciones > 0 ? round(($item['cantidad'] / $totalObservaciones) * 100, 2) : 0;
                        return $item;
                    })->toArray()
                ];
                break;
        }

        // Estadísticas adicionales
        $observacionesPorTipo = $observaciones->groupBy('tipo')->map(function ($items, $tipo) {
            return [
                'tipo' => $tipo ?: 'Sin tipo',
                'cantidad' => $items->count()
            ];
        })->values()->sortByDesc('cantidad');

        // Obtener información de médico y paciente si se filtró
        $medicoFiltrado = null;
        if ($medicoId) {
            $medico = Medico::with('usuario')->find($medicoId);
            if ($medico) {
                $medicoFiltrado = $medico->usuario->nombre . ' ' . $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno;
            }
        }

        $pacienteFiltrado = null;
        if ($pacienteId) {
            $paciente = Paciente::with('usuario')->find($pacienteId);
            if ($paciente) {
                $pacienteFiltrado = $paciente->usuario->nombre . ' ' . $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno;
            }
        }

        // Datos para el PDF
        $datos = [
            'observaciones' => $observaciones,
            'estadisticas' => $estadisticas,
            'estadisticasDetalladas' => $estadisticasDetalladas,
            'observacionesPorTipo' => $observacionesPorTipo,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'tipoDistribucion' => $tipoDistribucion,
            'medicoFiltrado' => $medicoFiltrado,
            'pacienteFiltrado' => $pacienteFiltrado,
            'tipoObservacion' => $tipoObservacion,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
            'logo' => $this->obtenerLogoBase64()
        ];

        // Generar PDF
        $pdf = PDF::loadView('admin.reportes.seguimiento-pdf', $datos);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-html5-parser', true);
        $pdf->setOption('isPhpEnabled', true);
        
        $nombreArchivo = 'Reporte_Seguimiento_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }

    /**
     * Reporte de Diagnósticos - Vista con filtros
     */
    public function diagnosticos()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden acceder a los reportes.');
        }

        $medicos = Medico::with('usuario')
                        ->get()
                        ->map(function($medico) {
                            return [
                                'id' => $medico->id_medico,
                                'nombre' => $medico->usuario->nombre . ' ' . $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno
                            ];
                        });

        $pacientes = Paciente::with('usuario')
                        ->get()
                        ->map(function($paciente) {
                            return [
                                'id' => $paciente->id_paciente,
                                'nombre' => $paciente->usuario->nombre . ' ' . $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno
                            ];
                        });

        return view('admin.reportes.diagnosticos', compact('medicos', 'pacientes'));
    }

    /**
     * Generar PDF del Reporte de Diagnósticos
     */
    public function diagnosticosPDF(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden generar reportes.');
        }

        // Obtener filtros
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $tipoPeriodo = $request->input('tipo_periodo', 'mes'); // mes, trimestre, año
        $medicoId = $request->input('medico_id');
        $pacienteId = $request->input('paciente_id');

        // Construir query base
        $query = Diagnostico::with(['paciente.usuario', 'medico.usuario', 'catalogoDiagnostico']);

        // Aplicar filtros
        if ($fechaDesde) {
            $query->whereDate('fecha', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->whereDate('fecha', '<=', $fechaHasta);
        }
        if ($medicoId) {
            $query->where('id_medico', $medicoId);
        }
        if ($pacienteId) {
            $query->where('id_paciente', $pacienteId);
        }

        $diagnosticos = $query->orderBy('fecha', 'desc')->get();
        $totalDiagnosticos = $diagnosticos->count();

        // Agrupar por período según el tipo seleccionado
        $datosGrafica = [];
        $tituloGrafica = '';

        switch ($tipoPeriodo) {
            case 'mes':
                $tituloGrafica = 'Distribución por Mes';
                $agrupacion = $diagnosticos->groupBy(function($diagnostico) {
                    return \Carbon\Carbon::parse($diagnostico->fecha)->format('Y-m');
                });
                foreach ($agrupacion as $periodo => $items) {
                    $fecha = \Carbon\Carbon::createFromFormat('Y-m', $periodo);
                    $label = $fecha->format('M Y');
                    $cantidad = $items->count();
                    $porcentaje = $totalDiagnosticos > 0 ? round(($cantidad / $totalDiagnosticos) * 100, 2) : 0;
                    $datosGrafica[] = [
                        'label' => $label,
                        'cantidad' => $cantidad,
                        'porcentaje' => $porcentaje,
                        'periodo' => $periodo
                    ];
                }
                break;

            case 'trimestre':
                $tituloGrafica = 'Distribución por Trimestre';
                $agrupacion = $diagnosticos->groupBy(function($diagnostico) {
                    $fecha = \Carbon\Carbon::parse($diagnostico->fecha);
                    $trimestre = ceil($fecha->month / 3);
                    return $fecha->year . '-T' . $trimestre;
                });
                foreach ($agrupacion as $periodo => $items) {
                    $label = str_replace(['-T1', '-T2', '-T3', '-T4'], [' Q1', ' Q2', ' Q3', ' Q4'], $periodo);
                    $cantidad = $items->count();
                    $porcentaje = $totalDiagnosticos > 0 ? round(($cantidad / $totalDiagnosticos) * 100, 2) : 0;
                    $datosGrafica[] = [
                        'label' => $label,
                        'cantidad' => $cantidad,
                        'porcentaje' => $porcentaje,
                        'periodo' => $periodo
                    ];
                }
                break;

            case 'año':
                $tituloGrafica = 'Distribución por Año';
                $agrupacion = $diagnosticos->groupBy(function($diagnostico) {
                    return \Carbon\Carbon::parse($diagnostico->fecha)->format('Y');
                });
                foreach ($agrupacion as $periodo => $items) {
                    $label = $periodo;
                    $cantidad = $items->count();
                    $porcentaje = $totalDiagnosticos > 0 ? round(($cantidad / $totalDiagnosticos) * 100, 2) : 0;
                    $datosGrafica[] = [
                        'label' => $label,
                        'cantidad' => $cantidad,
                        'porcentaje' => $porcentaje,
                        'periodo' => $periodo
                    ];
                }
                break;
        }

        // Ordenar por período
        usort($datosGrafica, function($a, $b) {
            return strcmp($a['periodo'], $b['periodo']);
        });

        // Estadísticas generales
        $estadisticas = [
            'total' => $totalDiagnosticos,
        ];

        // Obtener información de médico y paciente si se filtró
        $medicoFiltrado = null;
        if ($medicoId) {
            $medico = Medico::with('usuario')->find($medicoId);
            if ($medico) {
                $medicoFiltrado = $medico->usuario->nombre . ' ' . $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno;
            }
        }

        $pacienteFiltrado = null;
        if ($pacienteId) {
            $paciente = Paciente::with('usuario')->find($pacienteId);
            if ($paciente) {
                $pacienteFiltrado = $paciente->usuario->nombre . ' ' . $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno;
            }
        }

        // Generar gráfica de barras como imagen
        $colores = ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'];
        $imagenGrafica = $this->generarGraficaBarrasImagen($datosGrafica, $colores, $totalDiagnosticos);

        // Datos para el PDF
        $datos = [
            'diagnosticos' => $diagnosticos,
            'datosGrafica' => $datosGrafica,
            'tituloGrafica' => $tituloGrafica,
            'estadisticas' => $estadisticas,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'tipoPeriodo' => $tipoPeriodo,
            'medicoFiltrado' => $medicoFiltrado,
            'pacienteFiltrado' => $pacienteFiltrado,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
            'colores' => $colores,
            'imagenGrafica' => $imagenGrafica,
            'logo' => $this->obtenerLogoBase64()
        ];

        // Generar PDF
        $pdf = PDF::loadView('admin.reportes.diagnosticos-pdf', $datos);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-html5-parser', true);
        $pdf->setOption('isPhpEnabled', true);
        
        $nombreArchivo = 'Reporte_Diagnosticos_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }

    /**
     * Reporte de Actividades - Vista con filtros
     */
    public function actividades()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden acceder a los reportes.');
        }

        $medicos = Medico::with('usuario')
                        ->get()
                        ->map(function($medico) {
                            return [
                                'id' => $medico->id_medico,
                                'nombre' => $medico->usuario->nombre . ' ' . $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno
                            ];
                        });

        $pacientes = Paciente::with('usuario')
                        ->get()
                        ->map(function($paciente) {
                            return [
                                'id' => $paciente->id_paciente,
                                'nombre' => $paciente->usuario->nombre . ' ' . $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno
                            ];
                        });

        return view('admin.reportes.actividades', compact('medicos', 'pacientes'));
    }

    /**
     * Generar PDF del Reporte de Efectividad de Actividades
     */
    public function actividadesPDF(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden generar reportes.');
        }

        // Obtener filtros
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $medicoId = $request->input('medico_id');
        $pacienteId = $request->input('paciente_id');

        // Construir query base
        $query = Actividad::with(['paciente.usuario', 'medico.usuario']);

        // Aplicar filtros
        if ($fechaDesde) {
            $query->whereDate('fecha_asignacion', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->whereDate('fecha_asignacion', '<=', $fechaHasta);
        }
        if ($medicoId) {
            $query->where('id_medico', $medicoId);
        }
        if ($pacienteId) {
            $query->where('id_paciente', $pacienteId);
        }

        $actividades = $query->orderBy('fecha_asignacion', 'desc')->get();
        $totalActividades = $actividades->count();

        // Calcular efectividad
        $completadas = $actividades->where('completada', true)->count();
        $pendientes = $actividades->where('completada', false)->count();
        $vencidas = $actividades->filter(function($actividad) {
            return !$actividad->completada && $actividad->fecha_limite && 
                   \Carbon\Carbon::parse($actividad->fecha_limite)->isPast();
        })->count();

        $tasaCompletitud = $totalActividades > 0 ? round(($completadas / $totalActividades) * 100, 2) : 0;
        $tasaCumplimiento = $totalActividades > 0 ? round((($completadas) / ($completadas + $vencidas)) * 100, 2) : 0;

        // Estadísticas por actividad
        $actividadesPorNombre = $actividades->groupBy('nombre')->map(function ($items) {
            $total = $items->count();
            $completadas = $items->where('completada', true)->count();
            return [
                'nombre' => $items->first()->nombre,
                'total' => $total,
                'completadas' => $completadas,
                'pendientes' => $total - $completadas,
                'tasa_completitud' => $total > 0 ? round(($completadas / $total) * 100, 2) : 0
            ];
        })->values()->sortByDesc('total')->take(10);

        // Estadísticas por paciente
        $actividadesPorPaciente = $actividades->groupBy('id_paciente')->map(function ($items) {
            $paciente = $items->first()->paciente;
            $total = $items->count();
            $completadas = $items->where('completada', true)->count();
            return [
                'nombre' => $paciente ? ($paciente->usuario->nombre . ' ' . $paciente->usuario->apPaterno) : 'Sin paciente',
                'total' => $total,
                'completadas' => $completadas,
                'tasa_completitud' => $total > 0 ? round(($completadas / $total) * 100, 2) : 0
            ];
        })->values()->sortByDesc('tasa_completitud')->take(10);

        // Estadísticas generales
        $estadisticas = [
            'total' => $totalActividades,
            'completadas' => $completadas,
            'pendientes' => $pendientes,
            'vencidas' => $vencidas,
            'tasa_completitud' => $tasaCompletitud,
            'tasa_cumplimiento' => $tasaCumplimiento
        ];

        // Obtener información de médico y paciente si se filtró
        $medicoFiltrado = null;
        if ($medicoId) {
            $medico = Medico::with('usuario')->find($medicoId);
            if ($medico) {
                $medicoFiltrado = $medico->usuario->nombre . ' ' . $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno;
            }
        }

        $pacienteFiltrado = null;
        if ($pacienteId) {
            $paciente = Paciente::with('usuario')->find($pacienteId);
            if ($paciente) {
                $pacienteFiltrado = $paciente->usuario->nombre . ' ' . $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno;
            }
        }

        // Datos para el PDF
        $datos = [
            'actividades' => $actividades,
            'estadisticas' => $estadisticas,
            'actividadesPorNombre' => $actividadesPorNombre,
            'actividadesPorPaciente' => $actividadesPorPaciente,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'medicoFiltrado' => $medicoFiltrado,
            'pacienteFiltrado' => $pacienteFiltrado,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
            'logo' => $this->obtenerLogoBase64()
        ];

        // Generar PDF
        $pdf = PDF::loadView('admin.reportes.actividades-pdf', $datos);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-html5-parser', true);
        $pdf->setOption('isPhpEnabled', true);
        
        $nombreArchivo = 'Reporte_Actividades_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }

    /**
     * Reporte de Análisis Clínicos - Vista con filtros
     */
    public function analisis()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden acceder a los reportes.');
        }

        $medicos = Medico::with('usuario')
                        ->get()
                        ->map(function($medico) {
                            return [
                                'id' => $medico->id_medico,
                                'nombre' => $medico->usuario->nombre . ' ' . $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno
                            ];
                        });

        $pacientes = Paciente::with('usuario')
                        ->get()
                        ->map(function($paciente) {
                            return [
                                'id' => $paciente->id_paciente,
                                'nombre' => $paciente->usuario->nombre . ' ' . $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno
                            ];
                        });

        return view('admin.reportes.analisis', compact('medicos', 'pacientes'));
    }

    /**
     * Generar PDF del Reporte de Análisis Clínicos Más Repetidos
     */
    public function analisisPDF(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden generar reportes.');
        }

        // Obtener filtros
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $medicoId = $request->input('medico_id');
        $pacienteId = $request->input('paciente_id');
        $limite = $request->input('limite', 10); // Top N análisis más repetidos

        // Construir query base
        $query = AnalisisClinico::with(['paciente.usuario', 'medico.usuario']);

        // Aplicar filtros
        if ($fechaDesde) {
            $query->whereDate('fecha', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->whereDate('fecha', '<=', $fechaHasta);
        }
        if ($medicoId) {
            $query->where('id_medico', $medicoId);
        }
        if ($pacienteId) {
            $query->where('id_paciente', $pacienteId);
        }

        $analisis = $query->orderBy('fecha', 'desc')->get();
        $totalAnalisis = $analisis->count();

        // Agrupar por tipo de análisis y contar repeticiones
        $analisisPorTipo = $analisis->groupBy('tipo_analisis')->map(function ($items, $tipo) {
            return [
                'tipo' => $tipo ?: 'Sin tipo',
                'cantidad' => $items->count(),
                'porcentaje' => 0, // Se calculará después
                'analisis' => $items->take(5) // Primeros 5 de cada tipo para mostrar detalles
            ];
        })->values()->sortByDesc('cantidad')->take($limite);

        // Calcular porcentajes
        $analisisPorTipo = $analisisPorTipo->map(function ($item) use ($totalAnalisis) {
            $item['porcentaje'] = $totalAnalisis > 0 ? round(($item['cantidad'] / $totalAnalisis) * 100, 2) : 0;
            return $item;
        });

        // Estadísticas generales
        $estadisticas = [
            'total' => $totalAnalisis,
            'tipos_unicos' => $analisis->groupBy('tipo_analisis')->count(),
        ];

        // Obtener información de médico y paciente si se filtró
        $medicoFiltrado = null;
        if ($medicoId) {
            $medico = Medico::with('usuario')->find($medicoId);
            if ($medico) {
                $medicoFiltrado = $medico->usuario->nombre . ' ' . $medico->usuario->apPaterno . ' ' . $medico->usuario->apMaterno;
            }
        }

        $pacienteFiltrado = null;
        if ($pacienteId) {
            $paciente = Paciente::with('usuario')->find($pacienteId);
            if ($paciente) {
                $pacienteFiltrado = $paciente->usuario->nombre . ' ' . $paciente->usuario->apPaterno . ' ' . $paciente->usuario->apMaterno;
            }
        }

        // Datos para el PDF
        $datos = [
            'analisisPorTipo' => $analisisPorTipo,
            'estadisticas' => $estadisticas,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'medicoFiltrado' => $medicoFiltrado,
            'pacienteFiltrado' => $pacienteFiltrado,
            'limite' => $limite,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
            'logo' => $this->obtenerLogoBase64()
        ];

        // Generar PDF
        $pdf = PDF::loadView('admin.reportes.analisis-pdf', $datos);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-html5-parser', true);
        $pdf->setOption('isPhpEnabled', true);
        
        $nombreArchivo = 'Reporte_Analisis_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }

    /**
     * Generar gráfica de pastel como imagen usando técnica compatible
     */
    private function generarGraficaPastelImagen($datosGrafica, $colores, $total)
    {
        // Si GD está disponible, usarlo
        if (function_exists('imagecreatetruecolor')) {
            return $this->generarGraficaPastelGD($datosGrafica, $colores, $total);
        }
        
        // Si no, generar HTML/CSS que funcione mejor
        return null; // Retornar null para usar fallback HTML
    }

    /**
     * Generar gráfica de pastel usando GD
     */
    private function generarGraficaPastelGD($datosGrafica, $colores, $total)
    {
        $width = 300;
        $height = 300;
        $centerX = $width / 2;
        $centerY = $height / 2;
        $radius = 110;
        $innerRadius = 60;

        $image = imagecreatetruecolor($width, $height);
        
        // Colores
        $blanco = imagecolorallocate($image, 255, 255, 255);
        $gris = imagecolorallocate($image, 229, 231, 235);
        $azul = imagecolorallocate($image, 59, 130, 246);
        $grisTexto = imagecolorallocate($image, 107, 114, 128);
        
        // Fondo blanco
        imagefill($image, 0, 0, $blanco);
        
        // Convertir colores hex a RGB
        $coloresRGB = [];
        foreach ($colores as $colorHex) {
            $r = hexdec(substr($colorHex, 1, 2));
            $g = hexdec(substr($colorHex, 3, 2));
            $b = hexdec(substr($colorHex, 5, 2));
            $coloresRGB[] = imagecolorallocate($image, $r, $g, $b);
        }
        
        // Dibujar segmentos
        $currentAngle = -90;
        foreach ($datosGrafica as $index => $dato) {
            $percentage = $dato['porcentaje'] / 100;
            $angle = $percentage * 360;
            $endAngle = $currentAngle + $angle;
            
            $color = $coloresRGB[$index % count($coloresRGB)];
            
            // Dibujar el segmento
            $this->dibujarSegmentoPastel($image, $centerX, $centerY, $radius, $innerRadius, $currentAngle, $endAngle, $color);
            
            $currentAngle = $endAngle;
        }
        
        // Dibujar círculo central
        imagefilledellipse($image, $centerX, $centerY, $innerRadius * 2, $innerRadius * 2, $blanco);
        imageellipse($image, $centerX, $centerY, $innerRadius * 2, $innerRadius * 2, $gris);
        
        // Texto del total
        $text = (string)$total;
        $fontSize = 5;
        $fontPath = storage_path('fonts/DejaVuSans-Bold.ttf');
        
        if (file_exists($fontPath)) {
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
            $textWidth = $bbox[4] - $bbox[0];
            $textHeight = $bbox[1] - $bbox[7];
            $x = $centerX - ($textWidth / 2);
            $y = $centerY - ($textHeight / 2) - 10;
            imagettftext($image, $fontSize, 0, $x, $y, $azul, $fontPath, $text);
            
            $text2 = "Total";
            $bbox2 = imagettfbbox(3, 0, $fontPath, $text2);
            $textWidth2 = $bbox2[4] - $bbox2[0];
            $x2 = $centerX - ($textWidth2 / 2);
            $y2 = $centerY + 15;
            imagettftext($image, 3, 0, $x2, $y2, $grisTexto, $fontPath, $text2);
        } else {
            // Fallback sin fuente TTF
            $textWidth = imagefontwidth($fontSize) * strlen($text);
            $textHeight = imagefontheight($fontSize);
            $x = $centerX - ($textWidth / 2);
            $y = $centerY - ($textHeight / 2);
            imagestring($image, $fontSize, $x, $y, $text, $azul);
        }
        
        // Guardar imagen temporalmente
        $tempFile = tempnam(sys_get_temp_dir(), 'grafica_pastel_') . '.png';
        imagepng($image, $tempFile);
        imagedestroy($image);
        
        // Leer imagen como base64
        $imageData = file_get_contents($tempFile);
        $base64 = base64_encode($imageData);
        unlink($tempFile);
        
        return 'data:image/png;base64,' . $base64;
    }

    /**
     * Dibujar un segmento del pastel
     */
    private function dibujarSegmentoPastel($image, $centerX, $centerY, $outerRadius, $innerRadius, $startAngle, $endAngle, $color)
    {
        $points = [];
        
        // Puntos del arco exterior
        for ($angle = $startAngle; $angle <= $endAngle; $angle += 0.5) {
            $rad = deg2rad($angle);
            $x = $centerX + $outerRadius * cos($rad);
            $y = $centerY + $outerRadius * sin($rad);
            $points[] = $x;
            $points[] = $y;
        }
        
        // Puntos del arco interior (en reversa)
        for ($angle = $endAngle; $angle >= $startAngle; $angle -= 0.5) {
            $rad = deg2rad($angle);
            $x = $centerX + $innerRadius * cos($rad);
            $y = $centerY + $innerRadius * sin($rad);
            $points[] = $x;
            $points[] = $y;
        }
        
        // Cerrar el polígono
        if (count($points) >= 6) {
            imagefilledpolygon($image, $points, count($points) / 2, $color);
            // Borde blanco
            imagepolygon($image, $points, count($points) / 2, imagecolorallocate($image, 255, 255, 255));
        }
    }

    /**
     * Generar gráfica de barras como imagen usando GD
     */
    private function generarGraficaBarrasImagen($datosGrafica, $colores, $total)
    {
        // Si GD está disponible, usarlo
        if (function_exists('imagecreatetruecolor')) {
            return $this->generarGraficaBarrasGD($datosGrafica, $colores, $total);
        }
        
        return null;
    }

    /**
     * Generar gráfica de barras usando GD
     */
    private function generarGraficaBarrasGD($datosGrafica, $colores, $total)
    {
        if (empty($datosGrafica)) {
            return null;
        }

        $width = 600;
        $height = 300;
        $marginTop = 40;
        $marginBottom = 60;
        $marginLeft = 80;
        $marginRight = 40;
        $chartWidth = $width - $marginLeft - $marginRight;
        $chartHeight = $height - $marginTop - $marginBottom;

        $image = imagecreatetruecolor($width, $height);
        
        // Colores
        $blanco = imagecolorallocate($image, 255, 255, 255);
        $gris = imagecolorallocate($image, 229, 231, 235);
        $grisOscuro = imagecolorallocate($image, 107, 114, 128);
        $negro = imagecolorallocate($image, 0, 0, 0);
        
        // Fondo blanco
        imagefill($image, 0, 0, $blanco);
        
        // Convertir colores hex a RGB
        $coloresRGB = [];
        foreach ($colores as $colorHex) {
            $r = hexdec(substr($colorHex, 1, 2));
            $g = hexdec(substr($colorHex, 3, 2));
            $b = hexdec(substr($colorHex, 5, 2));
            $coloresRGB[] = imagecolorallocate($image, $r, $g, $b);
        }
        
        // Encontrar valor máximo
        $maxValor = max(array_column($datosGrafica, 'cantidad'));
        if ($maxValor == 0) $maxValor = 1;
        
        // Dibujar barras
        $numBarras = count($datosGrafica);
        $anchoBarra = $chartWidth / max($numBarras, 1);
        $espacioBarra = $anchoBarra * 0.2;
        $anchoBarraReal = $anchoBarra - $espacioBarra;
        
        foreach ($datosGrafica as $index => $dato) {
            $alturaBarra = ($dato['cantidad'] / $maxValor) * $chartHeight;
            $x = $marginLeft + ($index * $anchoBarra) + ($espacioBarra / 2);
            $y = $marginTop + $chartHeight - $alturaBarra;
            
            $color = $coloresRGB[$index % count($coloresRGB)];
            
            // Dibujar barra
            imagefilledrectangle($image, $x, $y, $x + $anchoBarraReal, $marginTop + $chartHeight, $color);
            imagerectangle($image, $x, $y, $x + $anchoBarraReal, $marginTop + $chartHeight, $grisOscuro);
            
            // Texto del valor
            $texto = (string)$dato['cantidad'];
            $fontSize = 3;
            $textWidth = imagefontwidth($fontSize) * strlen($texto);
            $textX = $x + ($anchoBarraReal / 2) - ($textWidth / 2);
            $textY = $y - 15;
            if ($textY < $marginTop) {
                $textY = $y + 5;
                imagestring($image, $fontSize, $textX, $textY, $texto, $negro);
            } else {
                imagestring($image, $fontSize, $textX, $textY, $texto, $blanco);
            }
            
            // Etiqueta del período (rotada o truncada)
            $label = substr($dato['label'], 0, 8);
            $labelWidth = imagefontwidth(2) * strlen($label);
            $labelX = $x + ($anchoBarraReal / 2) - ($labelWidth / 2);
            $labelY = $height - $marginBottom + 20;
            imagestring($image, 2, $labelX, $labelY, $label, $grisOscuro);
        }
        
        // Eje Y (valores)
        $numMarcas = 5;
        for ($i = 0; $i <= $numMarcas; $i++) {
            $valor = ($maxValor / $numMarcas) * $i;
            $y = $marginTop + $chartHeight - (($valor / $maxValor) * $chartHeight);
            $texto = (string)round($valor);
            imagestring($image, 2, $marginLeft - (imagefontwidth(2) * strlen($texto)) - 5, $y - 7, $texto, $grisOscuro);
            imageline($image, $marginLeft - 5, $y, $marginLeft, $y, $gris);
        }
        
        // Guardar imagen temporalmente
        $tempFile = tempnam(sys_get_temp_dir(), 'grafica_barras_') . '.png';
        imagepng($image, $tempFile);
        imagedestroy($image);
        
        // Leer imagen como base64
        $imageData = file_get_contents($tempFile);
        $base64 = base64_encode($imageData);
        unlink($tempFile);
        
        return 'data:image/png;base64,' . $base64;
    }

    /**
     * Obtener el logo en base64 para incluir en los PDFs
     */
    private function obtenerLogoBase64()
    {
        $logoPath = public_path('images/logo.png');
        
        if (file_exists($logoPath)) {
            $imageData = file_get_contents($logoPath);
            $base64 = base64_encode($imageData);
            return 'data:image/png;base64,' . $base64;
        }
        
        return null;
    }
}

