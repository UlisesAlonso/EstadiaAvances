@extends('layouts.app')

@section('title', 'Catálogo de Diagnósticos')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Catálogo de Diagnósticos</h2>
                        <p class="text-gray-600">Gestiona el catálogo de diagnósticos médicos disponibles</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.catalogo-diagnosticos.create' : 'medico.catalogo-diagnosticos.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nuevo Diagnóstico
                        </a>
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.diagnosticos.index' : 'medico.diagnosticos.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver a Diagnósticos
                        </a>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <form method="GET" action="{{ route(Auth::user()->isAdmin() ? 'admin.catalogo-diagnosticos.index' : 'medico.catalogo-diagnosticos.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="categoria" class="block text-sm font-medium text-gray-700 mb-1">Categoría Médica</label>
                                <input type="text" 
                                       name="categoria" 
                                       id="categoria"
                                       value="{{ request('categoria') }}"
                                       placeholder="Ej: Cardiovascular, Respiratorio..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                                <input type="text" 
                                       name="codigo" 
                                       id="codigo"
                                       value="{{ request('codigo') }}"
                                       placeholder="Código del diagnóstico..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <input type="text" 
                                       name="descripcion" 
                                       id="descripcion"
                                       value="{{ request('descripcion') }}"
                                       placeholder="Descripción clínica..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button type="submit" 
                                    class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Filtrar
                            </button>
                            <a href="{{ route(Auth::user()->isAdmin() ? 'admin.catalogo-diagnosticos.index' : 'medico.catalogo-diagnosticos.index') }}" 
                               class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tabla -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción Clínica</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado por</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($catalogoDiagnosticos as $catalogo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $catalogo->codigo ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ Str::limit($catalogo->descripcion_clinica, 80) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $catalogo->categoria_medica }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $catalogo->usuarioCreador->nombre ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $catalogo->fecha_creacion ? $catalogo->fecha_creacion->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route(Auth::user()->isAdmin() ? 'admin.catalogo-diagnosticos.show' : 'medico.catalogo-diagnosticos.show', $catalogo->id_diagnostico) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                Ver
                                            </a>
                                            <a href="{{ route(Auth::user()->isAdmin() ? 'admin.catalogo-diagnosticos.edit' : 'medico.catalogo-diagnosticos.edit', $catalogo->id_diagnostico) }}" 
                                               class="text-green-600 hover:text-green-900">
                                                Editar
                                            </a>
                                            @if(Auth::user()->isAdmin())
                                                <form method="POST" 
                                                      action="{{ route('admin.catalogo-diagnosticos.destroy', $catalogo->id_diagnostico) }}" 
                                                      class="inline"
                                                      onsubmit="return confirm('¿Estás seguro de eliminar este diagnóstico del catálogo?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron diagnósticos en el catálogo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-6">
                    {{ $catalogoDiagnosticos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


