@extends('layouts.app')

@section('title', 'Nueva Publicación')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Nueva Publicación</h2>
                        <p class="text-gray-600">Comparte tu experiencia con otros pacientes</p>
                    </div>
                    <a href="{{ route('foro.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Cancelar
                    </a>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route('paciente.foro.store') }}" class="space-y-6">
                    @csrf

                    <!-- Título -->
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
                            Título de la publicación <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="titulo" 
                               id="titulo"
                               value="{{ old('titulo') }}"
                               required
                               maxlength="255"
                               placeholder="Ej: Mi experiencia con la dieta recomendada"
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
                                  placeholder="Comparte tu experiencia, avances, resultados obtenidos, consejos, etc..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('contenido') border-red-500 @enderror">{{ old('contenido') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Sé descriptivo y comparte detalles que puedan ayudar a otros pacientes.</p>
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
                               value="{{ old('fecha_publicacion', now()->format('Y-m-d')) }}"
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
                                <option value="{{ $actividad->id_actividad }}" {{ old('id_actividad') == $actividad->id_actividad ? 'selected' : '' }}>
                                    {{ $actividad->nombre }} - {{ $actividad->fecha_asignacion->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Vincula esta publicación con una actividad completada.</p>
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
                                <option value="{{ $tratamiento->id_tratamiento }}" {{ old('id_tratamiento') == $tratamiento->id_tratamiento ? 'selected' : '' }}>
                                    {{ $tratamiento->nombre }} - {{ $tratamiento->fecha_inicio->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Vincula esta publicación con un tratamiento activo.</p>
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
                               value="{{ old('etiquetas') }}"
                               maxlength="500"
                               placeholder="Ej: nutrición, ejercicio, terapia, motivación (separadas por comas)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('etiquetas') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Separa las etiquetas con comas. Ejemplo: nutrición, ejercicio, motivación</p>
                        @error('etiquetas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Información importante -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Importante</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Tu publicación será revisada por un administrador antes de ser publicada. Esto puede tomar algunos días.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('foro.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Publicar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

