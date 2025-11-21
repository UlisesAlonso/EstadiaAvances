@extends('layouts.app')

@section('title', 'Detalles del Análisis Clínico')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Detalles del Análisis Clínico</h2>
                        <p class="text-gray-600">Información completa de tu análisis clínico</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('paciente.analisis.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver a Mis Análisis
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

                <!-- Información del análisis -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Información básica -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Análisis</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tipo o Nombre del Estudio</label>
                                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $analisis->tipo_estudio }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fecha del Análisis</label>
                                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $analisis->fecha_analisis->format('d/m/Y') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Descripción Detallada</label>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $analisis->descripcion }}</p>
                                </div>

                                @if($analisis->valores_obtenidos)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Valores Obtenidos</label>
                                    <div class="mt-1 text-sm text-gray-900 whitespace-pre-wrap bg-blue-50 p-3 rounded border border-blue-200">
                                        {{ $analisis->valores_obtenidos }}
                                    </div>
                                </div>
                                @endif

                                @if($analisis->observaciones_clinicas)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Observaciones Clínicas</label>
                                    <div class="mt-1 text-sm text-gray-900 whitespace-pre-wrap bg-yellow-50 p-3 rounded border border-yellow-200">
                                        {{ $analisis->observaciones_clinicas }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Información del médico -->
                        <div class="bg-green-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Médico Responsable</h3>
                            
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="text-lg font-medium text-green-800">
                                            {{ substr($analisis->medico->usuario->nombre, 0, 1) }}{{ substr($analisis->medico->usuario->apPaterno, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Dr. {{ $analisis->medico->usuario->nombre }} {{ $analisis->medico->usuario->apPaterno }} {{ $analisis->medico->usuario->apMaterno }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $analisis->medico->especialidad }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del sistema -->
                    <div class="space-y-6">
                        <div class="bg-purple-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Sistema</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">ID del Análisis</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $analisis->id_analisis }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fecha de Creación</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($analisis->fecha_creacion)
                                            {{ $analisis->fecha_creacion->format('d/m/Y H:i') }}
                                        @else
                                            No disponible
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

