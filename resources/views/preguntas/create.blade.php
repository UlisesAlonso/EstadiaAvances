@extends('layouts.app')

@section('title', 'Crear Pregunta')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Crear Nueva Pregunta o Cuestionario</h2>
                        <p class="text-gray-600">Crea una nueva pregunta o cuestionario clínico para los pacientes</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.store' : 'medico.preguntas.store') }}" class="space-y-6" id="preguntaForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Descripción de la pregunta -->
                        <div class="md:col-span-2">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción de la Pregunta * 
                            </label>
                            <textarea name="descripcion" 
                                      id="descripcion"
                                      rows="4"
                                      placeholder="Escribe la pregunta o descripción del cuestionario..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('descripcion') border-red-500 @enderror"
                                      required>{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo de pregunta -->
                        <div>
                            <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Pregunta * 
                            </label>
                            <select name="tipo" 
                                    id="tipo"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('tipo') border-red-500 @enderror"
                                    required>
                                <option value="">Selecciona el tipo</option>
                                <option value="abierta" {{ old('tipo') == 'abierta' ? 'selected' : '' }}>Abierta</option>
                                <option value="opcion_multiple" {{ old('tipo') == 'opcion_multiple' ? 'selected' : '' }}>Opción Múltiple</option>
                            </select>
                            @error('tipo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Especialidad médica -->
                        <div>
                            <label for="especialidad_medica" class="block text-sm font-medium text-gray-700 mb-2">
                                Especialidad Médica
                            </label>
                            <select name="especialidad_medica" 
                                    id="especialidad_medica"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('especialidad_medica') border-red-500 @enderror">
                                <option value="">Selecciona una especialidad</option>
                                @foreach($especialidades as $esp)
                                    <option value="{{ $esp }}" {{ old('especialidad_medica') == $esp ? 'selected' : '' }}>
                                        {{ $esp }}
                                    </option>
                                @endforeach
                            </select>
                            @error('especialidad_medica')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Opciones múltiples (solo se muestra si el tipo es opción múltiple) -->
                        <div class="md:col-span-2" id="opcionesContainer" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Opciones de Respuesta * (Mínimo 2 opciones)
                            </label>
                            <div id="opcionesList">
                                <div class="flex items-center space-x-2 mb-2">
                                    <input type="text" 
                                           id="opcion_1"
                                           data-name="opciones_multiple[]"
                                           placeholder="Opción 1"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <button type="button" onclick="removeOpcion(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" style="display: none;">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex items-center space-x-2 mb-2">
                                    <input type="text" 
                                           id="opcion_2"
                                           data-name="opciones_multiple[]"
                                           placeholder="Opción 2"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <button type="button" onclick="removeOpcion(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" style="display: none;">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <button type="button" onclick="addOpcion()" class="mt-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Agregar Opción
                            </button>
                            @error('opciones_multiple')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('opciones_multiple.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha de asignación -->
                        <div>
                            <label for="fecha_asignacion" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Asignación * 
                            </label>
                            <input type="date" 
                                   name="fecha_asignacion" 
                                   id="fecha_asignacion"
                                   value="{{ old('fecha_asignacion', date('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('fecha_asignacion') border-red-500 @enderror"
                                   required>
                            @error('fecha_asignacion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Paciente destinatario -->
                        <div>
                            <label for="id_paciente" class="block text-sm font-medium text-gray-700 mb-2">
                                Paciente Destinatario
                            </label>
                            <select name="id_paciente" 
                                    id="id_paciente"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('id_paciente') border-red-500 @enderror">
                                <option value="">Todos los pacientes</option>
                                @foreach($pacientes as $paciente)
                                    <option value="{{ $paciente->id_paciente }}" {{ old('id_paciente') == $paciente->id_paciente ? 'selected' : '' }}>
                                        {{ $paciente->usuario->nombre }} {{ $paciente->usuario->apPaterno }} {{ $paciente->usuario->apMaterno }} - {{ $paciente->usuario->correo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_paciente')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Diagnóstico vinculado -->
                        <div>
                            <label for="id_diagnostico" class="block text-sm font-medium text-gray-700 mb-2">
                                Diagnóstico Vinculado
                            </label>
                            <select name="id_diagnostico" 
                                    id="id_diagnostico"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('id_diagnostico') border-red-500 @enderror">
                                <option value="">Ninguno</option>
                                @foreach($diagnosticos as $diagnostico)
                                    <option value="{{ $diagnostico->id_diagnostico }}" {{ old('id_diagnostico') == $diagnostico->id_diagnostico ? 'selected' : '' }}>
                                        {{ $diagnostico->catalogoDiagnostico->descripcion_clinica ?? 'N/A' }} - {{ $diagnostico->paciente->usuario->nombre ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_diagnostico')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tratamiento vinculado -->
                        <div>
                            <label for="id_tratamiento" class="block text-sm font-medium text-gray-700 mb-2">
                                Tratamiento Vinculado
                            </label>
                            <select name="id_tratamiento" 
                                    id="id_tratamiento"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('id_tratamiento') border-red-500 @enderror">
                                <option value="">Ninguno</option>
                                @foreach($tratamientos as $tratamiento)
                                    <option value="{{ $tratamiento->id_tratamiento }}" {{ old('id_tratamiento') == $tratamiento->id_tratamiento ? 'selected' : '' }}>
                                        {{ $tratamiento->nombre }} - {{ $tratamiento->paciente->usuario->nombre ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_tratamiento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index') }}" 
                           class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Crear Pregunta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Mostrar/ocultar opciones múltiples según el tipo
document.getElementById('tipo').addEventListener('change', function() {
    const opcionesContainer = document.getElementById('opcionesContainer');
    const opcionesInputs = document.querySelectorAll('input[name="opciones_multiple[]"]');
    
    if (this.value === 'opcion_multiple') {
        opcionesContainer.style.display = 'block';
        // Restaurar name y required para opción múltiple
        opcionesInputs.forEach(input => {
            input.setAttribute('name', 'opciones_multiple[]');
            input.setAttribute('required', 'required');
        });
    } else {
        opcionesContainer.style.display = 'none';
        // Eliminar name y required para pregunta abierta
        opcionesInputs.forEach(input => {
            input.removeAttribute('name');
            input.removeAttribute('required');
        });
    }
});

// Inicializar si hay valor en old
const tipoInicial = document.getElementById('tipo').value;
if (tipoInicial === 'opcion_multiple') {
    document.getElementById('opcionesContainer').style.display = 'block';
    // Asegurar que los campos tengan name y required
    const opcionesInputs = document.querySelectorAll('input[data-name="opciones_multiple[]"]');
    opcionesInputs.forEach(input => {
        input.setAttribute('name', 'opciones_multiple[]');
        input.setAttribute('required', 'required');
    });
} else {
    // Si es abierta, eliminar name y required
    const opcionesInputs = document.querySelectorAll('input[data-name="opciones_multiple[]"]');
    opcionesInputs.forEach(input => {
        input.removeAttribute('name');
        input.removeAttribute('required');
    });
}

// Agregar nueva opción
function addOpcion() {
    const opcionesList = document.getElementById('opcionesList');
    const tipo = document.getElementById('tipo').value;
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2 mb-2';
    const count = opcionesList.children.length + 1;
    const nameAttr = tipo === 'opcion_multiple' ? 'name="opciones_multiple[]"' : '';
    const requiredAttr = tipo === 'opcion_multiple' ? 'required' : '';
    div.innerHTML = `
        <input type="text" 
               data-name="opciones_multiple[]"
               ${nameAttr}
               placeholder="Opción ${count}"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
               ${requiredAttr}>
        <button type="button" onclick="removeOpcion(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    opcionesList.appendChild(div);
    updateRemoveButtons();
}

// Eliminar opción
function removeOpcion(button) {
    if (document.getElementById('opcionesList').children.length > 2) {
        button.parentElement.remove();
        updateRemoveButtons();
    }
}

// Actualizar visibilidad de botones eliminar
function updateRemoveButtons() {
    const opciones = document.getElementById('opcionesList').children;
    for (let i = 0; i < opciones.length; i++) {
        const removeBtn = opciones[i].querySelector('button');
        if (opciones.length > 2) {
            removeBtn.style.display = 'block';
        } else {
            removeBtn.style.display = 'none';
        }
    }
}

// Inicializar campos según el tipo al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const tipo = document.getElementById('tipo').value;
    if (tipo === 'abierta') {
        const opcionesInputs = document.querySelectorAll('input[data-name="opciones_multiple[]"]');
        opcionesInputs.forEach(input => {
            input.removeAttribute('name');
            input.removeAttribute('required');
        });
    }
});

// Validación del formulario
document.getElementById('preguntaForm').addEventListener('submit', function(e) {
    const tipo = document.getElementById('tipo').value;
    
    // Si es pregunta abierta, asegurar que no se envíen opciones múltiples
    if (tipo === 'abierta') {
        const opcionesInputs = document.querySelectorAll('input[data-name="opciones_multiple[]"]');
        opcionesInputs.forEach(input => {
            input.removeAttribute('name');
            input.removeAttribute('required');
        });
    } else if (tipo === 'opcion_multiple') {
        // Si es opción múltiple, asegurar que tengan name y required, y validar
        const opcionesInputs = document.querySelectorAll('input[data-name="opciones_multiple[]"]');
        opcionesInputs.forEach(input => {
            input.setAttribute('name', 'opciones_multiple[]');
            input.setAttribute('required', 'required');
        });
        
        const opciones = document.querySelectorAll('input[name="opciones_multiple[]"]');
        const opcionesValidas = Array.from(opciones).filter(op => op.value.trim() !== '');
        if (opcionesValidas.length < 2) {
            e.preventDefault();
            alert('Debe proporcionar al menos 2 opciones para preguntas de opción múltiple.');
            return false;
        }
    }
});
</script>
@endsection

