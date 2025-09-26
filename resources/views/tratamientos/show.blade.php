@extends('layouts.app')

@section('title', 'Detalles del Tratamiento')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Detalles del Tratamiento</h2>
                        <p class="text-gray-600">Información completa del tratamiento clínico</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('medico.tratamientos.edit', $tratamiento->id_tratamiento) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Modificar Tratamiento
                        </a>
                        <a href="{{ route('medico.tratamientos.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>

                <!-- Información del Tratamiento -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Información Principal -->
                    <div class="lg:col-span-2">
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Tratamiento</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Tratamiento</label>
                                    <p class="text-sm text-gray-900 font-medium">{{ $tratamiento->nombre }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $tratamiento->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $tratamiento->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosis/Concentración</label>
                                    <p class="text-sm text-gray-900">{{ $tratamiento->dosis }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Frecuencia</label>
                                    <p class="text-sm text-gray-900">{{ $tratamiento->frecuencia }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Duración</label>
                                    <p class="text-sm text-gray-900">{{ $tratamiento->duracion }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                                    <p class="text-sm text-gray-900">{{ $tratamiento->fecha_inicio->format('d/m/Y') }}</p>
                                </div>
                                
                                @if($tratamiento->diagnostico)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico Relacionado</label>
                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                        <p class="text-sm text-blue-900 font-medium">{{ $tratamiento->diagnostico->descripcion }}</p>
                                        <p class="text-xs text-blue-600 mt-1">Fecha del diagnóstico: {{ $tratamiento->diagnostico->fecha->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            @if($tratamiento->observaciones)
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones Clínicas</label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                                        <p class="text-sm text-gray-900 whitespace-pre-line">{{ $tratamiento->observaciones }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Información del Paciente y Médico -->
                    <div class="space-y-6">
                        <!-- Información del Paciente -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Paciente</h3>
                            
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-lg font-medium text-gray-700">
                                            {{ substr($tratamiento->paciente->usuario->nombre, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $tratamiento->paciente->usuario->nombre }} {{ $tratamiento->paciente->usuario->apPaterno }} {{ $tratamiento->paciente->usuario->apMaterno }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $tratamiento->paciente->usuario->correo }}</div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Fecha de Nacimiento</label>
                                    <p class="text-sm text-gray-900">{{ $tratamiento->paciente->fecha_nacimiento->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Sexo</label>
                                    <p class="text-sm text-gray-900 capitalize">{{ $tratamiento->paciente->sexo }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Médico -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Médico Responsable</h3>
                            
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-lg font-medium text-blue-700">
                                            {{ substr($tratamiento->medico->usuario->nombre, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        Dr. {{ $tratamiento->medico->usuario->nombre }} {{ $tratamiento->medico->usuario->apPaterno }} {{ $tratamiento->medico->usuario->apMaterno }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $tratamiento->medico->especialidad }}</div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Especialidad</label>
                                    <p class="text-sm text-gray-900">{{ $tratamiento->medico->especialidad }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Cédula Profesional</label>
                                    <p class="text-sm text-gray-900">{{ $tratamiento->medico->cedula_profesional }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnóstico Relacionado -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Diagnóstico Relacionado</h3>
                            
                            @if($tratamiento->diagnostico)
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Descripción</label>
                                        <p class="text-sm text-gray-900">{{ $tratamiento->diagnostico->descripcion }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Fecha del Diagnóstico</label>
                                        <p class="text-sm text-gray-900">{{ $tratamiento->diagnostico->fecha->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <svg class="h-8 w-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500">Sin diagnóstico relacionado</p>
                                    <p class="text-xs text-gray-400 mt-1">Este tratamiento no está asociado a ningún diagnóstico</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">
                    <div class="flex space-x-3">
                        @if($tratamiento->activo)
                            <!-- Botón para suspender -->
                            <form method="POST" action="{{ route('medico.tratamientos.toggle-status', $tratamiento->id_tratamiento) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                        onclick="return confirm('¿Estás seguro de suspender este tratamiento?')">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Suspender Tratamiento
                                </button>
                            </form>
                            <!-- Botón para finalizar -->
                            <form method="POST" action="{{ route('medico.tratamientos.finalizar', $tratamiento->id_tratamiento) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                        onclick="return confirm('¿Estás seguro de finalizar este tratamiento? Esta acción no se puede deshacer.')">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Finalizar Tratamiento
                                </button>
                            </form>
                        @else
                            <!-- Botón para reactivar -->
                            <form method="POST" action="{{ route('medico.tratamientos.toggle-status', $tratamiento->id_tratamiento) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                        onclick="return confirm('¿Estás seguro de reactivar este tratamiento?')">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1m-6-8h8a2 2 0 012 2v8a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2z"></path>
                                    </svg>
                                    Reactivar Tratamiento
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    <div class="text-sm text-gray-500">
                        @if($tratamiento->created_at)
                            Creado el {{ $tratamiento->created_at->format('d/m/Y H:i') }}
                        @else
                            Fecha de inicio: {{ $tratamiento->fecha_inicio->format('d/m/Y') }}
                        @endif
                        @if($tratamiento->updated_at && $tratamiento->updated_at != $tratamiento->created_at)
                            <br>Última modificación: {{ $tratamiento->updated_at->format('d/m/Y H:i') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
