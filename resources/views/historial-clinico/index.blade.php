@extends('layouts.app')

@section('title', 'Gestión de Historial Clínico')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Gestión de Historial Clínico</h2>
                        <p class="text-gray-600">Administra los eventos clínicos de los pacientes</p>
                    </div>
                    @auth
                        @if(auth()->user()->isMedico() || auth()->user()->isAdmin())
                            <a href="{{ route('historial-clinico.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Nuevo Evento Clínico
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Filtros -->
                @auth
                    @if(auth()->user()->isMedico() || auth()->user()->isAdmin())
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <form method="GET" action="{{ route('historial-clinico.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label for="nombre_paciente" class="block text-sm font-medium text-gray-700 mb-1">Buscar por paciente</label>
                                    <input type="text" 
                                           name="nombre_paciente" 
                                           id="nombre_paciente"
                                           value="{{ request('nombre_paciente') }}"
                                           placeholder="Nombre del paciente..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div>
                                    <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-1">Fecha desde</label>
                                    <input type="date" 
                                           name="fecha_desde" 
                                           id="fecha_desde"
                                           value="{{ request('fecha_desde') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div>
                                    <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-1">Fecha hasta</label>
                                    <input type="date" 
                                           name="fecha_hasta" 
                                           id="fecha_hasta"
                                           value="{{ request('fecha_hasta') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div>
                                    <label for="id_diagnostico" class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico</label>
                                    <select name="id_diagnostico" 
                                            id="id_diagnostico"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Todos los diagnósticos</option>
                                        @if(isset($diagnosticos))
                                            @foreach($diagnosticos as $diagnostico)
                                                <option value="{{ $diagnostico->id_diagnostico }}" {{ request('id_diagnostico') == $diagnostico->id_diagnostico ? 'selected' : '' }}>
                                                    @if($diagnostico->catalogoDiagnostico)
                                                        {{ $diagnostico->catalogoDiagnostico->descripcion_clinica }}
                                                    @else
                                                        {{ $diagnostico->descripcion }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                
                                @if(auth()->user()->isMedico())
                                    <div>
                                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                        <select name="estado" 
                                                id="estado"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Todos los estados</option>
                                            <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                            <option value="cerrado" {{ request('estado') == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                                        </select>
                                    </div>
                                @endif
                                
                                <div class="flex items-end gap-2">
                                    <button type="submit" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Filtrar
                                    </button>
                                    
                                    @if(request()->hasAny(['nombre_paciente', 'fecha_desde', 'fecha_hasta', 'id_diagnostico', 'estado']))
                                        <a href="{{ route('historial-clinico.index') }}" 
                                           class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                            Limpiar
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    @endif
                @endauth

                <!-- Tabla de historiales -->
                @if($historiales->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Evento</th>
                                    @if(auth()->user()->isMedico() || auth()->user()->isAdmin())
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médico</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnóstico</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tratamiento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($historiales as $historial)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $historial->fecha_evento ? $historial->fecha_evento->format('d/m/Y') : ($historial->fecha_registro->format('d/m/Y')) }}
                                        </td>
                                        @if(auth()->user()->isMedico() || auth()->user()->isAdmin())
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $historial->paciente->usuario->nombre }}
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($historial->medico)
                                                {{ $historial->medico->usuario->nombre }}
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($historial->diagnostico && $historial->diagnostico->catalogoDiagnostico)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ Str::limit($historial->diagnostico->catalogoDiagnostico->descripcion_clinica, 30) }}
                                                </span>
                                            @elseif($historial->diagnostico)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ Str::limit($historial->diagnostico->descripcion, 30) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($historial->tratamiento)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ Str::limit($historial->tratamiento->nombre, 30) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($historial->estado == 'activo')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Activo
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Cerrado
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('historial-clinico.show', $historial->id_historial) }}" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                @if((auth()->user()->isMedico() || auth()->user()->isAdmin()) && $historial->estado == 'activo')
                                                    <a href="{{ route('historial-clinico.edit', $historial->id_historial) }}" 
                                                       class="text-yellow-600 hover:text-yellow-900">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('historial-clinico.cerrar', $historial->id_historial) }}" 
                                                          method="POST" 
                                                          class="inline"
                                                          onsubmit="return confirm('¿Está seguro de cerrar este evento clínico?');">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" 
                                                                class="text-orange-600 hover:text-orange-900"
                                                                title="Cerrar evento">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-6">
                        {{ $historiales->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay registros de historial clínico</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if(auth()->user()->isMedico() || auth()->user()->isAdmin())
                                Comienza creando un nuevo evento clínico.
                            @else
                                Aún no tienes registros en tu historial clínico.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


