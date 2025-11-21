@extends('layouts.app')

@section('title', 'Reporte de Preguntas y Respuestas')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Reporte de Preguntas y Respuestas</h2>
                        <p class="text-gray-600">Reporte clínico de cuestionarios por paciente</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>

                <!-- Información del paciente -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Paciente</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                            <p class="mt-1 text-sm text-gray-900 font-semibold">
                                {{ $paciente->usuario->nombre }} {{ $paciente->usuario->apPaterno }} {{ $paciente->usuario->apMaterno }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $paciente->usuario->correo }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total de Preguntas</label>
                            <p class="mt-1 text-2xl font-bold text-blue-900">{{ $preguntas->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                @if($preguntas->count() > 0)
                    @php
                        $totalRespuestas = 0;
                        $respuestasCumplidas = 0;
                        foreach($preguntas as $pregunta) {
                            $respuestasPregunta = $pregunta->respuestas->where('id_usuario', $paciente->usuario->id_usuario);
                            $totalRespuestas += $respuestasPregunta->count();
                            $respuestasCumplidas += $respuestasPregunta->where('cumplimiento', true)->count();
                        }
                    @endphp
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-600">Total de Respuestas</p>
                                    <p class="text-2xl font-bold text-green-900">{{ $totalRespuestas }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-blue-600">Respuestas Cumplidas</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $respuestasCumplidas }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-purple-600">Porcentaje de Cumplimiento</p>
                                    <p class="text-2xl font-bold text-purple-900">
                                        {{ $totalRespuestas > 0 ? round(($respuestasCumplidas / $totalRespuestas) * 100, 1) : 0 }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Lista de preguntas y respuestas -->
                <div class="space-y-6">
                    @forelse($preguntas as $pregunta)
                        @php
                            $respuestaPaciente = $pregunta->respuestas->where('id_usuario', $paciente->usuario->id_usuario)->first();
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <span class="text-sm font-medium text-gray-500">ID: #{{ $pregunta->id_pregunta }}</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $pregunta->tipo === 'abierta' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $pregunta->tipo === 'abierta' ? 'Abierta' : 'Opción Múltiple' }}
                                        </span>
                                        @if($pregunta->especialidad_medica)
                                            <span class="text-sm text-gray-500">{{ $pregunta->especialidad_medica }}</span>
                                        @endif
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $pregunta->descripcion }}</h3>
                                    <p class="text-sm text-gray-500">
                                        Asignada: {{ $pregunta->fecha_asignacion->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>

                            @if($respuestaPaciente)
                                <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Respuesta del Paciente</h4>
                                    <p class="text-gray-900 mb-3">{{ $respuestaPaciente->respuesta }}</p>
                                    <div class="flex items-center space-x-4 text-sm">
                                        <span class="text-gray-600">
                                            Fecha: {{ $respuestaPaciente->fecha_respuesta->format('d/m/Y H:i') }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $respuestaPaciente->cumplimiento ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $respuestaPaciente->cumplimiento ? 'Cumplido' : 'No Cumplido' }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <p class="text-sm text-yellow-800">Sin respuesta registrada</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay preguntas</h3>
                            <p class="mt-1 text-sm text-gray-500">No se encontraron preguntas para este paciente.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

