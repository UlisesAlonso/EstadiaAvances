@extends('layouts.app')

@section('title', 'Reportes del Sistema')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Reportes del Sistema</h2>
                    <p class="text-gray-600 mt-2">Genera y descarga reportes clínicos y administrativos en formato PDF</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al Dashboard
                </a>
            </div>
        </div>

        <!-- Grid de Reportes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($reportes as $reporte)
                @php
                    $colorClasses = [
                        'blue' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-600', 'button' => 'bg-blue-600 hover:bg-blue-700', 'ring' => 'ring-blue-500'],
                        'green' => ['bg' => 'bg-green-500', 'text' => 'text-green-600', 'button' => 'bg-green-600 hover:bg-green-700', 'ring' => 'ring-green-500'],
                        'purple' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-600', 'button' => 'bg-purple-600 hover:bg-purple-700', 'ring' => 'ring-purple-500'],
                        'red' => ['bg' => 'bg-red-500', 'text' => 'text-red-600', 'button' => 'bg-red-600 hover:bg-red-700', 'ring' => 'ring-red-500'],
                        'yellow' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-600', 'button' => 'bg-yellow-600 hover:bg-yellow-700', 'ring' => 'ring-yellow-500'],
                        'indigo' => ['bg' => 'bg-indigo-500', 'text' => 'text-indigo-600', 'button' => 'bg-indigo-600 hover:bg-indigo-700', 'ring' => 'ring-indigo-500'],
                    ];
                    $colors = $colorClasses[$reporte['color']] ?? $colorClasses['blue'];
                @endphp
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-200 overflow-hidden">
                    <!-- Header de la tarjeta con color -->
                    <div class="h-2 {{ $colors['bg'] }}"></div>
                    
                    <div class="p-6">
                        <!-- Icono y título -->
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                @if($reporte['icono'] == 'calendar')
                                    <svg class="h-10 w-10 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                @elseif($reporte['icono'] == 'pills')
                                    <svg class="h-10 w-10 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                    </svg>
                                @elseif($reporte['icono'] == 'chart-line')
                                    <svg class="h-10 w-10 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                @elseif($reporte['icono'] == 'file-medical')
                                    <svg class="h-10 w-10 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                @elseif($reporte['icono'] == 'tasks')
                                    <svg class="h-10 w-10 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                @elseif($reporte['icono'] == 'flask')
                                    <svg class="h-10 w-10 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                    </svg>
                                @endif
                            </div>
                            <h3 class="ml-3 text-xl font-semibold text-gray-900">{{ $reporte['titulo'] }}</h3>
                        </div>

                        <!-- Descripción -->
                        <p class="text-gray-600 text-sm mb-6">{{ $reporte['descripcion'] }}</p>

                        <!-- Botón de acción -->
                        <a href="{{ route($reporte['ruta']) }}" 
                           class="inline-flex items-center justify-center w-full px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white {{ $colors['button'] }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:{{ $colors['ring'] }} transition-colors">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Generar Reporte
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Información adicional -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Información sobre los Reportes</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Los reportes se generan en formato PDF y pueden incluir gráficas de pastel y barras según el tipo de reporte. Todos los reportes pueden ser filtrados por fechas, pacientes, médicos u otros criterios según corresponda.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

