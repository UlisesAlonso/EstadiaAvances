@extends('layouts.app')

@section('title', 'Reporte de Historial Clínico')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Reporte de Historial Clínico</h2>
                            <p class="text-gray-600">Historial clínico completo del paciente</p>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="window.print()" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Imprimir
                            </button>
                            <a href="{{ route('historial-clinico.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Volver
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Información del Paciente -->
                <div class="mb-8 bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Información del Paciente</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nombre completo</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $paciente->usuario->nombre }}
                                @if($paciente->usuario->apPaterno) {{ $paciente->usuario->apPaterno }} @endif
                                @if($paciente->usuario->apMaterno) {{ $paciente->usuario->apMaterno }} @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Correo electrónico</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $paciente->usuario->correo }}</p>
                        </div>
                        @if($paciente->fecha_nacimiento)
                            <div>
                                <p class="text-sm text-gray-600">Fecha de nacimiento</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $paciente->fecha_nacimiento->format('d/m/Y') }}
                                    ({{ $paciente->fecha_nacimiento->age }} años)
                                </p>
                            </div>
                        @endif
                        @if($paciente->sexo)
                            <div>
                                <p class="text-sm text-gray-600">Sexo</p>
                                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($paciente->sexo) }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Resumen -->
                <div class="mb-6 bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Resumen del Historial</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Total de eventos</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $historiales->count() }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Eventos activos</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ $historiales->where('estado', 'activo')->count() }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Eventos cerrados</p>
                            <p class="text-2xl font-bold text-gray-600">
                                {{ $historiales->where('estado', 'cerrado')->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Historial Completo -->
                @if($historiales->count() > 0)
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Historial Clínico Completo</h3>
                        
                        @foreach($historiales as $historial)
                            <div class="border border-gray-200 rounded-lg p-6 mb-4">
                                <!-- Encabezado del Evento -->
                                <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-200">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">
                                            Evento del {{ $historial->fecha_evento ? $historial->fecha_evento->format('d/m/Y') : $historial->fecha_registro->format('d/m/Y') }}
                                        </h4>
                                        <p class="text-sm text-gray-600">
                                            Registrado el {{ $historial->fecha_registro->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    <div>
                                        @if($historial->estado == 'activo')
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Cerrado
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Médico Tratante -->
                                @if($historial->medico)
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-gray-700 mb-1">Médico Tratante</p>
                                        <p class="text-gray-900">
                                            {{ $historial->medico->usuario->nombre }}
                                            @if($historial->medico->usuario->apPaterno) {{ $historial->medico->usuario->apPaterno }} @endif
                                            @if($historial->medico->usuario->apMaterno) {{ $historial->medico->usuario->apMaterno }} @endif
                                            @if($historial->medico->especialidad)
                                                - {{ $historial->medico->especialidad }}
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                <!-- Diagnóstico -->
                                @if($historial->diagnostico)
                                    <div class="mb-4 bg-blue-50 p-4 rounded-lg">
                                        <p class="text-sm font-medium text-gray-700 mb-2">Diagnóstico</p>
                                        @if($historial->diagnostico->catalogoDiagnostico)
                                            <p class="text-sm text-gray-600 mb-1">
                                                <span class="font-medium">Código:</span> {{ $historial->diagnostico->catalogoDiagnostico->codigo }}
                                            </p>
                                            <p class="text-gray-900 font-semibold">
                                                {{ $historial->diagnostico->catalogoDiagnostico->descripcion_clinica }}
                                            </p>
                                        @else
                                            <p class="text-gray-900 font-semibold">{{ $historial->diagnostico->descripcion }}</p>
                                        @endif
                                    </div>
                                @endif

                                <!-- Tratamiento -->
                                @if($historial->tratamiento)
                                    <div class="mb-4 bg-green-50 p-4 rounded-lg">
                                        <p class="text-sm font-medium text-gray-700 mb-2">Tratamiento</p>
                                        <p class="text-gray-900 font-semibold mb-2">{{ $historial->tratamiento->nombre }}</p>
                                        <div class="grid grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <span class="font-medium text-gray-700">Dosis:</span>
                                                <span class="text-gray-900">{{ $historial->tratamiento->dosis }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-700">Frecuencia:</span>
                                                <span class="text-gray-900">{{ $historial->tratamiento->frecuencia }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-700">Duración:</span>
                                                <span class="text-gray-900">{{ $historial->tratamiento->duracion }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Observaciones -->
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Observaciones Médicas</p>
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $historial->observaciones }}</p>
                                </div>

                                <!-- Resultados de Análisis -->
                                @if($historial->resultados_analisis)
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-gray-700 mb-2">Resultados de Análisis o Estudios Médicos</p>
                                        <p class="text-gray-900 whitespace-pre-wrap">{{ $historial->resultados_analisis }}</p>
                                    </div>
                                @endif

                                <!-- Archivos Adjuntos -->
                                @if($historial->archivos_adjuntos && count($historial->archivos_adjuntos) > 0)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-sm font-medium text-gray-700 mb-2">Archivos Adjuntos</p>
                                        <ul class="list-disc list-inside text-sm text-gray-600">
                                            @foreach($historial->archivos_adjuntos as $archivo)
                                                <li>{{ basename($archivo) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500">No hay registros de historial clínico para este paciente.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection


