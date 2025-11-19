@extends('layouts.app')

@section('title', 'Detalles del Análisis Clínico')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Detalles del Análisis Clínico</h2>
                        <p class="text-gray-600">Información completa del análisis clínico</p>
                    </div>
                    <div class="flex space-x-3">
                        @if(Auth::user()->isMedico() || Auth::user()->isAdmin())
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.analisis-clinicos.edit' : 'medico.analisis-clinicos.edit', $analisis->id_analisis) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar
                        </a>
                        @endif
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.analisis-clinicos.index' : (Auth::user()->isMedico() ? 'medico.analisis-clinicos.index' : 'paciente.analisis-clinicos.index')) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Información básica -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Análisis</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tipo de Análisis</label>
                                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $analisis->tipo_analisis }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fecha del Análisis</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $analisis->fecha ? $analisis->fecha->format('d/m/Y') : 'N/A' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $analisis->descripcion ?? 'Sin descripción' }}</p>
                                </div>

                                @if($analisis->resultado)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Resultado</label>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $analisis->resultado }}</p>
                                </div>
                                @endif

                                @if($analisis->observaciones_clinicas)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Observaciones Clínicas</label>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $analisis->observaciones_clinicas }}</p>
                                </div>
                                @endif

                                @if(Auth::user()->isAdmin())
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                                    <span class="mt-1 inline-block px-2 py-1 text-xs font-medium rounded-full {{ ($analisis->estado ?? 'activo') == 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ($analisis->estado ?? 'activo') == 'activo' ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Información del paciente -->
                        @if($analisis->paciente)
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Paciente</h3>
                            
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-lg font-medium text-blue-800">
                                            {{ substr($analisis->paciente->usuario->nombre, 0, 1) }}{{ substr($analisis->paciente->usuario->apPaterno ?? '', 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $analisis->paciente->usuario->nombre }} {{ $analisis->paciente->usuario->apPaterno }} {{ $analisis->paciente->usuario->apMaterno }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $analisis->paciente->usuario->correo }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Información médica y valores -->
                    <div class="space-y-6">
                        <!-- Información del médico -->
                        @if($analisis->medico)
                        <div class="bg-green-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Médico Responsable</h3>
                            
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    Dr. {{ $analisis->medico->usuario->nombre }} {{ $analisis->medico->usuario->apPaterno }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $analisis->medico->especialidad }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Valores Cuantitativos -->
                        @if($analisis->valores_cuantitativos && count($analisis->valores_cuantitativos) > 0)
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Valores Cuantitativos</h3>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($analisis->valores_cuantitativos as $valor)
                                        <tr>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $valor['nombre'] ?? 'N/A' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $valor['valor'] ?? 'N/A' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $valor['unidad'] ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Gráfica simple para valores numéricos -->
                            @if(count($analisis->valores_cuantitativos) > 0)
                            <div class="mt-6">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Resumen Visual</h4>
                                <div class="space-y-3">
                                    @foreach($analisis->valores_cuantitativos as $valor)
                                        @if(isset($valor['valor']) && is_numeric($valor['valor']))
                                        <div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-700">{{ $valor['nombre'] ?? 'Valor' }}</span>
                                                <span class="text-gray-900 font-medium">{{ $valor['valor'] }} {{ $valor['unidad'] ?? '' }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                @php
                                                    $maxValue = 100; // Valor máximo para la escala (puede ajustarse)
                                                    $percentage = min(($valor['valor'] / $maxValue) * 100, 100);
                                                @endphp
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

