@extends('layouts.app')

@section('title', 'Seleccionar Paciente - Seguimiento')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">Seguimiento del Paciente</h1>
                        <p class="text-blue-100">Selecciona un paciente para ver su seguimiento completo</p>
                    </div>
                    <a href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'medico.dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 border border-white text-sm font-medium rounded-md text-white bg-white bg-opacity-20 hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Mensajes -->
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Búsqueda -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route(auth()->user()->isAdmin() ? 'admin.seguimiento.index' : 'medico.seguimiento.index') }}" class="flex gap-4">
                    <div class="flex-1">
                        <input type="text" 
                               name="buscar" 
                               id="buscar"
                               value="{{ request('buscar') }}"
                               placeholder="Buscar por nombre, apellido o correo..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Buscar
                    </button>
                    @if(request('buscar'))
                        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.seguimiento.index' : 'medico.seguimiento.index') }}" 
                           class="inline-flex items-center px-6 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Lista de Pacientes -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Pacientes Registrados</h2>
                
                @if($pacientes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($pacientes as $paciente)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $paciente->usuario->nombre }} {{ $paciente->usuario->apPaterno }} {{ $paciente->usuario->apMaterno }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $paciente->usuario->correo }}</p>
                                        @if($paciente->fecha_nacimiento)
                                            <p class="text-xs text-gray-500 mt-1">
                                                Edad: {{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age }} años
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-4">
                                    <a href="{{ route(auth()->user()->isAdmin() ? 'admin.seguimiento.index' : 'medico.seguimiento.index', $paciente->id_paciente) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Ver Seguimiento
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginación -->
                    <div class="mt-6">
                        {{ $pacientes->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron pacientes</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if(request('buscar'))
                                No hay pacientes que coincidan con tu búsqueda.
                            @else
                                No hay pacientes registrados en el sistema.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

