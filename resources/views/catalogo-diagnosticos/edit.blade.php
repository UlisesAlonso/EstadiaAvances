@extends('layouts.app')

@section('title', 'Editar Diagnóstico del Catálogo')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Editar Diagnóstico del Catálogo</h2>
                        <p class="text-gray-600">Modifica la información del diagnóstico en el catálogo</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.catalogo-diagnosticos.show' : 'medico.catalogo-diagnosticos.show', $catalogoDiagnostico->id_diagnostico) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Ver Detalles
                        </a>
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.catalogo-diagnosticos.index' : 'medico.catalogo-diagnosticos.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Volver al Catálogo
                        </a>
                    </div>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route(Auth::user()->isAdmin() ? 'admin.catalogo-diagnosticos.update' : 'medico.catalogo-diagnosticos.update', $catalogoDiagnostico->id_diagnostico) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Código -->
                        <div>
                            <label for="codigo" class="block text-sm font-medium text-gray-700 mb-2">
                                Código (Opcional)
                            </label>
                            <input type="text" 
                                   name="codigo" 
                                   id="codigo"
                                   value="{{ old('codigo', $catalogoDiagnostico->codigo) }}"
                                   maxlength="50"
                                   placeholder="Ej: I10, E11, etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('codigo') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Código único del diagnóstico (opcional)</p>
                            @error('codigo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Categoría Médica -->
                        <div>
                            <label for="categoria_medica" class="block text-sm font-medium text-gray-700 mb-2">
                                Categoría Médica *
                            </label>
                            <input type="text" 
                                   name="categoria_medica" 
                                   id="categoria_medica"
                                   value="{{ old('categoria_medica', $catalogoDiagnostico->categoria_medica) }}"
                                   required
                                   maxlength="100"
                                   placeholder="Ej: Cardiovascular, Respiratorio, Neurológico..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('categoria_medica') border-red-500 @enderror">
                            @error('categoria_medica')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción Clínica -->
                        <div class="md:col-span-2">
                            <label for="descripcion_clinica" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción Clínica *
                            </label>
                            <textarea name="descripcion_clinica" 
                                      id="descripcion_clinica"
                                      rows="6"
                                      required
                                      maxlength="1000"
                                      placeholder="Describe detalladamente el diagnóstico médico..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('descripcion_clinica') border-red-500 @enderror">{{ old('descripcion_clinica', $catalogoDiagnostico->descripcion_clinica) }}</textarea>
                            <div class="mt-1 flex justify-between text-sm text-gray-500">
                                <span>Máximo 1000 caracteres</span>
                                <span id="char-count">0/1000</span>
                            </div>
                            @error('descripcion_clinica')
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
                                        <li>Los cambios quedarán registrados con tu información como modificador</li>
                                        <li>El código debe ser único si se proporciona</li>
                                        <li>Los diagnósticos existentes que usen este catálogo no se verán afectados</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.catalogo-diagnosticos.index' : 'medico.catalogo-diagnosticos.index') }}" 
                           class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
    const textarea = document.getElementById('descripcion_clinica');
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


