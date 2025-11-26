@extends('layouts.app')

@section('title', 'Reporte de Diagnósticos')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Reporte de Diagnósticos por Período</h2>
                    <p class="text-gray-600 mt-2">Genera reportes de diagnósticos con gráfica de barras mostrando distribución por período</p>
                </div>
                <a href="{{ route('admin.reportes.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a Reportes
                </a>
            </div>
        </div>

        <!-- Formulario de Filtros -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtros del Reporte</h3>
                
                <form method="POST" action="{{ route('admin.reportes.diagnosticos.pdf') }}" id="formReporte">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Fecha Desde -->
                        <div>
                            <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha Desde
                            </label>
                            <input type="date" 
                                   name="fecha_desde" 
                                   id="fecha_desde"
                                   value="{{ old('fecha_desde', request('fecha_desde')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                        </div>

                        <!-- Fecha Hasta -->
                        <div>
                            <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha Hasta
                            </label>
                            <input type="date" 
                                   name="fecha_hasta" 
                                   id="fecha_hasta"
                                   value="{{ old('fecha_hasta', request('fecha_hasta')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                        </div>

                        <!-- Tipo de Período -->
                        <div>
                            <label for="tipo_periodo" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Período
                            </label>
                            <select name="tipo_periodo" 
                                    id="tipo_periodo"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                                <option value="mes" {{ old('tipo_periodo', 'mes') == 'mes' ? 'selected' : '' }}>Por Mes</option>
                                <option value="trimestre" {{ old('tipo_periodo') == 'trimestre' ? 'selected' : '' }}>Por Trimestre</option>
                                <option value="año" {{ old('tipo_periodo') == 'año' ? 'selected' : '' }}>Por Año</option>
                            </select>
                        </div>

                        <!-- Médico (opcional) -->
                        <div>
                            <label for="medico_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Médico
                            </label>
                            <select name="medico_id" 
                                    id="medico_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                                <option value="">Todos los médicos</option>
                                @foreach($medicos as $medico)
                                    <option value="{{ $medico['id'] }}" {{ old('medico_id') == $medico['id'] ? 'selected' : '' }}>
                                        {{ $medico['nombre'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Paciente (opcional) -->
                        <div>
                            <label for="paciente_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Paciente
                            </label>
                            <select name="paciente_id" 
                                    id="paciente_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                                <option value="">Todos los pacientes</option>
                                @foreach($pacientes as $paciente)
                                    <option value="{{ $paciente['id'] }}" {{ old('paciente_id') == $paciente['id'] ? 'selected' : '' }}>
                                        {{ $paciente['nombre'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" 
                                onclick="document.getElementById('formReporte').reset();"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Limpiar Filtros
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 inline-flex items-center">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Generar PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Información -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Información del Reporte</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>Este reporte genera un PDF profesional con:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Gráfica de barras mostrando la distribución por período seleccionado</li>
                            <li>Estadísticas generales de diagnósticos</li>
                            <li>Tabla detallada con todos los diagnósticos filtrados</li>
                            <li>Filtros aplicados y fecha de generación</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
