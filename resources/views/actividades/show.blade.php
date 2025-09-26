@extends('layouts.app')

@section('title', 'Detalles de Actividad')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Detalles de la Actividad</h2>
                        <p class="text-gray-600">Información completa de la actividad clínica</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('medico.actividades.edit', $actividad->id_actividad) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar Actividad
                        </a>
                        <a href="{{ route('medico.actividades.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>

                <!-- Información de la actividad -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Información básica -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información Básica</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nombre de la Actividad</label>
                                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $actividad->nombre }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $actividad->descripcion }}</p>
                                </div>

                                @if($actividad->instrucciones)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Instrucciones Específicas</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $actividad->instrucciones }}</p>
                                </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $actividad->completada ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $actividad->completada ? 'Completada' : 'Pendiente' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Información del paciente -->
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Paciente Asignado</h3>
                            
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-lg font-medium text-blue-800">
                                            {{ substr($actividad->paciente->usuario->nombre, 0, 1) }}{{ substr($actividad->paciente->usuario->apPaterno, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $actividad->paciente->usuario->nombre }} {{ $actividad->paciente->usuario->apPaterno }} {{ $actividad->paciente->usuario->apMaterno }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $actividad->paciente->usuario->correo }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información temporal y médica -->
                    <div class="space-y-6">
                        <!-- Fechas -->
                        <div class="bg-green-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Fechas</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fecha de Asignación</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $actividad->fecha_asignacion->format('d/m/Y') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fecha Límite</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $actividad->fecha_limite->format('d/m/Y') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Días Restantes</label>
                                    @php
                                        $diasRestantes = now()->diffInDays($actividad->fecha_limite, false);
                                    @endphp
                                    <p class="mt-1 text-sm font-semibold {{ $diasRestantes < 0 ? 'text-red-600' : ($diasRestantes <= 3 ? 'text-yellow-600' : 'text-green-600') }}">
                                        @if($diasRestantes < 0)
                                            Vencida hace {{ abs($diasRestantes) }} días
                                        @elseif($diasRestantes == 0)
                                            Vence hoy
                                        @else
                                            {{ $diasRestantes }} días restantes
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Información médica -->
                        <div class="bg-purple-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información Médica</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Periodicidad</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $actividad->periodicidad }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Médico Asignador</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        Dr. {{ $actividad->medico->usuario->nombre }} {{ $actividad->medico->usuario->apPaterno }} {{ $actividad->medico->usuario->apMaterno }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Comentarios del paciente -->
                        @if($actividad->comentarios_paciente)
                        <div class="bg-yellow-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Comentarios del Paciente</h3>
                            <p class="text-sm text-gray-900">{{ $actividad->comentarios_paciente }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Acciones -->
                <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">
                    <div class="flex space-x-3">
                        @if(!$actividad->completada)
                            <form method="POST" action="{{ route('medico.actividades.toggle-completada', $actividad->id_actividad) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                        onclick="return confirm('¿Marcar esta actividad como completada?')">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Marcar como Completada
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('medico.actividades.toggle-completada', $actividad->id_actividad) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                                        onclick="return confirm('¿Marcar esta actividad como pendiente?')">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Marcar como Pendiente
                                </button>
                            </form>
                        @endif
                        
                        <!-- Botón de eliminar -->
                        <form method="POST" action="{{ route('medico.actividades.destroy', $actividad->id_actividad) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    onclick="return confirm('¿Estás seguro de eliminar esta actividad? Esta acción no se puede deshacer.')">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Eliminar Actividad
                            </button>
                        </form>
                    </div>
                    
                    <div class="text-sm text-gray-500">
                        @if($actividad->created_at)
                            Creada el {{ $actividad->created_at->format('d/m/Y H:i') }}
                        @endif
                        @if($actividad->updated_at && $actividad->updated_at != $actividad->created_at)
                            <br>Última modificación: {{ $actividad->updated_at->format('d/m/Y H:i') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
