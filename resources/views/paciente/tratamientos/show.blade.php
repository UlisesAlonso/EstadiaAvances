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
                        <p class="text-gray-600">Información completa de tu tratamiento médico</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('paciente.tratamientos.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver a Mis Tratamientos
                        </a>
                        <a href="{{ route('paciente.dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Dashboard
                        </a>
                    </div>
                </div>

                <!-- Información del tratamiento -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Información básica -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Tratamiento</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nombre del Tratamiento</label>
                                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $tratamiento->nombre }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Dosis</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $tratamiento->dosis }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Frecuencia</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $tratamiento->frecuencia }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Duración</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $tratamiento->duracion }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $tratamiento->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $tratamiento->activo ? 'Activo' : 'Finalizado/Suspendido' }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($tratamiento->diagnostico)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Diagnóstico Relacionado</label>
                                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                    <p class="text-sm text-blue-900 font-medium">{{ $tratamiento->diagnostico->descripcion }}</p>
                                    <p class="text-xs text-blue-600 mt-1">Fecha del diagnóstico: {{ $tratamiento->diagnostico->fecha->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Información del médico -->
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Médico Responsable</h3>
                            
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-lg font-medium text-blue-800">
                                            {{ substr($tratamiento->medico->usuario->nombre, 0, 1) }}{{ substr($tratamiento->medico->usuario->apPaterno, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Dr. {{ $tratamiento->medico->usuario->nombre }} {{ $tratamiento->medico->usuario->apPaterno }} {{ $tratamiento->medico->usuario->apMaterno }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $tratamiento->medico->especialidad }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información temporal y diagnóstica -->
                    <div class="space-y-6">
                        <!-- Fechas -->
                        <div class="bg-green-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Fechas</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $tratamiento->fecha_inicio->format('d/m/Y') }}</p>
                                </div>

                                @if($tratamiento->created_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fecha de Prescripción</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $tratamiento->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                @endif

                                @if($tratamiento->updated_at && $tratamiento->updated_at != $tratamiento->created_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Última Modificación</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $tratamiento->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Diagnóstico relacionado -->
                        @if($tratamiento->diagnostico)
                        <div class="bg-purple-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Diagnóstico Relacionado</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Diagnóstico</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $tratamiento->diagnostico->nombre }}</p>
                                </div>

                                @if($tratamiento->diagnostico->descripcion)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $tratamiento->diagnostico->descripcion }}</p>
                                </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fecha del Diagnóstico</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $tratamiento->diagnostico->fecha_diagnostico->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Observaciones -->
                @if($tratamiento->observaciones)
                <div class="mt-8">
                    <div class="bg-yellow-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Observaciones del Médico</h3>
                        <p class="text-sm text-gray-900">{{ $tratamiento->observaciones }}</p>
                    </div>
                </div>
                @endif

                <!-- Información adicional -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        <p><strong>ID del Tratamiento:</strong> {{ $tratamiento->id_tratamiento }}</p>
                        @if($tratamiento->created_at)
                            <p><strong>Registrado el:</strong> {{ $tratamiento->created_at->format('d/m/Y H:i') }}</p>
                        @endif
                        @if($tratamiento->updated_at && $tratamiento->updated_at != $tratamiento->created_at)
                            <p><strong>Última actualización:</strong> {{ $tratamiento->updated_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
