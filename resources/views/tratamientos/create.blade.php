@extends('layouts.app')

@section('title', 'Crear Nuevo Tratamiento')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Crear Nuevo Tratamiento</h2>
                            <p class="text-gray-600">Registra un nuevo tratamiento clínico para un paciente</p>
                        </div>
                        <a href="{{ route('medico.tratamientos.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver a Tratamientos
                        </a>
                    </div>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route('medico.tratamientos.store') }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Paciente -->
                        <div class="md:col-span-2">
                            <label for="id_paciente" class="block text-sm font-medium text-gray-700 mb-2">
                                Paciente <span class="text-red-500">*</span>
                            </label>
                            <select name="id_paciente" 
                                    id="id_paciente"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('id_paciente') border-red-500 @enderror">
                                <option value="">Selecciona un paciente</option>
                                @foreach($pacientes as $paciente)
                                    <option value="{{ $paciente->id_paciente }}" 
                                            {{ old('id_paciente') == $paciente->id_paciente ? 'selected' : '' }}>
                                        {{ $paciente->usuario->nombre }} {{ $paciente->usuario->apPaterno }} {{ $paciente->usuario->apMaterno }}
                                        - {{ $paciente->usuario->correo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_paciente')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Diagnóstico del Catálogo -->
                        <div class="md:col-span-2">
                            <label for="id_PDiag" class="block text-sm font-medium text-gray-700 mb-2">
                                Diagnóstico del Catálogo <span class="text-red-500">*</span>
                            </label>
                            <select name="id_PDiag" 
                                    id="id_PDiag"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('id_PDiag') border-red-500 @enderror">
                                <option value="">Seleccione un diagnóstico del catálogo</option>
                                @if(isset($catalogoDiagnosticos) && $catalogoDiagnosticos->count() > 0)
                                    @php
                                        $categoriaActual = '';
                                    @endphp
                                    @foreach($catalogoDiagnosticos as $catalogo)
                                        @if($categoriaActual !== $catalogo->categoria_medica)
                                            @if($categoriaActual !== '')
                                                </optgroup>
                                            @endif
                                            <optgroup label="{{ $catalogo->categoria_medica }}">
                                            @php
                                                $categoriaActual = $catalogo->categoria_medica;
                                            @endphp
                                        @endif
                                        <option value="{{ $catalogo->id_diagnostico }}" 
                                                {{ old('id_PDiag') == $catalogo->id_diagnostico ? 'selected' : '' }}>
                                            {{ $catalogo->codigo ? $catalogo->codigo . ' - ' : '' }}{{ $catalogo->descripcion_clinica }}
                                        </option>
                                    @endforeach
                                    @if($categoriaActual !== '')
                                        </optgroup>
                                    @endif
                                @else
                                    <option value="" disabled>No hay diagnósticos disponibles en el catálogo</option>
                                @endif
                            </select>
                            <p class="mt-1 text-sm text-gray-500">
                                Selecciona un diagnóstico del catálogo. Si no encuentras el diagnóstico, puedes 
                                <a href="{{ route(Auth::user()->isAdmin() ? 'admin.catalogo-diagnosticos.create' : 'medico.catalogo-diagnosticos.create') }}" 
                                   class="text-blue-600 hover:text-blue-800 underline" 
                                   target="_blank">
                                    agregar uno nuevo al catálogo
                                </a>
                            </p>
                            @error('id_PDiag')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nombre del Tratamiento -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre del Tratamiento <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre"
                                   value="{{ old('nombre') }}"
                                   required
                                   placeholder="Ej: Metformina, Fisioterapia, etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('nombre') border-red-500 @enderror">
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dosis -->
                        <div>
                            <label for="dosis" class="block text-sm font-medium text-gray-700 mb-2">
                                Dosis/Concentración <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="dosis" 
                                   id="dosis"
                                   value="{{ old('dosis') }}"
                                   required
                                   placeholder="Ej: 500mg, 10ml, etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('dosis') border-red-500 @enderror">
                            @error('dosis')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Frecuencia -->
                        <div>
                            <label for="frecuencia" class="block text-sm font-medium text-gray-700 mb-2">
                                Frecuencia <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="frecuencia" 
                                   id="frecuencia"
                                   value="{{ old('frecuencia') }}"
                                   required
                                   placeholder="Ej: Cada 8 horas, 2 veces al día, etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('frecuencia') border-red-500 @enderror">
                            @error('frecuencia')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duración -->
                        <div>
                            <label for="duracion" class="block text-sm font-medium text-gray-700 mb-2">
                                Duración <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="duracion" 
                                   id="duracion"
                                   value="{{ old('duracion') }}"
                                   required
                                   placeholder="Ej: 30 días, 2 semanas, etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('duracion') border-red-500 @enderror">
                            @error('duracion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha de Inicio -->
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Inicio <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="fecha_inicio" 
                                   id="fecha_inicio"
                                   value="{{ old('fecha_inicio', date('Y-m-d')) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('fecha_inicio') border-red-500 @enderror">
                            @error('fecha_inicio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Observaciones -->
                        <div class="md:col-span-2">
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                                Observaciones Clínicas
                            </label>
                            <textarea name="observaciones" 
                                      id="observaciones"
                                      rows="4"
                                      placeholder="Observaciones adicionales sobre el tratamiento, efectos secundarios esperados, precauciones, etc."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('observaciones') border-red-500 @enderror">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('medico.tratamientos.index') }}" 
                           class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Crear Tratamiento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
