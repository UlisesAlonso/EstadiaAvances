@extends('layouts.app')

@section('title', 'Responder Cuestionario')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Responder Cuestionario</h2>
                        <p class="text-gray-600">Completa tu respuesta a la pregunta asignada</p>
                    </div>
                    <a href="{{ route('paciente.preguntas.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver
                    </a>
                </div>

                <!-- Información de la pregunta -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $pregunta->texto }}</h3>
                    @if($pregunta->descripcion)
                    <p class="text-sm text-gray-600 mb-4">{{ $pregunta->descripcion }}</p>
                    @endif
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span>Categoría: {{ $pregunta->categoria }}</span>
                        <span>•</span>
                        <span>Especialidad: {{ $pregunta->especialidad_medica }}</span>
                        @if($pregunta->medico)
                        <span>•</span>
                        <span>Médico: Dr. {{ $pregunta->medico->usuario->nombre }}</span>
                        @endif
                    </div>
                </div>

                <!-- Todas las respuestas (comentarios públicos) -->
                @if($todasLasRespuestas && $todasLasRespuestas->count() > 0)
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-3">
                        Respuestas ({{ $totalRespuestas }}/3)
                        @if($totalRespuestas >= 3)
                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                Cerrada
                            </span>
                        @endif
                    </h4>
                    <div class="space-y-3">
                        @foreach($todasLasRespuestas as $respuesta)
                            <div class="border-l-4 {{ $respuesta->id_paciente == Auth::user()->paciente->id_paciente ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-gray-50' }} pl-4 py-3 rounded">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 mb-1">
                                            {{ $respuesta->paciente && $respuesta->paciente->usuario ? $respuesta->paciente->usuario->nombre . ' ' . $respuesta->paciente->usuario->apPaterno : 'Anónimo' }}
                                            @if($respuesta->id_paciente == Auth::user()->paciente->id_paciente)
                                                <span class="ml-2 text-xs text-blue-600">(Tú)</span>
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-700">{{ $respuesta->respuesta }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <p class="text-xs text-gray-500">
                                        {{ $respuesta->fecha_hora ? $respuesta->fecha_hora->format('d/m/Y H:i') : $respuesta->fecha->format('d/m/Y') }}
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
                <div class="mb-6">
                    <p class="text-sm text-gray-500">Aún no hay respuestas. Sé el primero en responder.</p>
                </div>
                @endif

                <!-- Formulario de respuesta -->
                @if($puedeResponder)
                <form method="POST" action="{{ route('paciente.preguntas.responder', $pregunta->id_pregunta) }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="respuesta" class="block text-sm font-medium text-gray-700 mb-2">
                            Tu Respuesta *
                            @if($pregunta->tipo == 'opcion_multiple' && $pregunta->opciones)
                                <span class="text-xs text-gray-500">(Selecciona una opción)</span>
                            @endif
                        </label>
                        
                        @if($pregunta->tipo == 'opcion_multiple' && $pregunta->opciones)
                            <select name="respuesta" 
                                    id="respuesta"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('respuesta') border-red-500 @enderror">
                                <option value="">Selecciona una opción</option>
                                @foreach($pregunta->opciones as $opcion)
                                    <option value="{{ $opcion }}" {{ old('respuesta') == $opcion ? 'selected' : '' }}>
                                        {{ $opcion }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <textarea name="respuesta" 
                                      id="respuesta"
                                      rows="5"
                                      required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('respuesta') border-red-500 @enderror"
                                      placeholder="Escribe tu respuesta aquí...">{{ old('respuesta') }}</textarea>
                        @endif
                        
                        @error('respuesta')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="cumplimiento" 
                                   value="1"
                                   {{ old('cumplimiento') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Marcar como cumplido</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('paciente.preguntas.index') }}" 
                           class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            Guardar Respuesta
                        </button>
                    </div>
                </form>
                @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <p class="text-sm text-gray-800">
                            Esta pregunta ha alcanzado el límite de 3 respuestas. Ya no se pueden agregar más respuestas, pero puedes seguir viendo todas las respuestas publicadas.
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

