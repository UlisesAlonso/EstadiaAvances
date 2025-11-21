@extends('layouts.app')

@section('title', 'Crear Análisis Clínico')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Crear Nuevo Análisis Clínico</h2>
                        <p class="text-gray-600">Registra un nuevo análisis clínico en el sistema</p>
                    </div>
                    <a href="{{ route(Auth::user()->isAdmin() ? 'admin.analisis-clinicos.index' : 'medico.analisis-clinicos.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver
                    </a>
                </div>

                <form method="POST" action="{{ route(Auth::user()->isAdmin() ? 'admin.analisis-clinicos.store' : 'medico.analisis-clinicos.store') }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Paciente -->
                        <div class="md:col-span-2">
                            <label for="id_paciente" class="block text-sm font-medium text-gray-700 mb-2">Paciente *</label>
                            <select name="id_paciente" 
                                    id="id_paciente"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('id_paciente') border-red-500 @enderror">
                                <option value="">Selecciona un paciente</option>
                                @foreach($pacientes as $paciente)
                                    <option value="{{ $paciente->id_paciente }}" {{ old('id_paciente') == $paciente->id_paciente ? 'selected' : '' }}>
                                        {{ $paciente->usuario->nombre }} {{ $paciente->usuario->apPaterno }} {{ $paciente->usuario->apMaterno }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_paciente')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo de Análisis -->
                        <div>
                            <label for="tipo_analisis" class="block text-sm font-medium text-gray-700 mb-2">Tipo o Nombre del Análisis *</label>
                            <input type="text" 
                                   name="tipo_analisis" 
                                   id="tipo_analisis"
                                   required
                                   value="{{ old('tipo_analisis') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('tipo_analisis') border-red-500 @enderror"
                                   placeholder="Ej: Hemograma completo, Química sanguínea, etc.">
                            @error('tipo_analisis')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">Fecha del Análisis *</label>
                            <input type="date" 
                                   name="fecha" 
                                   id="fecha"
                                   required
                                   value="{{ old('fecha', date('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('fecha') border-red-500 @enderror">
                            @error('fecha')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="md:col-span-2">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción Detallada *</label>
                            <textarea name="descripcion" 
                                      id="descripcion"
                                      rows="4"
                                      required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('descripcion') border-red-500 @enderror"
                                      placeholder="Describe el análisis clínico realizado...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Resultado -->
                        <div class="md:col-span-2">
                            <label for="resultado" class="block text-sm font-medium text-gray-700 mb-2">Resultado (Opcional)</label>
                            <textarea name="resultado" 
                                      id="resultado"
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                      placeholder="Resultado del análisis...">{{ old('resultado') }}</textarea>
                        </div>

                        <!-- Valores Cuantitativos -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Valores Cuantitativos (Opcional)</label>
                            <div id="valores-container" class="space-y-2">
                                <div class="flex items-center space-x-2 valores-item">
                                    <input type="text" 
                                           name="valores_cuantitativos[0][nombre]" 
                                           placeholder="Nombre del valor (ej: Glucosa)"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <input type="number" 
                                           step="0.01"
                                           name="valores_cuantitativos[0][valor]" 
                                           placeholder="Valor"
                                           class="w-32 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <input type="text" 
                                           name="valores_cuantitativos[0][unidad]" 
                                           placeholder="Unidad (ej: mg/dL)"
                                           class="w-32 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <button type="button" onclick="removeValor(this)" class="text-red-600 hover:text-red-800">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <button type="button" onclick="addValor()" class="mt-2 px-4 py-2 text-sm text-blue-600 hover:text-blue-800">
                                + Agregar Valor Cuantitativo
                            </button>
                        </div>

                        <!-- Observaciones Clínicas -->
                        <div class="md:col-span-2">
                            <label for="observaciones_clinicas" class="block text-sm font-medium text-gray-700 mb-2">Observaciones Clínicas (Opcional)</label>
                            <textarea name="observaciones_clinicas" 
                                      id="observaciones_clinicas"
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                      placeholder="Observaciones y notas clínicas relevantes...">{{ old('observaciones_clinicas') }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.analisis-clinicos.index' : 'medico.analisis-clinicos.index') }}" 
                           class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            Crear Análisis
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let valorIndex = 1;

function addValor() {
    const container = document.getElementById('valores-container');
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2 valores-item';
    div.innerHTML = `
        <input type="text" 
               name="valores_cuantitativos[${valorIndex}][nombre]" 
               placeholder="Nombre del valor"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
        <input type="number" 
               step="0.01"
               name="valores_cuantitativos[${valorIndex}][valor]" 
               placeholder="Valor"
               class="w-32 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
        <input type="text" 
               name="valores_cuantitativos[${valorIndex}][unidad]" 
               placeholder="Unidad"
               class="w-32 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
        <button type="button" onclick="removeValor(this)" class="text-red-600 hover:text-red-800">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
    valorIndex++;
}

function removeValor(button) {
    const items = document.querySelectorAll('.valores-item');
    if (items.length > 1) {
        button.parentElement.remove();
    }
}
</script>
@endsection

