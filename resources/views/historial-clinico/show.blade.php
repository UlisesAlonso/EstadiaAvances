@extends('layouts.app')

@section('title', 'Detalles del Evento Clínico')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Detalles del Evento Clínico</h2>
                            <p class="text-gray-600">Información completa del registro clínico</p>
                        </div>
                        <div class="flex space-x-2">
                            @if((auth()->user()->isMedico() || auth()->user()->isAdmin()) && $historial->estado == 'activo')
                                <a href="{{ route('historial-clinico.edit', $historial->id_historial) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </a>
                            @endif
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

                <!-- Información del Evento -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Fecha del Evento -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-1">Fecha del Evento</h3>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $historial->fecha_evento ? $historial->fecha_evento->format('d/m/Y') : $historial->fecha_registro->format('d/m/Y') }}
                        </p>
                    </div>

                    <!-- Fecha de Registro -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-1">Fecha de Registro</h3>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $historial->fecha_registro->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <!-- Estado -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-1">Estado</h3>
                        <p class="text-lg font-semibold">
                            @if($historial->estado == 'activo')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Cerrado
                                </span>
                            @endif
                        </p>
                    </div>

                    <!-- Paciente -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-1">Paciente</h3>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $historial->paciente->usuario->nombre }}
                            @if($historial->paciente->usuario->apPaterno) {{ $historial->paciente->usuario->apPaterno }} @endif
                            @if($historial->paciente->usuario->apMaterno) {{ $historial->paciente->usuario->apMaterno }} @endif
                        </p>
                        <p class="text-sm text-gray-600">{{ $historial->paciente->usuario->correo }}</p>
                    </div>
                </div>

                <!-- Médico Tratante -->
                @if($historial->medico)
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Médico Tratante</h3>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $historial->medico->usuario->nombre }}
                            @if($historial->medico->usuario->apPaterno) {{ $historial->medico->usuario->apPaterno }} @endif
                            @if($historial->medico->usuario->apMaterno) {{ $historial->medico->usuario->apMaterno }} @endif
                        </p>
                        @if($historial->medico->especialidad)
                            <p class="text-sm text-gray-600">Especialidad: {{ $historial->medico->especialidad }}</p>
                        @endif
                    </div>
                @endif

                <!-- Diagnóstico -->
                @if($historial->diagnostico)
                    <div class="mb-6 bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Diagnóstico</h3>
                        @if($historial->diagnostico->catalogoDiagnostico)
                            <p class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">Código:</span> {{ $historial->diagnostico->catalogoDiagnostico->codigo }}
                            </p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $historial->diagnostico->catalogoDiagnostico->descripcion_clinica }}
                            </p>
                            @if($historial->diagnostico->catalogoDiagnostico->categoria_medica)
                                <p class="text-sm text-gray-600 mt-1">
                                    <span class="font-medium">Categoría:</span> {{ $historial->diagnostico->catalogoDiagnostico->categoria_medica }}
                                </p>
                            @endif
                        @else
                            <p class="text-lg font-semibold text-gray-900">{{ $historial->diagnostico->descripcion }}</p>
                        @endif
                        <p class="text-sm text-gray-600 mt-2">
                            Fecha del diagnóstico: {{ $historial->diagnostico->fecha->format('d/m/Y') }}
                        </p>
                    </div>
                @endif

                <!-- Tratamiento -->
                @if($historial->tratamiento)
                    <div class="mb-6 bg-green-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Tratamiento</h3>
                        <p class="text-lg font-semibold text-gray-900 mb-2">{{ $historial->tratamiento->nombre }}</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
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
                        @if($historial->tratamiento->observaciones)
                            <p class="text-sm text-gray-700 mt-2">
                                <span class="font-medium">Observaciones del tratamiento:</span>
                                {{ $historial->tratamiento->observaciones }}
                            </p>
                        @endif
                        <p class="text-sm text-gray-600 mt-2">
                            Fecha de inicio: {{ $historial->tratamiento->fecha_inicio->format('d/m/Y') }}
                        </p>
                    </div>
                @endif

                <!-- Análisis Relacionado -->
                @if($historial->analisis)
                    <div class="mb-6 bg-indigo-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Análisis o Estudio Médico Relacionado</h3>
                        <p class="text-lg font-semibold text-gray-900 mb-2">{{ $historial->analisis->tipo_estudio }}</p>
                        @if($historial->analisis->descripcion)
                            <p class="text-sm text-gray-700 mb-2">
                                <span class="font-medium">Descripción:</span>
                                {{ $historial->analisis->descripcion }}
                            </p>
                        @endif
                        @if($historial->analisis->valores_obtenidos)
                            <p class="text-sm text-gray-700 mb-2">
                                <span class="font-medium">Valores Obtenidos:</span>
                                {{ $historial->analisis->valores_obtenidos }}
                            </p>
                        @endif
                        @if($historial->analisis->observaciones_clinicas)
                            <p class="text-sm text-gray-700 mb-2">
                                <span class="font-medium">Observaciones Clínicas:</span>
                                {{ $historial->analisis->observaciones_clinicas }}
                            </p>
                        @endif
                        <p class="text-sm text-gray-600">
                            Fecha del análisis: {{ $historial->analisis->fecha_analisis ? $historial->analisis->fecha_analisis->format('d/m/Y') : 'No especificada' }}
                        </p>
                    </div>
                @endif

                <!-- Observaciones Médicas -->
                <div class="mb-6 bg-yellow-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Observaciones Médicas</h3>
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $historial->observaciones }}</p>
                </div>

                <!-- Resultados de Análisis -->
                @if($historial->resultados_analisis)
                    <div class="mb-6 bg-purple-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Resultados de Análisis o Estudios Médicos</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $historial->resultados_analisis }}</p>
                    </div>
                @endif

                <!-- Antecedentes Médicos (solo si existen) -->
                @if($historial->alergias || $historial->enfermedades_familiares || $historial->cirugias_previas || 
                    $historial->consumo_tabaco || $historial->consumo_alcohol || $historial->realiza_ejercicio || 
                    $historial->tipo_alimentacion || $historial->observaciones_antecedentes)
                    <div class="mb-6 bg-orange-50 p-4 rounded-lg border-l-4 border-orange-400">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="h-5 w-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Antecedentes Médicos
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($historial->alergias)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">Alergias</h4>
                                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $historial->alergias }}</p>
                                </div>
                            @endif

                            @if($historial->enfermedades_familiares)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">Enfermedades Familiares Crónicas</h4>
                                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $historial->enfermedades_familiares }}</p>
                                </div>
                            @endif

                            @if($historial->cirugias_previas)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">Cirugías Previas</h4>
                                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $historial->cirugias_previas }}</p>
                                </div>
                            @endif

                            @if($historial->consumo_tabaco)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">Consumo de Tabaco</h4>
                                    <p class="text-sm text-gray-900">
                                        @if($historial->consumo_tabaco == 'si')
                                            Sí
                                        @elseif($historial->consumo_tabaco == 'no')
                                            No
                                        @else
                                            Ex fumador
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if($historial->consumo_alcohol)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">Consumo de Alcohol</h4>
                                    <p class="text-sm text-gray-900">
                                        @if($historial->consumo_alcohol == 'si')
                                            Sí
                                        @elseif($historial->consumo_alcohol == 'no')
                                            No
                                        @else
                                            Ocasional
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if($historial->realiza_ejercicio)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">Realiza Ejercicio</h4>
                                    <p class="text-sm text-gray-900">
                                        @if($historial->realiza_ejercicio == 'si')
                                            Sí
                                        @elseif($historial->realiza_ejercicio == 'no')
                                            No
                                        @else
                                            Ocasional
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if($historial->tipo_alimentacion)
                                <div class="md:col-span-2">
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">Tipo de Alimentación</h4>
                                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $historial->tipo_alimentacion }}</p>
                                </div>
                            @endif

                            @if($historial->observaciones_antecedentes)
                                <div class="md:col-span-2">
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">Observaciones Adicionales</h4>
                                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $historial->observaciones_antecedentes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Archivos Adjuntos -->
                @if($historial->archivos_adjuntos && count($historial->archivos_adjuntos) > 0)
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-4">Archivos Adjuntos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($historial->archivos_adjuntos as $archivo)
                                <div class="flex items-center justify-between p-3 bg-white rounded-md border border-gray-200">
                                    <div class="flex items-center">
                                        <svg class="h-6 w-6 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ basename($archivo) }}</span>
                                    </div>
                                    <a href="{{ asset('storage/' . $archivo) }}" 
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-800">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

