@extends('layouts.app')

@section('title', 'Gestión de Diagnósticos')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Gestión de Diagnósticos</h2>
                        <p class="text-gray-600">Administra los diagnósticos médicos del sistema</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.diagnosticos.create' : 'medico.diagnosticos.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nuevo Diagnóstico
                        </a>
                        <a href="{{ route('medico.dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver al Dashboard
                        </a>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <form method="GET" action="{{ route(Auth::user()->isAdmin() ? 'admin.diagnosticos.index' : 'medico.diagnosticos.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="paciente" class="block text-sm font-medium text-gray-700 mb-1">Paciente</label>
                                <input type="text" 
                                       name="paciente" 
                                       id="paciente"
                                       value="{{ request('paciente') }}"
                                       placeholder="Nombre del paciente..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            
                            <div>
                                <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                                <input type="date" 
                                       name="fecha" 
                                       id="fecha"
                                       value="{{ request('fecha') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            
                            <div class="flex items-end">
                                <button type="submit" 
                                        class="w-full px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Filtrar
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <a href="{{ route(Auth::user()->isAdmin() ? 'admin.diagnosticos.index' : 'medico.diagnosticos.index') }}" 
                               class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Limpiar Filtros
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tabla de diagnósticos -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnóstico (Catálogo)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                @if(Auth::user()->isAdmin())
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médico</th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($diagnosticos as $diagnostico)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-blue-800">
                                                        {{ substr($diagnostico->paciente->usuario->nombre, 0, 1) }}{{ substr($diagnostico->paciente->usuario->apPaterno, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $diagnostico->paciente->usuario->nombre }} {{ $diagnostico->paciente->usuario->apPaterno }} {{ $diagnostico->paciente->usuario->apMaterno }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $diagnostico->paciente->usuario->correo }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($diagnostico->catalogoDiagnostico)
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-900">
                                                    {{ $diagnostico->catalogoDiagnostico->codigo ? $diagnostico->catalogoDiagnostico->codigo . ' - ' : '' }}
                                                    {{ Str::limit($diagnostico->catalogoDiagnostico->descripcion_clinica, 60) }}
                                                </span>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                                                        {{ $diagnostico->catalogoDiagnostico->categoria_medica }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">No especificado</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $diagnostico->fecha->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ Str::limit($diagnostico->descripcion, 100) }}</div>
                                    </td>
                                    @if(Auth::user()->isAdmin())
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Dr. {{ $diagnostico->medico->usuario->nombre }} {{ $diagnostico->medico->usuario->apPaterno }}
                                    </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route(Auth::user()->isAdmin() ? 'admin.diagnosticos.show' : 'medico.diagnosticos.show', $diagnostico->id_diagnostico) }}" 
                                               class="text-blue-600 hover:text-blue-900"
                                               title="Ver detalles">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route(Auth::user()->isAdmin() ? 'admin.diagnosticos.edit' : 'medico.diagnosticos.edit', $diagnostico->id_diagnostico) }}" 
                                               class="text-yellow-600 hover:text-yellow-900"
                                               title="Editar diagnóstico">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route(Auth::user()->isAdmin() ? 'admin.diagnosticos.destroy' : 'medico.diagnosticos.destroy', $diagnostico->id_diagnostico) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('¿Estás seguro de eliminar este diagnóstico?')"
                                                        title="Eliminar diagnóstico">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->isAdmin() ? '5' : '4' }}" class="px-6 py-4 text-center text-gray-500">
                                        <div class="text-center py-8">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-gray-500 mt-2">No se encontraron diagnósticos</p>
                                            <p class="text-gray-400 text-sm mt-1">Los diagnósticos aparecerán aquí cuando se creen</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($diagnosticos->hasPages())
                    <div class="mt-6">
                        {{ $diagnosticos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
