@extends('layouts.app')

@section('title', 'Gestión de Preguntas y Cuestionarios')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Gestión de Preguntas y Cuestionarios</h2>
                        <p class="text-gray-600">Administra las preguntas clínicas dirigidas a los pacientes</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.create' : 'medico.preguntas.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nueva Pregunta
                        </a>
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'medico.dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver al Dashboard
                        </a>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <form method="GET" action="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            @if(Auth::user()->isMedico() || Auth::user()->isAdmin())
                            <div>
                                <label for="paciente" class="block text-sm font-medium text-gray-700 mb-1">Paciente</label>
                                <input type="text" 
                                       name="paciente" 
                                       id="paciente"
                                       value="{{ request('paciente') }}"
                                       placeholder="Nombre del paciente..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            @endif
                            
                            <div>
                                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                <select name="tipo" id="tipo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">Todos</option>
                                    <option value="abierta" {{ request('tipo') == 'abierta' ? 'selected' : '' }}>Abierta</option>
                                    <option value="opcion_multiple" {{ request('tipo') == 'opcion_multiple' ? 'selected' : '' }}>Opción Múltiple</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <select name="estado" id="estado" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">Todos</option>
                                    <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activa</option>
                                    <option value="inactiva" {{ request('estado') == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-1">Fecha Desde</label>
                                <input type="date" 
                                       name="fecha_desde" 
                                       id="fecha_desde"
                                       value="{{ request('fecha_desde') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-1">Fecha Hasta</label>
                                <input type="date" 
                                       name="fecha_hasta" 
                                       id="fecha_hasta"
                                       value="{{ request('fecha_hasta') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            
                            @if(Auth::user()->isMedico() || Auth::user()->isAdmin())
                            @if($diagnosticos && $diagnosticos->count() > 0)
                            <div>
                                <label for="id_diagnostico" class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico</label>
                                <select name="id_diagnostico" id="id_diagnostico" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">Todos</option>
                                    @foreach($diagnosticos as $diagnostico)
                                        <option value="{{ $diagnostico->id_diagnostico }}" {{ request('id_diagnostico') == $diagnostico->id_diagnostico ? 'selected' : '' }}>
                                            {{ $diagnostico->catalogoDiagnostico ? $diagnostico->catalogoDiagnostico->descripcion_clinica : $diagnostico->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            
                            @if($especialidades && $especialidades->count() > 0)
                            <div>
                                <label for="especialidad" class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                                <select name="especialidad" id="especialidad" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">Todas</option>
                                    @foreach($especialidades as $especialidad)
                                        <option value="{{ $especialidad }}" {{ request('especialidad') == $especialidad ? 'selected' : '' }}>
                                            {{ $especialidad }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            @endif
                            
                            <div class="flex items-end">
                                <button type="submit" 
                                        class="w-full px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Filtrar
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index') }}" 
                               class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Limpiar Filtros
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tabla de preguntas -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                @if(Auth::user()->isMedico() || Auth::user()->isAdmin())
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pregunta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Asignación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Respuestas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                @if(Auth::user()->isAdmin())
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médico</th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($preguntas as $pregunta)
                                <tr class="hover:bg-gray-50">
                                    @if(Auth::user()->isMedico() || Auth::user()->isAdmin())
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($pregunta->paciente)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-blue-800">
                                                        {{ substr($pregunta->paciente->usuario->nombre, 0, 1) }}{{ substr($pregunta->paciente->usuario->apPaterno ?? '', 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $pregunta->paciente->usuario->nombre }} {{ $pregunta->paciente->usuario->apPaterno }} {{ $pregunta->paciente->usuario->apMaterno }}
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <span class="text-sm text-gray-400">General</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($pregunta->texto, 80) }}</div>
                                        @if($pregunta->descripcion)
                                        <div class="text-sm text-gray-500 mt-1">{{ Str::limit($pregunta->descripcion, 60) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $pregunta->tipo == 'abierta' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $pregunta->tipo == 'abierta' ? 'Abierta' : 'Opción Múltiple' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $pregunta->categoria }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $pregunta->fecha_asignacion ? $pregunta->fecha_asignacion->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="font-medium">{{ $pregunta->total_respuestas }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $pregunta->estado == 'activa' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $pregunta->estado == 'activa' ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </td>
                                    @if(Auth::user()->isAdmin())
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($pregunta->medico)
                                        Dr. {{ $pregunta->medico->usuario->nombre }} {{ $pregunta->medico->usuario->apPaterno }}
                                        @else
                                        <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.show' : (Auth::user()->isMedico() ? 'medico.preguntas.show' : 'paciente.preguntas.show'), $pregunta->id_pregunta) }}" 
                                               class="text-blue-600 hover:text-blue-900"
                                               title="Ver detalles">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            @if(Auth::user()->isMedico() || Auth::user()->isAdmin())
                                            <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.edit' : 'medico.preguntas.edit', $pregunta->id_pregunta) }}" 
                                               class="text-yellow-600 hover:text-yellow-900"
                                               title="Editar pregunta">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.destroy' : 'medico.preguntas.destroy', $pregunta->id_pregunta) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('¿Estás seguro de eliminar esta pregunta?')"
                                                        title="Eliminar pregunta">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->isAdmin() ? '9' : (Auth::user()->isMedico() ? '8' : '6') }}" class="px-6 py-4 text-center text-gray-500">
                                        <div class="text-center py-8">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-gray-500 mt-2">No se encontraron preguntas</p>
                                            <p class="text-gray-400 text-sm mt-1">Las preguntas aparecerán aquí cuando se creen</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
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

