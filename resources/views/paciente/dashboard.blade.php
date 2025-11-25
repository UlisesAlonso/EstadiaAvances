@extends('layouts.app')

@section('title', 'Panel de Salud')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Panel de Salud</h1>
        <p class="text-gray-600">Bienvenido, {{ Auth::user()->nombre }} - Monitorea tu salud en tiempo real</p>
        
        @if($paciente->id_paciente <= 0)
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">¡Bienvenido a tu primera visita!</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Tu historial médico se creará automáticamente cuando tengas tu primera cita con un médico.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Tarjetas de Estadísticas Principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Total de Citas</p>
                    <p class="text-3xl font-bold">{{ $stats['total_citas'] }}</p>
                    <p class="text-blue-100 text-xs mt-2">{{ $stats['citas_completadas'] }} completadas</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Diagnósticos</p>
                    <p class="text-3xl font-bold">{{ $stats['total_diagnosticos'] }}</p>
                    <p class="text-green-100 text-xs mt-2">Registros médicos</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Tratamientos</p>
                    <p class="text-3xl font-bold">{{ $stats['tratamientos_activos'] }}</p>
                    <p class="text-purple-100 text-xs mt-2">{{ $stats['tratamientos_completados'] }} completados</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm font-medium mb-1">Análisis</p>
                    <p class="text-3xl font-bold">{{ $stats['total_analisis'] }}</p>
                    <p class="text-indigo-100 text-xs mt-2">Estudios realizados</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @if($paciente->id_paciente > 0)
    <!-- Gráficas Principales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Gráfica 1: Evolución de Citas -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Evolución de Citas (12 meses)</h3>
                <div class="flex space-x-2 text-xs">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-1"></div>
                        <span class="text-gray-600">Completadas</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-1"></div>
                        <span class="text-gray-600">Pendientes</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-1"></div>
                        <span class="text-gray-600">Canceladas</span>
                    </div>
                </div>
            </div>
            <canvas id="chartCitas" height="100"></canvas>
        </div>

        <!-- Gráfica 2: Diagnósticos por Mes -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900">Diagnósticos Registrados</h3>
                <p class="text-sm text-gray-600">Evolución mensual de diagnósticos</p>
            </div>
            <canvas id="chartDiagnosticos" height="100"></canvas>
        </div>
    </div>

    <!-- Segunda Fila de Gráficas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Gráfica 3: Tratamientos Activos vs Completados -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900">Estado de Tratamientos</h3>
                <p class="text-sm text-gray-600">Distribución de tratamientos</p>
            </div>
            <canvas id="chartTratamientos" height="100"></canvas>
        </div>

        <!-- Gráfica 4: Historial Clínico por Mes -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900">Historial Clínico</h3>
                <p class="text-sm text-gray-600">Registros médicos por mes</p>
            </div>
            <canvas id="chartHistorial" height="100"></canvas>
        </div>
    </div>

    <!-- Tercera Fila de Gráficas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Gráfica 5: Análisis por Tipo -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900">Análisis por Tipo</h3>
                <p class="text-sm text-gray-600">Distribución de estudios realizados</p>
            </div>
            <canvas id="chartAnalisisTipo" height="100"></canvas>
        </div>

        <!-- Gráfica 6: Análisis por Mes -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900">Análisis Realizados</h3>
                <p class="text-sm text-gray-600">Evolución mensual de análisis</p>
            </div>
            <canvas id="chartAnalisisMes" height="100"></canvas>
        </div>
    </div>

    <!-- Gráfica 7: Tratamientos por Tipo (Horizontal) -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-900">Tratamientos por Tipo</h3>
            <p class="text-sm text-gray-600">Distribución de tratamientos prescritos</p>
        </div>
        <canvas id="chartTratamientosTipo" height="80"></canvas>
    </div>
    @else
    <!-- Mensaje cuando no hay datos -->
    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
        <svg class="mx-auto h-16 w-16 text-blue-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <h3 class="text-xl font-bold text-gray-900 mb-2">No hay datos para mostrar</h3>
        <p class="text-gray-600 mb-6">Agenda tu primera cita para comenzar a ver tus estadísticas de salud</p>
    </div>
    @endif

    <!-- Información Rápida -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <!-- Próximas Citas -->
        <div class="bg-white rounded-xl shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Próximas Citas</h3>
            </div>
            <div class="p-6">
                @if($citasProximas->count() > 0)
                    <div class="space-y-4">
                        @foreach($citasProximas as $cita)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $cita->medico->usuario->nombre }} {{ $cita->medico->usuario->apPaterno }}</p>
                                    <p class="text-sm text-gray-600">{{ $cita->fecha->format('d/m/Y H:i') }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $cita->medico->especialidad }}</p>
                                </div>
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                    @if($cita->estado === 'confirmada') bg-green-100 text-green-800
                                    @elseif($cita->estado === 'pendiente') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($cita->estado) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No tienes citas próximas programadas</p>
                @endif
            </div>
        </div>

        <!-- Tratamientos Activos -->
        <div class="bg-white rounded-xl shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Tratamientos Activos</h3>
            </div>
            <div class="p-6">
                @if($tratamientosActivos->count() > 0)
                    <div class="space-y-4">
                        @foreach($tratamientosActivos as $tratamiento)
                            <div class="p-4 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg border-l-4 border-purple-500">
                                <p class="font-semibold text-gray-900 mb-1">{{ $tratamiento->nombre }}</p>
                                <p class="text-sm text-gray-600 mb-2">{{ $tratamiento->descripcion }}</p>
                                <div class="flex items-center text-xs text-gray-500">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Inicio: {{ $tratamiento->fecha_inicio->format('d/m/Y') }}</span>
                                    @if($tratamiento->fecha_fin)
                                        <span class="ml-4">Fin: {{ $tratamiento->fecha_fin->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No tienes tratamientos activos</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@if($paciente->id_paciente > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const datosGraficas = @json($datosGraficas);
    const charts = {};

    // Configuración común
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6B7280';

    // 1. Gráfica de Citas (Barras agrupadas)
    const ctxCitas = document.getElementById('chartCitas');
    if (ctxCitas) {
        charts.citas = new Chart(ctxCitas, {
            type: 'bar',
            data: {
                labels: datosGraficas.citas_por_mes.labels,
                datasets: [
                    {
                        label: 'Completadas',
                        data: datosGraficas.citas_por_mes.completadas,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Pendientes',
                        data: datosGraficas.citas_por_mes.pendientes,
                        backgroundColor: 'rgba(234, 179, 8, 0.7)',
                        borderColor: 'rgba(234, 179, 8, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Canceladas',
                        data: datosGraficas.citas_por_mes.canceladas,
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // 2. Gráfica de Diagnósticos (Línea)
    const ctxDiagnosticos = document.getElementById('chartDiagnosticos');
    if (ctxDiagnosticos) {
        charts.diagnosticos = new Chart(ctxDiagnosticos, {
            type: 'line',
            data: {
                labels: datosGraficas.diagnosticos_por_mes.labels,
                datasets: [{
                    label: 'Diagnósticos',
                    data: datosGraficas.diagnosticos_por_mes.data,
                    borderColor: 'rgba(34, 197, 94, 1)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // 3. Gráfica de Tratamientos (Dona)
    const ctxTratamientos = document.getElementById('chartTratamientos');
    if (ctxTratamientos) {
        charts.tratamientos = new Chart(ctxTratamientos, {
            type: 'doughnut',
            data: {
                labels: ['Activos', 'Completados'],
                datasets: [{
                    data: [
                        datosGraficas.tratamientos_activos,
                        datosGraficas.tratamientos_completados
                    ],
                    backgroundColor: [
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
                    ],
                    borderColor: [
                        'rgba(168, 85, 247, 1)',
                        'rgba(139, 92, 246, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // 4. Gráfica de Historial Clínico (Área)
    const ctxHistorial = document.getElementById('chartHistorial');
    if (ctxHistorial) {
        charts.historial = new Chart(ctxHistorial, {
            type: 'line',
            data: {
                labels: datosGraficas.historial_por_mes.labels,
                datasets: [{
                    label: 'Registros',
                    data: datosGraficas.historial_por_mes.data,
                    borderColor: 'rgba(99, 102, 241, 1)',
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // 5. Gráfica de Análisis por Tipo (Barras horizontales)
    const ctxAnalisisTipo = document.getElementById('chartAnalisisTipo');
    if (ctxAnalisisTipo && datosGraficas.analisis_por_tipo.length > 0) {
        const tiposAnalisis = datosGraficas.analisis_por_tipo.map(a => a.tipo_estudio || 'Sin tipo');
        const totalesAnalisis = datosGraficas.analisis_por_tipo.map(a => a.total);
        
        charts.analisisTipo = new Chart(ctxAnalisisTipo, {
            type: 'bar',
            data: {
                labels: tiposAnalisis,
                datasets: [{
                    label: 'Análisis',
                    data: totalesAnalisis,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // 6. Gráfica de Análisis por Mes (Barras)
    const ctxAnalisisMes = document.getElementById('chartAnalisisMes');
    if (ctxAnalisisMes) {
        charts.analisisMes = new Chart(ctxAnalisisMes, {
            type: 'bar',
            data: {
                labels: datosGraficas.analisis_por_mes.labels,
                datasets: [{
                    label: 'Análisis',
                    data: datosGraficas.analisis_por_mes.data,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // 7. Gráfica de Tratamientos por Tipo (Barras horizontales)
    const ctxTratamientosTipo = document.getElementById('chartTratamientosTipo');
    if (ctxTratamientosTipo && datosGraficas.tratamientos_por_tipo.length > 0) {
        const nombresTratamientos = datosGraficas.tratamientos_por_tipo.map(t => t.nombre);
        const totalesTratamientos = datosGraficas.tratamientos_por_tipo.map(t => t.total);
        
        charts.tratamientosTipo = new Chart(ctxTratamientosTipo, {
            type: 'bar',
            data: {
                labels: nombresTratamientos,
                datasets: [{
                    label: 'Tratamientos',
                    data: totalesTratamientos,
                    backgroundColor: 'rgba(168, 85, 247, 0.7)',
                    borderColor: 'rgba(168, 85, 247, 1)',
                    borderWidth: 2,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>
@endif
@endsection
