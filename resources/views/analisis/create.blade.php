@extends('layouts.app')

@section('title', 'Crear Análisis Clínico')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Crear Nuevo Análisis Clínico</h2>
                        <p class="text-gray-600">Registra un nuevo análisis clínico para un paciente</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.analisis.index' : 'medico.analisis.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route(Auth::user()->isAdmin() ? 'admin.analisis.store' : 'medico.analisis.store') }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tipo o nombre del estudio -->
                        <div class="md:col-span-2">
                            <label for="tipo_estudio" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo o Nombre del Estudio *
                            </label>
                            <input type="text" 
                                   name="tipo_estudio" 
                                   id="tipo_estudio"
                                   value="{{ old('tipo_estudio') }}"
                                   placeholder="Ej: Hemograma completo, Química sanguínea, Radiografía de tórax..."
                                   required
                                   maxlength="255"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('tipo_estudio') border-red-500 @enderror">
                            @error('tipo_estudio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción detallada -->
                        <div class="md:col-span-2">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción Detallada *
                            </label>
                            <textarea name="descripcion" 
                                      id="descripcion"
                                      rows="4"
                                      required
                                      maxlength="2000"
                                      placeholder="Describe detalladamente el análisis clínico realizado..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('descripcion') border-red-500 @enderror">{{ old('descripcion') }}</textarea>
                            <div class="mt-1 flex justify-between text-sm text-gray-500">
                                <span>Máximo 2000 caracteres</span>
                                <span id="char-count-desc">0/2000</span>
                            </div>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha del análisis -->
                        <div>
                            <label for="fecha_analisis" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha del Análisis *
                            </label>
                            <input type="date" 
                                   name="fecha_analisis" 
                                   id="fecha_analisis"
                                   value="{{ old('fecha_analisis', date('Y-m-d')) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('fecha_analisis') border-red-500 @enderror">
                            @error('fecha_analisis')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Paciente asociado -->
                        <div>
                            <label for="id_paciente" class="block text-sm font-medium text-gray-700 mb-2">
                                Paciente Asociado *
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

                        <!-- Médico responsable -->
                        <div>
                            <label for="id_medico" class="block text-sm font-medium text-gray-700 mb-2">
                                Médico Responsable *
                            </label>
                            <select name="id_medico" 
                                    id="id_medico"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('id_medico') border-red-500 @enderror"
                                    {{ Auth::user()->isMedico() ? 'disabled' : '' }}>
                                <option value="">Selecciona un médico</option>
                                @foreach($medicos as $medico)
                                    <option value="{{ $medico->id_medico }}" 
                                            {{ (old('id_medico') == $medico->id_medico) || (Auth::user()->isMedico() && Auth::user()->medico && Auth::user()->medico->id_medico == $medico->id_medico) ? 'selected' : '' }}>
                                        {{ $medico->usuario->nombre }} {{ $medico->usuario->apPaterno }} - {{ $medico->especialidad }}
                                    </option>
                                @endforeach
                            </select>
                            @if(Auth::user()->isMedico())
                                <input type="hidden" name="id_medico" value="{{ Auth::user()->medico->id_medico }}">
                            @endif
                            @error('id_medico')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Valores obtenidos (opcional) -->
                        <div class="md:col-span-2">
                            <label for="valores_obtenidos" class="block text-sm font-medium text-gray-700 mb-2">
                                Valores Obtenidos (Resultados Cuantitativos)
                            </label>
                            <textarea name="valores_obtenidos" 
                                      id="valores_obtenidos"
                                      rows="3"
                                      maxlength="2000"
                                      placeholder="Ingresa los valores numéricos obtenidos en el análisis (ej: Glucosa: 95 mg/dL, Hemoglobina: 14.5 g/dL)..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('valores_obtenidos') border-red-500 @enderror">{{ old('valores_obtenidos') }}</textarea>
                            <div class="mt-1 flex justify-between text-sm text-gray-500">
                                <span>Opcional - Máximo 2000 caracteres</span>
                                <span id="char-count-valores">0/2000</span>
                            </div>
                            @error('valores_obtenidos')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Observaciones clínicas (opcional) -->
                        <div class="md:col-span-2">
                            <label for="observaciones_clinicas" class="block text-sm font-medium text-gray-700 mb-2">
                                Observaciones Clínicas
                            </label>
                            <textarea name="observaciones_clinicas" 
                                      id="observaciones_clinicas"
                                      rows="3"
                                      maxlength="2000"
                                      placeholder="Ingresa observaciones clínicas relevantes sobre el análisis..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('observaciones_clinicas') border-red-500 @enderror">{{ old('observaciones_clinicas') }}</textarea>
                            <div class="mt-1 flex justify-between text-sm text-gray-500">
                                <span>Opcional - Máximo 2000 caracteres</span>
                                <span id="char-count-obs">0/2000</span>
                            </div>
                            @error('observaciones_clinicas')
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
                                        <li>El análisis será registrado con tu información como médico responsable</li>
                                        <li>La fecha de creación se registrará automáticamente</li>
                                        <li>Este análisis quedará vinculado al historial clínico del paciente</li>
                                        <li>Los valores obtenidos y observaciones son opcionales pero recomendados para un mejor seguimiento</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.analisis.index' : 'medico.analisis.index') }}" 
                           class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Crear Análisis
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const descripcion = document.getElementById('descripcion');
    const valores = document.getElementById('valores_obtenidos');
    const observaciones = document.getElementById('observaciones_clinicas');
    const charCountDesc = document.getElementById('char-count-desc');
    const charCountValores = document.getElementById('char-count-valores');
    const charCountObs = document.getElementById('char-count-obs');
    
    function updateCharCount(textarea, counter, max) {
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            counter.textContent = length + '/' + max;
            
            if (length > max) {
                counter.classList.add('text-red-600');
                counter.classList.remove('text-gray-500');
            } else {
                counter.classList.remove('text-red-600');
                counter.classList.add('text-gray-500');
            }
        });
        
        // Inicializar contador
        counter.textContent = textarea.value.length + '/' + max;
    }
    
    updateCharCount(descripcion, charCountDesc, 2000);
    updateCharCount(valores, charCountValores, 2000);
    updateCharCount(observaciones, charCountObs, 2000);
});
</script>
@endsection

