@extends('layouts.app')

@section('title', 'Editar Diagnóstico')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Editar Diagnóstico</h2>
                        <p class="text-gray-600">Modifica la información del diagnóstico médico</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('medico.diagnosticos.show', $diagnostico->id_diagnostico) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver Diagnóstico
                        </a>
                        <a href="{{ route('medico.diagnosticos.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver a Diagnósticos
                        </a>
                    </div>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route('medico.diagnosticos.update', $diagnostico->id_diagnostico) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Paciente (solo lectura) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Paciente</label>
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
                                {{ $diagnostico->paciente->usuario->nombre }} {{ $diagnostico->paciente->usuario->apPaterno }} {{ $diagnostico->paciente->usuario->apMaterno }}
                                - {{ $diagnostico->paciente->usuario->correo }}
                            </div>
                            <p class="mt-1 text-sm text-gray-500">El paciente no puede ser cambiado una vez creado el diagnóstico</p>
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha del Diagnóstico *
                            </label>
                            <input type="date" 
                                   name="fecha" 
                                   id="fecha"
                                   value="{{ old('fecha', $diagnostico->fecha->format('Y-m-d')) }}"
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
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $diagnostico->descripcion) }}</textarea>
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
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    Información sobre la edición
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Puedes modificar la fecha y descripción del diagnóstico</li>
                                        <li>El paciente y médico responsable no pueden ser cambiados</li>
                                        <li>Los cambios quedarán registrados en el historial del sistema</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('medico.diagnosticos.show', $diagnostico->id_diagnostico) }}" 
                           class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Actualizar Diagnóstico
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
