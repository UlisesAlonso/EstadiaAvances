@extends('layouts.app')

@section('title', 'Editar Pregunta')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Editar Pregunta</h2>
                        <p class="text-gray-600">Modifica los datos de la pregunta</p>
                    </div>
                    <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver
                    </a>
                </div>

                <form method="POST" action="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.update' : 'medico.preguntas.update', $pregunta->id_pregunta) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="texto" class="block text-sm font-medium text-gray-700 mb-2">Texto de la Pregunta *</label>
                            <textarea name="texto" 
                                      id="texto"
                                      rows="3"
                                      required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('texto') border-red-500 @enderror">{{ old('texto', $pregunta->texto) }}</textarea>
                            @error('texto')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción (Opcional)</label>
                            <textarea name="descripcion" 
                                      id="descripcion"
                                      rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('descripcion', $pregunta->descripcion) }}</textarea>
                        </div>

                        <div>
                            <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Pregunta *</label>
                            <select name="tipo" 
                                    id="tipo"
                                    required
                                    onchange="toggleOpciones()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('tipo') border-red-500 @enderror">
                                <option value="abierta" {{ old('tipo', $pregunta->tipo) == 'abierta' ? 'selected' : '' }}>Abierta</option>
                                <option value="opcion_multiple" {{ old('tipo', $pregunta->tipo) == 'opcion_multiple' ? 'selected' : '' }}>Opción Múltiple</option>
                            </select>
                            @error('tipo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="categoria" class="block text-sm font-medium text-gray-700 mb-2">Categoría *</label>
                            <input type="text" 
                                   name="categoria" 
                                   id="categoria"
                                   required
                                   value="{{ old('categoria', $pregunta->categoria) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('categoria') border-red-500 @enderror">
                            @error('categoria')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="especialidad_medica" class="block text-sm font-medium text-gray-700 mb-2">Especialidad Médica *</label>
                            <select name="especialidad_medica" 
                                    id="especialidad_medica"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('especialidad_medica') border-red-500 @enderror">
                                @foreach($especialidades as $especialidad)
                                    <option value="{{ $especialidad }}" {{ old('especialidad_medica', $pregunta->especialidad_medica) == $especialidad ? 'selected' : '' }}>
                                        {{ $especialidad }}
                                    </option>
                                @endforeach
                            </select>
                            @error('especialidad_medica')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fecha_asignacion" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Asignación *</label>
                            <input type="date" 
                                   name="fecha_asignacion" 
                                   id="fecha_asignacion"
                                   required
                                   value="{{ old('fecha_asignacion', $pregunta->fecha_asignacion ? $pregunta->fecha_asignacion->format('Y-m-d') : '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('fecha_asignacion') border-red-500 @enderror">
                            @error('fecha_asignacion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="id_paciente" class="block text-sm font-medium text-gray-700 mb-2">Paciente (Opcional)</label>
                            <select name="id_paciente" 
                                    id="id_paciente"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Todos los pacientes (General)</option>
                                @foreach($pacientes as $paciente)
                                    <option value="{{ $paciente->id_paciente }}" {{ old('id_paciente', $pregunta->id_paciente) == $paciente->id_paciente ? 'selected' : '' }}>
                                        {{ $paciente->usuario->nombre }} {{ $paciente->usuario->apPaterno }} {{ $paciente->usuario->apMaterno }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                            <select name="estado" 
                                    id="estado"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="activa" {{ old('estado', $pregunta->estado) == 'activa' ? 'selected' : '' }}>Activa</option>
                                <option value="inactiva" {{ old('estado', $pregunta->estado) == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                            </select>
                        </div>

                        <div id="opciones-container" class="md:col-span-2 {{ $pregunta->tipo == 'opcion_multiple' ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Opciones de Respuesta *</label>
                            <div id="opciones-list" class="space-y-2">
                                @if($pregunta->tipo == 'opcion_multiple' && $pregunta->opciones)
                                    @foreach($pregunta->opciones as $index => $opcion)
                                        <div class="flex items-center space-x-2">
                                            <input type="text" 
                                                   name="opciones[]" 
                                                   value="{{ $opcion }}"
                                                   placeholder="Opción {{ $index + 1 }}"
                                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                            <button type="button" onclick="removeOpcion(this)" class="text-red-600 hover:text-red-800">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex items-center space-x-2">
                                        <input type="text" 
                                               name="opciones[]" 
                                               placeholder="Opción 1"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <button type="button" onclick="removeOpcion(this)" class="text-red-600 hover:text-red-800">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" onclick="addOpcion()" class="mt-2 px-4 py-2 text-sm text-blue-600 hover:text-blue-800">
                                + Agregar Opción
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index') }}" 
                           class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            Actualizar Pregunta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleOpciones() {
    const tipo = document.getElementById('tipo').value;
    const container = document.getElementById('opciones-container');
    if (tipo === 'opcion_multiple') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}

function addOpcion() {
    const list = document.getElementById('opciones-list');
    const count = list.children.length + 1;
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2';
    div.innerHTML = `
        <input type="text" 
               name="opciones[]" 
               placeholder="Opción ${count}"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
        <button type="button" onclick="removeOpcion(this)" class="text-red-600 hover:text-red-800">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    list.appendChild(div);
}

function removeOpcion(button) {
    if (document.getElementById('opciones-list').children.length > 1) {
        button.parentElement.remove();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleOpciones();
});
</script>
@endsection

