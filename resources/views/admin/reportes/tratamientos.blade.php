@extends('layouts.app')

@section('title', 'Reporte de Tratamientos')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Reporte de Tratamientos Médicos</h2>
                    <p class="text-gray-600 mt-2">Genera un reporte detallado de tratamientos con filtros avanzados y gráficas.</p>
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
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Filtros para el Reporte de Tratamientos</h3>
                <form action="{{ route('admin.reportes.tratamientos.pdf') }}" method="POST" target="_blank" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="fecha_desde" class="block text-sm font-medium text-gray-700">Fecha de Inicio Desde:</label>
                            <input type="date" name="fecha_desde" id="fecha_desde"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="fecha_hasta" class="block text-sm font-medium text-gray-700">Fecha de Inicio Hasta:</label>
                            <input type="date" name="fecha_hasta" id="fecha_hasta"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado del Tratamiento:</label>
                            <select name="estado" id="estado"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                <option value="todos">Todos los Estados</option>
                                <option value="activo">Solo Activos</option>
                                <option value="inactivo">Solo Inactivos</option>
                            </select>
                        </div>
                        <div>
                            <label for="tipo_distribucion" class="block text-sm font-medium text-gray-700">Distribuir por:</label>
                            <select name="tipo_distribucion" id="tipo_distribucion"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                <option value="estado">Estado del Tratamiento</option>
                                <option value="medico">Médico</option>
                                <option value="paciente">Paciente</option>
                                <option value="frecuencia">Frecuencia de Aplicación</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="medico_id" class="block text-sm font-medium text-gray-700">Filtrar por Médico:</label>
                            <select name="medico_id" id="medico_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                <option value="">Todos los Médicos</option>
                                @foreach($medicos as $medico)
                                    <option value="{{ $medico['id'] }}">{{ $medico['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="paciente_id" class="block text-sm font-medium text-gray-700">Filtrar por Paciente:</label>
                            <select name="paciente_id" id="paciente_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                <option value="">Todos los Pacientes</option>
                                @foreach($pacientes as $paciente)
                                    <option value="{{ $paciente['id'] }}">{{ $paciente['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="nombre_tratamiento" class="block text-sm font-medium text-gray-700">Nombre del Tratamiento:</label>
                            <input type="text" name="nombre_tratamiento" id="nombre_tratamiento" list="tratamientos-list"
                                   placeholder="Buscar por nombre..."
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                            <datalist id="tratamientos-list">
                                @foreach($nombresTratamientos as $nombre)
                                    <option value="{{ $nombre }}">
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" id="limpiarFiltros"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Limpiar Filtros
                        </button>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Generar PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Información sobre el Reporte de Tratamientos</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Este reporte incluye:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Estadísticas generales (total, activos, inactivos)</li>
                            <li>Gráfica de pastel con distribución personalizable</li>
                            <li>Gráfica de barras horizontales</li>
                            <li>Tabla detallada con todos los tratamientos filtrados</li>
                            <li>Información completa de cada tratamiento (paciente, médico, dosis, frecuencia, duración)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const limpiarFiltrosBtn = document.getElementById('limpiarFiltros');
        const fechaDesde = document.getElementById('fecha_desde');
        const fechaHasta = document.getElementById('fecha_hasta');
        const estado = document.getElementById('estado');
        const tipoDistribucion = document.getElementById('tipo_distribucion');
        const medicoId = document.getElementById('medico_id');
        const pacienteId = document.getElementById('paciente_id');
        const nombreTratamiento = document.getElementById('nombre_tratamiento');

        limpiarFiltrosBtn.addEventListener('click', function() {
            fechaDesde.value = '';
            fechaHasta.value = '';
            estado.value = 'todos';
            tipoDistribucion.value = 'estado';
            medicoId.value = '';
            pacienteId.value = '';
            nombreTratamiento.value = '';
        });
    });
</script>
@endsection
