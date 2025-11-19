@extends('layouts.app')

@section('title', 'Reporte de Preguntas y Respuestas')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Reporte de Preguntas y Respuestas</h2>
                        <p class="text-gray-600">Reporte clínico de cuestionarios por paciente</p>
                    </div>
                    <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver
                    </a>
                </div>

                <!-- Información del paciente -->
                <div class="bg-blue-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Paciente: {{ $paciente->usuario->nombre }} {{ $paciente->usuario->apPaterno }} {{ $paciente->usuario->apMaterno }}</h3>
                    <p class="text-sm text-gray-600">{{ $paciente->usuario->correo }}</p>
                </div>

                <!-- Lista de preguntas y respuestas -->
                <div class="space-y-6">
                    @forelse($preguntas as $pregunta)
                        <div class="border border-gray-200 rounded-lg p-6">
                            <div class="mb-4">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ $pregunta->texto }}</h4>
                                @if($pregunta->descripcion)
                                <p class="text-sm text-gray-600 mb-2">{{ $pregunta->descripcion }}</p>
                                @endif
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span>Tipo: {{ $pregunta->tipo == 'abierta' ? 'Abierta' : 'Opción Múltiple' }}</span>
                                    <span>•</span>
                                    <span>Categoría: {{ $pregunta->categoria }}</span>
                                    <span>•</span>
                                    <span>Asignada: {{ $pregunta->fecha_asignacion ? $pregunta->fecha_asignacion->format('d/m/Y') : 'N/A' }}</span>
                                    <span>•</span>
                                    <span>Estado: 
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $pregunta->estado == 'activa' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $pregunta->estado == 'activa' ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <!-- Respuestas -->
                            @if($pregunta->respuestas && $pregunta->respuestas->count() > 0)
                                <div class="mt-4">
                                    <h5 class="text-md font-semibold text-gray-900 mb-3">Respuestas ({{ $pregunta->respuestas->count() }})</h5>
                                    <div class="space-y-3">
                                        @foreach($pregunta->respuestas as $respuesta)
                                            <div class="border-l-4 border-blue-500 pl-4 py-2 bg-blue-50 rounded">
                                                <p class="text-sm text-gray-900">{{ $respuesta->respuesta }}</p>
                                                <div class="flex items-center justify-between mt-2">
                                                    <p class="text-xs text-gray-500">
                                                        Fecha: {{ $respuesta->fecha_hora ? $respuesta->fecha_hora->format('d/m/Y H:i') : $respuesta->fecha->format('d/m/Y') }}
                                                    </p>
                                                    @if($respuesta->cumplimiento)
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                        Cumplido
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="mt-4 text-sm text-gray-500">
                                    No hay respuestas registradas para esta pregunta.
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-500 mt-2">No se encontraron preguntas para este paciente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

