@extends('layouts.app')

@section('title', 'Seguimiento del Paciente')

@section('content')
@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header con información del paciente -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">Seguimiento del Paciente</h1>
                        <div class="flex items-center space-x-4 mt-2">
                            <div>
                                <p class="text-blue-100 text-sm">Paciente</p>
                                <p class="text-xl font-semibold">{{ $paciente->usuario->nombre }} {{ $paciente->usuario->apPaterno }} {{ $paciente->usuario->apMaterno }}</p>
                            </div>
                            <div class="border-l border-blue-400 pl-4">
                                <p class="text-blue-100 text-sm">Correo</p>
                                <p class="text-lg">{{ $paciente->usuario->correo }}</p>
                            </div>
                            @if($paciente->fecha_nacimiento)
                            <div class="border-l border-blue-400 pl-4">
                                <p class="text-blue-100 text-sm">Edad</p>
                                <p class="text-lg">{{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age }} años</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        @auth
                            @if(auth()->user()->isMedico() || auth()->user()->isAdmin())
                                <a href="{{ route(auth()->user()->isAdmin() ? 'admin.seguimiento.reporte.pdf' : 'medico.seguimiento.reporte.pdf', $paciente->id_paciente) }}?{{ http_build_query(request()->all()) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-white text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    PDF
                                </a>
                                <a href="{{ route(auth()->user()->isAdmin() ? 'admin.seguimiento.reporte.excel' : 'medico.seguimiento.reporte.excel', $paciente->id_paciente) }}?{{ http_build_query(request()->all()) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-white text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Excel
                                </a>
                                <a href="{{ route(auth()->user()->isAdmin() ? 'admin.seguimiento.index' : 'medico.seguimiento.index') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-white text-sm font-medium rounded-md text-white bg-white bg-opacity-20 hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Volver a Lista
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Citas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500">Citas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['citas']['total'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $estadisticas['citas']['completadas'] }} completadas | 
                                {{ $estadisticas['citas']['pendientes'] }} pendientes
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tratamientos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500">Tratamientos</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['tratamientos']['total'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $estadisticas['tratamientos']['activos'] }} activos | 
                                {{ $estadisticas['tratamientos']['finalizados'] }} finalizados
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividades -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500">Actividades</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['actividades']['total'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $estadisticas['actividades']['completadas'] }} completadas | 
                                {{ $estadisticas['actividades']['pendientes'] }} pendientes
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Análisis -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500">Análisis</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['analisis']['total'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $estadisticas['analisis']['con_valores'] }} con valores | 
                                {{ $estadisticas['analisis']['este_mes'] }} este mes
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas de Evolución y Cumplimiento -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Gráfica de Barras: Cumplimiento de Citas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Cumplimiento de Citas por Mes</h3>
                    <div style="position: relative; height: 300px; max-height: 300px;">
                        <canvas id="chartCitas"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfica de Líneas: Evolución de Actividades -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Evolución de Actividades</h3>
                    <div style="position: relative; height: 300px; max-height: 300px;">
                        <canvas id="chartActividades"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfica Circular: Distribución de Eventos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución de Eventos</h3>
                    <div style="position: relative; height: 300px; max-height: 300px;">
                        <canvas id="chartDistribucion"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfica de Barras: Actividades por Tipo -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Cumplimiento de Actividades por Tipo</h3>
                    <div style="position: relative; height: 300px; max-height: 300px;">
                        <canvas id="chartActividadesTipo"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicadores de Cumplimiento -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Cumplimiento de Citas</h3>
                    <div class="flex items-center">
                        <div class="flex-1">
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>Citas completadas</span>
                                <span>{{ $estadisticas['cumplimiento_citas'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $estadisticas['cumplimiento_citas'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Cumplimiento de Actividades</h3>
                    <div class="flex items-center">
                        <div class="flex-1">
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>Actividades completadas</span>
                                <span>{{ $estadisticas['cumplimiento_actividades'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-yellow-600 h-4 rounded-full" style="width: {{ $estadisticas['cumplimiento_actividades'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas y Eventos Recientes -->
        @if(count($alertas) > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Alertas y Eventos Recientes</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                        {{ collect($alertas)->where('nivel', 'danger')->count() > 0 ? 'bg-red-100 text-red-800' : 
                           (collect($alertas)->where('nivel', 'warning')->count() > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ count($alertas) }} alerta(s)
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach($alertas as $alerta)
                    <div class="flex items-start p-4 rounded-lg border-l-4 
                        {{ $alerta['nivel'] == 'warning' ? 'bg-yellow-50 border-yellow-500' : 
                           ($alerta['nivel'] == 'danger' ? 'bg-red-50 border-red-500' : 'bg-blue-50 border-blue-500') }}">
                        <div class="flex-shrink-0">
                            @if($alerta['nivel'] == 'warning')
                                <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($alerta['nivel'] == 'danger')
                                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium 
                                    {{ $alerta['nivel'] == 'warning' ? 'text-yellow-800' : 
                                       ($alerta['nivel'] == 'danger' ? 'text-red-800' : 'text-blue-800') }}">
                                    {{ $alerta['mensaje'] }}
                                </p>
                                <span class="text-xs font-medium 
                                    {{ $alerta['nivel'] == 'warning' ? 'text-yellow-600' : 
                                       ($alerta['nivel'] == 'danger' ? 'text-red-600' : 'text-blue-600') }}">
                                    @if($alerta['tipo'] == 'cita_proxima' || $alerta['tipo'] == 'actividad_por_vencer')
                                        {{ \Carbon\Carbon::parse($alerta['fecha'])->diffForHumans() }}
                                    @endif
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($alerta['fecha'])->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Filtros -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Filtros</h2>
                <form method="GET" action="{{ route(auth()->user()->isPaciente() ? 'paciente.seguimiento.index' : (auth()->user()->isAdmin() ? 'admin.seguimiento.index' : 'medico.seguimiento.index'), $paciente->id_paciente ?? null) }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-1">Fecha Desde</label>
                            <input type="date" 
                                   name="fecha_desde" 
                                   id="fecha_desde"
                                   value="{{ $filtros['fecha_desde'] ?? '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-1">Fecha Hasta</label>
                            <input type="date" 
                                   name="fecha_hasta" 
                                   id="fecha_hasta"
                                   value="{{ $filtros['fecha_hasta'] ?? '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="tipo_informacion" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Información</label>
                            <select name="tipo_informacion" 
                                    id="tipo_informacion"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todos los tipos</option>
                                <option value="cita" {{ ($filtros['tipo_informacion'] ?? '') == 'cita' ? 'selected' : '' }}>Citas</option>
                                <option value="diagnostico" {{ ($filtros['tipo_informacion'] ?? '') == 'diagnostico' ? 'selected' : '' }}>Diagnósticos</option>
                                <option value="tratamiento" {{ ($filtros['tipo_informacion'] ?? '') == 'tratamiento' ? 'selected' : '' }}>Tratamientos</option>
                                <option value="actividad" {{ ($filtros['tipo_informacion'] ?? '') == 'actividad' ? 'selected' : '' }}>Actividades</option>
                                <option value="analisis" {{ ($filtros['tipo_informacion'] ?? '') == 'analisis' ? 'selected' : '' }}>Análisis</option>
                            </select>
                        </div>

                        <div>
                            <label for="id_diagnostico" class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico</label>
                            <select name="id_diagnostico" 
                                    id="id_diagnostico"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todos los diagnósticos</option>
                                @foreach($opcionesFiltros['diagnosticos'] as $diagnostico)
                                    <option value="{{ $diagnostico->id_diagnostico }}" {{ ($filtros['id_diagnostico'] ?? '') == $diagnostico->id_diagnostico ? 'selected' : '' }}>
                                        {{ $diagnostico->catalogoDiagnostico->descripcion_clinica ?? $diagnostico->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Filtrar
                        </button>
                        
                        @if(request()->hasAny(['fecha_desde', 'fecha_hasta', 'tipo_informacion', 'id_diagnostico', 'id_tratamiento']))
                            <a href="{{ route(auth()->user()->isPaciente() ? 'paciente.seguimiento.index' : (auth()->user()->isAdmin() ? 'admin.seguimiento.index' : 'medico.seguimiento.index'), $paciente->id_paciente ?? null) }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Limpiar Filtros
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Timeline de Eventos -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Línea de Tiempo - Historial Completo</h2>
                
                @if(count($datosConsolidados['timeline']) > 0)
                    <div class="relative">
                        <!-- Línea vertical -->
                        <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-300"></div>
                        
                        <div class="space-y-6">
                            @foreach($datosConsolidados['timeline'] as $evento)
                                <div class="relative flex items-start">
                                    <!-- Icono del evento -->
                                    <div class="flex-shrink-0 z-10">
                                        <div class="flex items-center justify-center w-16 h-16 rounded-full 
                                            {{ $evento['tipo'] == 'cita' ? 'bg-blue-100 border-2 border-blue-500' : 
                                               ($evento['tipo'] == 'diagnostico' ? 'bg-red-100 border-2 border-red-500' : 
                                               ($evento['tipo'] == 'tratamiento' ? 'bg-green-100 border-2 border-green-500' : 
                                               ($evento['tipo'] == 'actividad' ? 'bg-yellow-100 border-2 border-yellow-500' : 
                                               ($evento['tipo'] == 'analisis' ? 'bg-purple-100 border-2 border-purple-500' : 'bg-gray-100 border-2 border-gray-500')))) }}">
                                            @if($evento['tipo'] == 'cita')
                                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            @elseif($evento['tipo'] == 'diagnostico')
                                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            @elseif($evento['tipo'] == 'tratamiento')
                                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                                </svg>
                                            @elseif($evento['tipo'] == 'actividad')
                                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                </svg>
                                            @elseif($evento['tipo'] == 'analisis')
                                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Contenido del evento -->
                                    <div class="ml-6 flex-1 bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900">{{ $evento['titulo'] }}</h3>
                                                <p class="text-sm text-gray-600 mt-1">{{ $evento['descripcion'] }}</p>
                                                <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                                    <span class="flex items-center">
                                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ \Carbon\Carbon::parse($evento['fecha'])->format('d/m/Y') }}
                                                    </span>
                                                    @if(isset($evento['medico']))
                                                    <span class="flex items-center">
                                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        {{ $evento['medico'] }}
                                                    </span>
                                                    @endif
                                                    @if(isset($evento['estado']))
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        {{ $evento['estado'] == 'completada' || $evento['estado'] == 'activo' ? 'bg-green-100 text-green-800' : 
                                                           ($evento['estado'] == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                                                           ($evento['estado'] == 'cancelada' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                                        {{ ucfirst($evento['estado']) }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay eventos registrados</h3>
                        <p class="mt-1 text-sm text-gray-500">No se encontraron eventos en el rango de fechas seleccionado.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Observaciones Médicas -->
        @auth
            @if(auth()->user()->isMedico() || auth()->user()->isAdmin())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-gray-900">Observaciones Médicas</h2>
                            <a href="{{ route(auth()->user()->isAdmin() ? 'admin.seguimiento.observaciones.create' : 'medico.seguimiento.observaciones.create', $paciente->id_paciente) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Nueva Observación
                            </a>
                        </div>
                        @if(count($observaciones) > 0)
                        <div class="space-y-4">
                            @foreach($observaciones as $observacion)
                            <div class="border-l-4 border-indigo-500 bg-indigo-50 p-4 rounded-r-lg">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-600">{{ $observacion->observacion }}</p>
                                        <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                            <span>{{ \Carbon\Carbon::parse($observacion->fecha_observacion)->format('d/m/Y') }}</span>
                                            @if($observacion->tipo)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ ucfirst($observacion->tipo) }}
                                            </span>
                                            @endif
                                            @if($observacion->medico)
                                            <span>{{ $observacion->medico->usuario->nombre }} {{ $observacion->medico->usuario->apPaterno }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 ml-4">
                                        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.seguimiento.observaciones.edit' : 'medico.seguimiento.observaciones.edit', $observacion->id_observacion) }}" 
                                           class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route(auth()->user()->isAdmin() ? 'admin.seguimiento.observaciones.destroy' : 'medico.seguimiento.observaciones.destroy', $observacion->id_observacion) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900" 
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Estás seguro de eliminar esta observación? Esta acción no se puede deshacer.')">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No hay observaciones médicas registradas.</p>
                        </div>
                        @endif
                    </div>
                </div>
            @endif
        @endauth
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para las gráficas
    const datosGraficas = @json($datosGraficas ?? []);

    // Verificar que no se hayan creado gráficas previamente
    const charts = {};

    // Gráfica de Barras: Cumplimiento de Citas
    if (datosGraficas.citas_por_mes && datosGraficas.citas_por_mes.labels.length > 0) {
        const ctxCitas = document.getElementById('chartCitas');
        if (ctxCitas && !charts.citas) {
            charts.citas = new Chart(ctxCitas, {
                type: 'bar',
                data: {
                    labels: datosGraficas.citas_por_mes.labels,
                    datasets: [{
                        label: 'Total Citas',
                        data: datosGraficas.citas_por_mes.total,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Completadas',
                        data: datosGraficas.citas_por_mes.completadas,
                        backgroundColor: 'rgba(34, 197, 94, 0.5)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Canceladas',
                        data: datosGraficas.citas_por_mes.canceladas,
                        backgroundColor: 'rgba(239, 68, 68, 0.5)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1.5,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });
        }
    } else {
        const chartCitasContainer = document.getElementById('chartCitas')?.closest('.bg-white');
        if (chartCitasContainer) {
            chartCitasContainer.style.display = 'none';
        }
    }

    // Gráfica de Líneas: Evolución de Actividades
    if (datosGraficas.actividades_por_mes && datosGraficas.actividades_por_mes.labels.length > 0) {
        const ctxActividades = document.getElementById('chartActividades');
        if (ctxActividades && !charts.actividades) {
            charts.actividades = new Chart(ctxActividades, {
                type: 'line',
                data: {
                    labels: datosGraficas.actividades_por_mes.labels,
                    datasets: [{
                        label: 'Total Actividades',
                        data: datosGraficas.actividades_por_mes.total,
                        borderColor: 'rgba(234, 179, 8, 1)',
                        backgroundColor: 'rgba(234, 179, 8, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Completadas',
                        data: datosGraficas.actividades_por_mes.completadas,
                        borderColor: 'rgba(34, 197, 94, 1)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1.5,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }
    } else {
        const chartActividadesContainer = document.getElementById('chartActividades')?.closest('.bg-white');
        if (chartActividadesContainer) {
            chartActividadesContainer.style.display = 'none';
        }
    }

    // Gráfica Circular (Pie): Distribución de Eventos
    if (datosGraficas.distribucion_eventos && datosGraficas.distribucion_eventos.data.some(d => d > 0)) {
        const ctxDistribucion = document.getElementById('chartDistribucion');
        if (ctxDistribucion && !charts.distribucion) {
            charts.distribucion = new Chart(ctxDistribucion, {
                type: 'pie',
                data: {
                    labels: datosGraficas.distribucion_eventos.labels,
                    datasets: [{
                        data: datosGraficas.distribucion_eventos.data,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(234, 179, 8, 0.8)',
                            'rgba(168, 85, 247, 0.8)'
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(239, 68, 68, 1)',
                            'rgba(34, 197, 94, 1)',
                            'rgba(234, 179, 8, 1)',
                            'rgba(168, 85, 247, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1.5,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed + ' eventos';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    } else {
        const chartDistribucionContainer = document.getElementById('chartDistribucion')?.closest('.bg-white');
        if (chartDistribucionContainer) {
            chartDistribucionContainer.style.display = 'none';
        }
    }

    // Gráfica de Barras: Actividades por Tipo
    if (datosGraficas.actividades_por_tipo && datosGraficas.actividades_por_tipo.labels.length > 0) {
        const ctxActividadesTipo = document.getElementById('chartActividadesTipo');
        if (ctxActividadesTipo && !charts.actividadesTipo) {
            charts.actividadesTipo = new Chart(ctxActividadesTipo, {
                type: 'bar',
                data: {
                    labels: datosGraficas.actividades_por_tipo.labels,
                    datasets: [{
                        label: 'Total',
                        data: datosGraficas.actividades_por_tipo.total,
                        backgroundColor: 'rgba(234, 179, 8, 0.5)',
                        borderColor: 'rgba(234, 179, 8, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Completadas',
                        data: datosGraficas.actividades_por_tipo.completadas,
                        backgroundColor: 'rgba(34, 197, 94, 0.5)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1.5,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }
    } else {
        const chartActividadesTipoContainer = document.getElementById('chartActividadesTipo')?.closest('.bg-white');
        if (chartActividadesTipoContainer) {
            chartActividadesTipoContainer.style.display = 'none';
        }
    }
});
</script>
@endsection

