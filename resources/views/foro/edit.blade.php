@extends('layouts.app')

@section('title', 'Editar Publicación')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Editar Publicación</h2>
                        <p class="text-gray-600">Modifica tu publicación</p>
                    </div>
                    <a href="{{ route('foro.show', $publicacion->id_publicacion) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Cancelar
                    </a>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route('paciente.foro.update', $publicacion->id_publicacion) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Título -->
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
                            Título de la publicación <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="titulo" 
                               id="titulo"
                               value="{{ old('titulo', $publicacion->titulo) }}"
                               required
                               maxlength="255"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('titulo') border-red-500 @enderror">
                        @error('titulo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contenido -->
                    <div>
                        <label for="contenido" class="block text-sm font-medium text-gray-700 mb-2">
                            Contenido <span class="text-red-500">*</span>
                        </label>
                        <textarea name="contenido" 
                                  id="contenido"
                                  rows="10"
                                  required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('contenido') border-red-500 @enderror">{{ old('contenido', $publicacion->contenido) }}</textarea>
                        @error('contenido')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de publicación -->
                    <div>
                        <label for="fecha_publicacion" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de la experiencia <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="fecha_publicacion" 
                               id="fecha_publicacion"
                               value="{{ old('fecha_publicacion', $publicacion->fecha_publicacion->format('Y-m-d')) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('fecha_publicacion') border-red-500 @enderror">
                        @error('fecha_publicacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actividad relacionada (opcional) -->
                    @if(isset($actividades) && $actividades->count() > 0)
                    <div>
                        <label for="id_actividad" class="block text-sm font-medium text-gray-700 mb-2">
                            Actividad relacionada (opcional)
                        </label>
                        <select name="id_actividad" 
                                id="id_actividad"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('id_actividad') border-red-500 @enderror">
                            <option value="">Ninguna</option>
                            @foreach($actividades as $actividad)
                                <option value="{{ $actividad->id_actividad }}" {{ old('id_actividad', $publicacion->id_actividad) == $actividad->id_actividad ? 'selected' : '' }}>
                                    {{ $actividad->nombre }} - {{ $actividad->fecha_asignacion->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_actividad')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    <!-- Tratamiento relacionado (opcional) -->
                    @if(isset($tratamientos) && $tratamientos->count() > 0)
                    <div>
                        <label for="id_tratamiento" class="block text-sm font-medium text-gray-700 mb-2">
                            Tratamiento relacionado (opcional)
                        </label>
                        <select name="id_tratamiento" 
                                id="id_tratamiento"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('id_tratamiento') border-red-500 @enderror">
                            <option value="">Ninguno</option>
                            @foreach($tratamientos as $tratamiento)
                                <option value="{{ $tratamiento->id_tratamiento }}" {{ old('id_tratamiento', $publicacion->id_tratamiento) == $tratamiento->id_tratamiento ? 'selected' : '' }}>
                                    {{ $tratamiento->nombre }} - {{ $tratamiento->fecha_inicio->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_tratamiento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    <!-- Etiquetas -->
                    <div>
                        <label for="etiquetas" class="block text-sm font-medium text-gray-700 mb-2">
                            Etiquetas (opcional)
                        </label>
                        <input type="text" 
                               name="etiquetas" 
                               id="etiquetas"
                               value="{{ old('etiquetas', $publicacion->etiquetas) }}"
                               maxlength="500"
                               placeholder="Ej: nutrición, ejercicio, terapia, motivación (separadas por comas)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('etiquetas') border-red-500 @enderror">
                        @error('etiquetas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Información importante -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Importante</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Al editar tu publicación, volverá a estado "pendiente" y necesitará ser aprobada nuevamente por un administrador.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('foro.show', $publicacion->id_publicacion) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

