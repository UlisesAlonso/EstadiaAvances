@extends('layouts.app')

@section('title', 'Crear Diagnóstico')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Crear Nuevo Diagnóstico</h2>
                        <p class="text-gray-600">Registra un nuevo diagnóstico médico en el sistema</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.diagnosticos.index' : 'medico.diagnosticos.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver a Diagnósticos
                        </a>
                    </div>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route(Auth::user()->isAdmin() ? 'admin.diagnosticos.store' : 'medico.diagnosticos.store') }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Paciente -->
                        <div class="md:col-span-2">
                            <label for="id_paciente" class="block text-sm font-medium text-gray-700 mb-2">
                                Paciente *
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

                        <!-- Catálogo de Diagnóstico -->
                        <div class="md:col-span-2">
                            <label for="id_PDiag" class="block text-sm font-medium text-gray-700 mb-2">
                                Diagnóstico del Catálogo *
                            </label>
                            <select name="id_PDiag" 
                                    id="id_PDiag"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('id_PDiag') border-red-500 @enderror">
                                <option value="">Selecciona un diagnóstico del catálogo</option>
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
                            </select>
                            <p class="mt-1 text-sm text-gray-500">
                                Si no encuentras el diagnóstico, puedes 
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

                        <!-- Fecha -->
                        <div>
                            <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha del Diagnóstico *
                            </label>
                            <input type="date" 
                                   name="fecha" 
                                   id="fecha"
                                   value="{{ old('fecha', date('Y-m-d')) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('fecha') border-red-500 @enderror">
                            @error('fecha')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="md:col-span-2">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción del Diagnóstico *
                            </label>
                            <textarea name="descripcion" 
                                      id="descripcion"
                                      rows="6"
                                      required
                                      maxlength="1000"
                                      placeholder="Describe detalladamente el diagnóstico médico..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('descripcion') border-red-500 @enderror">{{ old('descripcion') }}</textarea>
                            <div class="mt-1 flex justify-between text-sm text-gray-500">
                                <span>Máximo 1000 caracteres</span>
                                <span id="char-count">0/1000</span>
                            </div>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Información importante
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>El diagnóstico será registrado con tu información como médico responsable</li>
                                        <li>La fecha del diagnóstico se registrará automáticamente</li>
                                        <li>Este diagnóstico quedará vinculado al historial clínico del paciente</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.diagnosticos.index' : 'medico.diagnosticos.index') }}" 
                           class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Crear Diagnóstico
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('descripcion');
    const charCount = document.getElementById('char-count');
    
    textarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length + '/1000';
        
        if (length > 1000) {
            charCount.classList.add('text-red-600');
            charCount.classList.remove('text-gray-500');
        } else {
            charCount.classList.remove('text-red-600');
            charCount.classList.add('text-gray-500');
        }
    });
    
    // Inicializar contador
    charCount.textContent = textarea.value.length + '/1000';
});
</script>
@endsection
