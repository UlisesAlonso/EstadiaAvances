@extends('layouts.app')

@section('title', 'Detalles de la Pregunta')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Detalles de la Pregunta</h2>
                        <p class="text-gray-600">Información completa de la pregunta y respuestas</p>
                    </div>
                    <div class="flex space-x-3">
                        @if(Auth::user()->isMedico() || Auth::user()->isAdmin())
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.edit' : 'medico.preguntas.edit', $pregunta->id_pregunta) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar
                        </a>
                        @endif
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.index' : (Auth::user()->isMedico() ? 'medico.preguntas.index' : 'paciente.preguntas.index')) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Pregunta</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Pregunta</label>
                                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $pregunta->texto }}</p>
                                </div>
                                @if($pregunta->descripcion)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pregunta->descripcion }}</p>
                                </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tipo</label>
                                    <span class="mt-1 inline-block px-2 py-1 text-xs font-medium rounded-full {{ $pregunta->tipo == 'abierta' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $pregunta->tipo == 'abierta' ? 'Abierta' : 'Opción Múltiple' }}
                                    </span>
                                </div>
                                @if($pregunta->tipo == 'opcion_multiple' && $pregunta->opciones)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Opciones</label>
                                    <ul class="mt-1 list-disc list-inside text-sm text-gray-900">
                                        @foreach($pregunta->opciones as $opcion)
                                            <li>{{ $opcion }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Categoría</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pregunta->categoria }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Especialidad Médica</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pregunta->especialidad_medica }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fecha de Asignación</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pregunta->fecha_asignacion ? $pregunta->fecha_asignacion->format('d/m/Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                                    <span class="mt-1 inline-block px-2 py-1 text-xs font-medium rounded-full {{ $pregunta->estado == 'activa' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $pregunta->estado == 'activa' ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if($pregunta->paciente)
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Paciente</h3>
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-lg font-medium text-blue-800">
                                            {{ substr($pregunta->paciente->usuario->nombre, 0, 1) }}{{ substr($pregunta->paciente->usuario->apPaterno ?? '', 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $pregunta->paciente->usuario->nombre }} {{ $pregunta->paciente->usuario->apPaterno }} {{ $pregunta->paciente->usuario->apMaterno }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $pregunta->paciente->usuario->correo }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="space-y-6">
                        @if($pregunta->medico)
                        <div class="bg-green-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Médico Responsable</h3>
                            <div class="flex items-center space-x-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Dr. {{ $pregunta->medico->usuario->nombre }} {{ $pregunta->medico->usuario->apPaterno }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $pregunta->medico->especialidad }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($pregunta->diagnostico)
                        <div class="bg-yellow-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Diagnóstico Vinculado</h3>
                            <p class="text-sm text-gray-900">
                                {{ $pregunta->diagnostico->catalogoDiagnostico ? $pregunta->diagnostico->catalogoDiagnostico->descripcion_clinica : $pregunta->diagnostico->descripcion }}
                            </p>
                        </div>
                        @endif

                        @if($pregunta->tratamiento)
                        <div class="bg-purple-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Tratamiento Vinculado</h3>
                            <p class="text-sm text-gray-900">{{ $pregunta->tratamiento->nombre }}</p>
                        </div>
                        @endif

                        <!-- Respuestas -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Respuestas ({{ $pregunta->total_respuestas }}/3)
                                @if($pregunta->total_respuestas >= 3)
                                    <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        Cerrada
                                    </span>
                                @endif
                            </h3>
                            @if($todasLasRespuestas && $todasLasRespuestas->count() > 0)
                                <div class="space-y-4 max-h-96 overflow-y-auto">
                                    @foreach($todasLasRespuestas as $respuesta)
                                        <div class="border-l-4 border-blue-500 pl-4 py-3 bg-blue-50 rounded">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900 mb-1">
                                                        {{ $respuesta->paciente && $respuesta->paciente->usuario ? $respuesta->paciente->usuario->nombre . ' ' . $respuesta->paciente->usuario->apPaterno . ' ' . $respuesta->paciente->usuario->apMaterno : ($respuesta->usuario ? $respuesta->usuario->nombre : 'Anónimo') }}
                                                    </p>
                                                    <p class="text-sm text-gray-700">{{ $respuesta->respuesta }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ $respuesta->fecha_hora ? $respuesta->fecha_hora->format('d/m/Y H:i') : $respuesta->fecha->format('d/m/Y') }}
                                                    </p>
                                                </div>
                                                @if($respuesta->cumplimiento)
                                                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    Cumplido
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($pregunta->respuestas && $pregunta->respuestas->count() > 0)
                                <div class="space-y-4 max-h-96 overflow-y-auto">
                                    @foreach($pregunta->respuestas as $respuesta)
                                        <div class="border-l-4 border-blue-500 pl-4 py-3 bg-blue-50 rounded">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900 mb-1">
                                                        {{ $respuesta->paciente && $respuesta->paciente->usuario ? $respuesta->paciente->usuario->nombre . ' ' . $respuesta->paciente->usuario->apPaterno . ' ' . $respuesta->paciente->usuario->apMaterno : ($respuesta->usuario ? $respuesta->usuario->nombre : 'Anónimo') }}
                                                    </p>
                                                    <p class="text-sm text-gray-700">{{ $respuesta->respuesta }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ $respuesta->fecha_hora ? $respuesta->fecha_hora->format('d/m/Y H:i') : $respuesta->fecha->format('d/m/Y') }}
                                                    </p>
                                                </div>
                                                @if($respuesta->cumplimiento)
                                                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    Cumplido
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">No hay respuestas aún</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

