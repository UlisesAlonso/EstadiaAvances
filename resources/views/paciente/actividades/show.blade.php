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
                        <p class="text-gray-600">Información completa de tu actividad clínica</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('paciente.actividades.index') }}" 
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
                                    @php
                                        $diasRestantes = now()->diffInDays($actividad->fecha_limite, false);
                                        $esVencida = !$actividad->completada && $diasRestantes < 0;
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $actividad->completada ? 'bg-green-100 text-green-800' : ($esVencida ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $actividad->completada ? 'Completada' : ($esVencida ? 'Vencida' : 'Pendiente') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Información del médico -->
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Médico Asignador</h3>
                            
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-lg font-medium text-blue-800">
                                            {{ substr($actividad->medico->usuario->nombre, 0, 1) }}{{ substr($actividad->medico->usuario->apPaterno, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Dr. {{ $actividad->medico->usuario->nombre }} {{ $actividad->medico->usuario->apPaterno }} {{ $actividad->medico->usuario->apMaterno }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $actividad->medico->usuario->correo }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información temporal y acciones -->
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

                        <!-- Información de periodicidad -->
                        <div class="bg-purple-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Periodicidad</h3>
                            <p class="text-sm text-gray-900">{{ $actividad->periodicidad }}</p>
                        </div>

                        <!-- Comentarios del paciente -->
                        @if($actividad->comentarios_paciente)
                        <div class="bg-yellow-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Tus Comentarios</h3>
                            <p class="text-sm text-gray-900">{{ $actividad->comentarios_paciente }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Acciones para el paciente -->
                @if(!$actividad->completada)
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones Disponibles</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Marcar como completada -->
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                            <h4 class="text-md font-medium text-gray-900 mb-2">Marcar como Completada</h4>
                            <p class="text-sm text-gray-600 mb-4">Indica que has completado esta actividad</p>
                            <form method="POST" action="{{ route('paciente.actividades.marcar-completada', $actividad->id_actividad) }}" class="inline">
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
                        </div>

                        <!-- Agregar comentario -->
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                            <h4 class="text-md font-medium text-gray-900 mb-2">Agregar Comentario</h4>
                            <p class="text-sm text-gray-600 mb-4">Comparte dudas, observaciones o comentarios con tu médico</p>
                            <form method="POST" action="{{ route('paciente.actividades.agregar-comentario', $actividad->id_actividad) }}" class="space-y-3">
                                @csrf
                                <textarea name="comentario" 
                                          rows="3"
                                          placeholder="Escribe tu comentario, duda o observación..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          required></textarea>
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    Enviar Comentario
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <div class="mt-8 bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-medium text-green-800">¡Actividad Completada!</h3>
                            <p class="text-sm text-green-700">Has completado exitosamente esta actividad. ¡Buen trabajo!</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Información adicional -->
                <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        @if($actividad->created_at)
                            Asignada el {{ $actividad->created_at->format('d/m/Y H:i') }}
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
