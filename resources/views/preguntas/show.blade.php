@extends('layouts.app')

@section('title', 'Detalles de Pregunta')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Detalles de la Pregunta o Cuestionario</h2>
                        <p class="text-gray-600">Información completa y respuestas recibidas</p>
                    </div>
                    <div class="flex space-x-3">
                        @if(!$pregunta->hasRespuestas())
                            <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.edit' : 'medico.preguntas.edit', $pregunta->id_pregunta) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar Pregunta
                            </a>
                        @endif
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>

                <!-- Información de la pregunta -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Información básica -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información Básica</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">ID de la Pregunta</label>
                                    <p class="mt-1 text-sm text-gray-900 font-semibold">#{{ $pregunta->id_pregunta }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pregunta->descripcion }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tipo de Pregunta</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $pregunta->tipo === 'abierta' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $pregunta->tipo === 'abierta' ? 'Abierta' : 'Opción Múltiple' }}
                                    </span>
                                </div>

                                @if($pregunta->esOpcionMultiple() && $pregunta->opciones_multiple)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Opciones de Respuesta</label>
                                    <ul class="mt-1 list-disc list-inside space-y-1">
                                        @foreach($pregunta->opciones_multiple as $opcion)
                                            <li class="text-sm text-gray-900">{{ $opcion }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Especialidad Médica</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pregunta->especialidad_medica ?? 'No especificada' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $pregunta->activa ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $pregunta->activa ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Información de asignación -->
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Asignación</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fecha de Asignación</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pregunta->fecha_asignacion->format('d/m/Y') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fecha de Creación</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pregunta->fecha_creacion->format('d/m/Y H:i') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Creada por</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $pregunta->medico->usuario->nombre ?? 'N/A' }} {{ $pregunta->medico->usuario->apPaterno ?? '' }}
                                    </p>
                                </div>

                                @if($pregunta->paciente)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Paciente Destinatario</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $pregunta->paciente->usuario->nombre }} {{ $pregunta->paciente->usuario->apPaterno }} {{ $pregunta->paciente->usuario->apMaterno }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">{{ $pregunta->paciente->usuario->correo }}</p>
                                </div>
                                @else
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Paciente Destinatario</label>
                                    <p class="mt-1 text-sm text-gray-900">Todos los pacientes</p>
                                </div>
                                @endif

                                @if($pregunta->diagnostico)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Diagnóstico Vinculado</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $pregunta->diagnostico->catalogoDiagnostico->descripcion_clinica ?? $pregunta->diagnostico->descripcion }}
                                    </p>
                                </div>
                                @endif

                                @if($pregunta->tratamiento)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tratamiento Vinculado</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pregunta->tratamiento->nombre }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="space-y-6">
                        <div class="bg-green-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Estadísticas</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Total de Respuestas</label>
                                    <p class="mt-1 text-2xl font-bold text-green-900">{{ $respuestas->count() }}</p>
                                </div>

                                @if($respuestas->count() > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Respuestas con Cumplimiento</label>
                                    <p class="mt-1 text-2xl font-bold text-green-900">
                                        {{ $respuestas->where('cumplimiento', true)->count() }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Porcentaje de Cumplimiento</label>
                                    <p class="mt-1 text-2xl font-bold text-green-900">
                                        {{ $respuestas->count() > 0 ? round(($respuestas->where('cumplimiento', true)->count() / $respuestas->count()) * 100, 1) : 0 }}%
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Respuestas -->
                @if($respuestas->count() > 0)
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Respuestas Recibidas</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Respuesta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha y Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cumplimiento</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($respuestas as $respuesta)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $respuesta->usuario->nombre }} {{ $respuesta->usuario->apPaterno }} {{ $respuesta->usuario->apMaterno }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $respuesta->usuario->correo }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ Str::limit($respuesta->respuesta, 100) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $respuesta->fecha_respuesta->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $respuesta->cumplimiento ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $respuesta->cumplimiento ? 'Cumplido' : 'No Cumplido' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Sin respuestas</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Esta pregunta aún no ha recibido respuestas de los pacientes.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

