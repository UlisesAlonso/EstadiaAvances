@extends('layouts.app')

@section('title', 'Mis Cuestionarios')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Mis Cuestionarios</h2>
                        <p class="text-gray-600">Preguntas y cuestionarios asignados por tu médico</p>
                    </div>
                    <a href="{{ route('paciente.dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver al Dashboard
                    </a>
                </div>

                <!-- Filtros -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <form method="GET" action="{{ route('paciente.preguntas.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                <select name="tipo" id="tipo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">Todos</option>
                                    <option value="abierta" {{ request('tipo') == 'abierta' ? 'selected' : '' }}>Abierta</option>
                                    <option value="opcion_multiple" {{ request('tipo') == 'opcion_multiple' ? 'selected' : '' }}>Opción Múltiple</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-1">Fecha Desde</label>
                                <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            
                            <div>
                                <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-1">Fecha Hasta</label>
                                <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            
                            <div class="flex items-end">
                                <button type="submit" class="w-full px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Filtrar
                                </button>
                            </div>
                        </div>
                        <a href="{{ route('paciente.preguntas.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            Limpiar Filtros
                        </a>
                    </form>
                </div>

                <!-- Lista de preguntas -->
                <div class="space-y-4">
                    @forelse($preguntas as $pregunta)
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ Str::limit($pregunta->texto, 100) }}</h3>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $pregunta->tipo == 'abierta' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $pregunta->tipo == 'abierta' ? 'Abierta' : 'Opción Múltiple' }}
                                        </span>
                                    </div>
                                    @if($pregunta->descripcion)
                                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($pregunta->descripcion, 150) }}</p>
                                    @endif
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span>Categoría: {{ $pregunta->categoria }}</span>
                                        <span>•</span>
                                        <span>Asignada: {{ $pregunta->fecha_asignacion ? $pregunta->fecha_asignacion->format('d/m/Y') : 'N/A' }}</span>
                                        <span>•</span>
                                        <span>Respuestas: {{ $pregunta->total_respuestas }}/3
                                            @if($pregunta->total_respuestas >= 3)
                                                <span class="ml-1 px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                                    Cerrada
                                                </span>
                                            @endif
                                        </span>
                                        @if($pregunta->medico)
                                        <span>•</span>
                                        <span>Médico: Dr. {{ $pregunta->medico->usuario->nombre }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('paciente.preguntas.show', $pregunta->id_pregunta) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Ver y Responder
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-500 mt-2">No tienes cuestionarios asignados</p>
                        </div>
                    @endforelse
                </div>

                @if($preguntas->hasPages())
                    <div class="mt-6">
                        {{ $preguntas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

