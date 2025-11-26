@extends('layouts.app')

@section('title', 'Reporte de Citas')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Reporte de Citas</h2>
                    <p class="text-gray-600 mt-2">Genera reportes de citas médicas con gráfica de pastel</p>
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
                
                <form method="POST" action="{{ route('admin.reportes.citas.pdf') }}" id="formReporte">
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
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
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
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Tipo de Distribución -->
                        <div>
                            <label for="tipo_distribucion" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Distribución
                            </label>
                            <select name="tipo_distribucion" 
                                    id="tipo_distribucion"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="estado" {{ old('tipo_distribucion', 'estado') == 'estado' ? 'selected' : '' }}>Por Estado</option>
                                <option value="medico" {{ old('tipo_distribucion') == 'medico' ? 'selected' : '' }}>Por Médico</option>
                                <option value="especialidad" {{ old('tipo_distribucion') == 'especialidad' ? 'selected' : '' }}>Por Especialidad</option>
                            </select>
                        </div>

                        <!-- Médico (opcional) -->
                        <div id="filtro_medico" style="display: none;">
                            <label for="id_medico" class="block text-sm font-medium text-gray-700 mb-2">
                                Médico
                            </label>
                            <select name="id_medico" 
                                    id="id_medico"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todos los médicos</option>
                                @foreach($medicos as $medico)
                                    <option value="{{ $medico['id'] }}" {{ old('id_medico') == $medico['id'] ? 'selected' : '' }}>
                                        {{ $medico['nombre'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Especialidad (opcional) -->
                        <div id="filtro_especialidad" style="display: none;">
                            <label for="especialidad" class="block text-sm font-medium text-gray-700 mb-2">
                                Especialidad
                            </label>
                            <select name="especialidad" 
                                    id="especialidad"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todas las especialidades</option>
                                @foreach($especialidades as $esp)
                                    <option value="{{ $esp }}" {{ old('especialidad') == $esp ? 'selected' : '' }}>
                                        {{ $esp }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" 
                                onclick="document.getElementById('formReporte').reset();"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Limpiar Filtros
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 inline-flex items-center">
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
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Información del Reporte</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Este reporte genera un PDF profesional con:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Gráfica de pastel mostrando la distribución seleccionada</li>
                            <li>Estadísticas generales de citas</li>
                            <li>Tabla detallada con todas las citas filtradas</li>
                            <li>Filtros aplicados y fecha de generación</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Mostrar/ocultar filtros según el tipo de distribución
    document.getElementById('tipo_distribucion').addEventListener('change', function() {
        const tipo = this.value;
        const filtroMedico = document.getElementById('filtro_medico');
        const filtroEspecialidad = document.getElementById('filtro_especialidad');
        
        if (tipo === 'medico') {
            filtroMedico.style.display = 'block';
            filtroEspecialidad.style.display = 'none';
        } else if (tipo === 'especialidad') {
            filtroMedico.style.display = 'none';
            filtroEspecialidad.style.display = 'block';
        } else {
            filtroMedico.style.display = 'none';
            filtroEspecialidad.style.display = 'none';
        }
    });

    // Ejecutar al cargar la página
    document.getElementById('tipo_distribucion').dispatchEvent(new Event('change'));
</script>
@endsection
