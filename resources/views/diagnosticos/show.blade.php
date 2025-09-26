@extends('layouts.app')

@section('title', 'Detalles del Diagnóstico')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Detalles del Diagnóstico</h2>
                        <p class="text-gray-600">Información completa del diagnóstico médico</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('medico.diagnosticos.edit', $diagnostico->id_diagnostico) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar Diagnóstico
                        </a>
                        <a href="{{ route('medico.diagnosticos.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver a Diagnósticos
                        </a>
                    </div>
                </div>

                <!-- Información del diagnóstico -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Información básica -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Diagnóstico</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fecha del Diagnóstico</label>
                                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $diagnostico->fecha->format('d/m/Y') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $diagnostico->descripcion }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Información del paciente -->
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Paciente</h3>
                            
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-lg font-medium text-blue-800">
                                            {{ substr($diagnostico->paciente->usuario->nombre, 0, 1) }}{{ substr($diagnostico->paciente->usuario->apPaterno, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $diagnostico->paciente->usuario->nombre }} {{ $diagnostico->paciente->usuario->apPaterno }} {{ $diagnostico->paciente->usuario->apMaterno }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $diagnostico->paciente->usuario->correo }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información médica -->
                    <div class="space-y-6">
                        <!-- Información del médico -->
                        <div class="bg-green-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Médico Responsable</h3>
                            
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="text-lg font-medium text-green-800">
                                            {{ substr($diagnostico->medico->usuario->nombre, 0, 1) }}{{ substr($diagnostico->medico->usuario->apPaterno, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Dr. {{ $diagnostico->medico->usuario->nombre }} {{ $diagnostico->medico->usuario->apPaterno }} {{ $diagnostico->medico->usuario->apMaterno }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $diagnostico->medico->especialidad }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Información del sistema -->
                        <div class="bg-purple-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Sistema</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">ID del Diagnóstico</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $diagnostico->id_diagnostico }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Registrado el</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($diagnostico->created_at)
                                            {{ $diagnostico->created_at->format('d/m/Y H:i') }}
                                        @else
                                            No disponible
                                        @endif
                                    </p>
                                </div>

                                @if($diagnostico->updated_at && $diagnostico->updated_at != $diagnostico->created_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Última modificación</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $diagnostico->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">
                    <div class="flex space-x-3">
                        <a href="{{ route('medico.diagnosticos.edit', $diagnostico->id_diagnostico) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar Diagnóstico
                        </a>
                        
                        <form method="POST" action="{{ route('medico.diagnosticos.destroy', $diagnostico->id_diagnostico) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    onclick="return confirm('¿Estás seguro de eliminar este diagnóstico? Esta acción no se puede deshacer.')">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Eliminar Diagnóstico
                            </button>
                        </form>
                    </div>
                    
                    <div class="text-sm text-gray-500">
                        <p>Diagnóstico #{{ $diagnostico->id_diagnostico }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
