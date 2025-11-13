@extends('layouts.app')

@section('title', 'Detalles del Diagnóstico del Catálogo')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Detalles del Diagnóstico del Catálogo</h2>
                        <p class="text-gray-600">Información completa del diagnóstico médico</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.catalogo-diagnosticos.edit' : 'medico.catalogo-diagnosticos.edit', $catalogoDiagnostico->id_diagnostico) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar
                        </a>
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.catalogo-diagnosticos.index' : 'medico.catalogo-diagnosticos.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver al Catálogo
                        </a>
                    </div>
                </div>

                <!-- Información del Diagnóstico -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Código</h3>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $catalogoDiagnostico->codigo ?? 'No especificado' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Categoría Médica</h3>
                        <p class="text-lg font-semibold text-gray-900">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $catalogoDiagnostico->categoria_medica }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Descripción Clínica</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $catalogoDiagnostico->descripcion_clinica }}</p>
                    </div>
                </div>

                <!-- Información de Auditoría -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Auditoría</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Creado por</h4>
                            <p class="text-gray-900">
                                {{ $catalogoDiagnostico->usuarioCreador->nombre ?? 'N/A' }}
                                @if($catalogoDiagnostico->usuarioCreador)
                                    <span class="text-gray-500 text-sm">({{ $catalogoDiagnostico->usuarioCreador->correo }})</span>
                                @endif
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $catalogoDiagnostico->fecha_creacion ? $catalogoDiagnostico->fecha_creacion->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        </div>

                        @if($catalogoDiagnostico->usuarioModificador)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Última modificación por</h4>
                            <p class="text-gray-900">
                                {{ $catalogoDiagnostico->usuarioModificador->nombre }}
                                <span class="text-gray-500 text-sm">({{ $catalogoDiagnostico->usuarioModificador->correo }})</span>
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $catalogoDiagnostico->fecha_modificacion ? $catalogoDiagnostico->fecha_modificacion->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Diagnósticos Asociados -->
                @if($catalogoDiagnostico->diagnosticos && $catalogoDiagnostico->diagnosticos->count() > 0)
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Diagnósticos Asociados ({{ $catalogoDiagnostico->diagnosticos->count() }})
                    </h3>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm text-yellow-800">
                            Este diagnóstico del catálogo está siendo utilizado en {{ $catalogoDiagnostico->diagnosticos->count() }} diagnóstico(s) de paciente(s).
                            @if(Auth::user()->isAdmin())
                                No se puede eliminar mientras tenga diagnósticos asociados.
                            @endif
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


