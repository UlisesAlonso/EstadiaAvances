@extends('layouts.app')

@section('title', 'Responder Cuestionario')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Cuestionario Clínico</h2>
                        <p class="text-gray-600">{{ $respuesta ? 'Tu respuesta registrada' : 'Responde el cuestionario asignado' }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('paciente.preguntas.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>

                <!-- Información de la pregunta -->
                <div class="mb-6 bg-gray-50 rounded-lg p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pregunta</label>
                            <p class="text-lg text-gray-900">{{ $pregunta->descripcion }}</p>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $pregunta->tipo === 'abierta' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $pregunta->tipo === 'abierta' ? 'Pregunta Abierta' : 'Opción Múltiple' }}
                                </span>
                            </div>
                            
                            @if($pregunta->especialidad_medica)
                                <div class="text-sm text-gray-600">
                                    <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    {{ $pregunta->especialidad_medica }}
                                </div>
                            @endif
                            
                            <div class="text-sm text-gray-600">
                                <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Asignada: {{ $pregunta->fecha_asignacion->format('d/m/Y') }}
                            </div>
                        </div>

                        @if($pregunta->medico)
                            <div class="text-sm text-gray-600">
                                <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Asignada por: Dr. {{ $pregunta->medico->usuario->nombre }} {{ $pregunta->medico->usuario->apPaterno }}
                            </div>
                        @endif
                    </div>
                </div>

                @if($respuesta)
                    <!-- Respuesta existente -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tu Respuesta</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Respuesta</label>
                                <div class="bg-white border border-gray-200 rounded-md p-4">
                                    <p class="text-gray-900">{{ $respuesta->respuesta }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha y Hora de Respuesta</label>
                                    <p class="text-gray-900">{{ $respuesta->fecha_respuesta->format('d/m/Y H:i') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cumplimiento</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $respuesta->cumplimiento ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $respuesta->cumplimiento ? 'Cumplido' : 'No Cumplido' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Formulario de respuesta -->
                    <form method="POST" action="{{ route('paciente.preguntas.responder', $pregunta->id_pregunta) }}" class="space-y-6">
                        @csrf
                        
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Tu Respuesta</h3>
                            
                            @if($pregunta->esOpcionMultiple() && $pregunta->opciones_multiple)
                                <div>
                                    <label for="respuesta" class="block text-sm font-medium text-gray-700 mb-2">
                                        Selecciona una opción *
                                    </label>
                                    <div class="space-y-2">
                                        @foreach($pregunta->opciones_multiple as $opcion)
                                            <label class="flex items-center p-3 border border-gray-300 rounded-md hover:bg-gray-50 cursor-pointer">
                                                <input type="radio" 
                                                       name="respuesta" 
                                                       value="{{ $opcion }}"
                                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                                       required>
                                                <span class="ml-3 text-sm text-gray-900">{{ $opcion }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('respuesta')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @else
                                <div>
                                    <label for="respuesta" class="block text-sm font-medium text-gray-700 mb-2">
                                        Escribe tu respuesta *
                                    </label>
                                    <textarea name="respuesta" 
                                              id="respuesta"
                                              rows="6"
                                              placeholder="Escribe tu respuesta aquí..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('respuesta') border-red-500 @enderror"
                                              required>{{ old('respuesta') }}</textarea>
                                    @error('respuesta')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <div class="mt-6">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="cumplimiento" 
                                           value="1"
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">
                                        Marcar como cumplido
                                    </span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500">Marca esta opción si has cumplido con lo solicitado en la pregunta.</p>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('paciente.preguntas.index') }}" 
                               class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Enviar Respuesta
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

