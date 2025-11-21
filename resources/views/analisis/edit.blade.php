@extends('layouts.app')

@section('title', 'Editar Análisis Clínico')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Editar Análisis Clínico</h2>
                        <p class="text-gray-600">Modifica la información del análisis clínico</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.analisis.show' : 'medico.analisis.show', $analisis->id_analisis) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver Análisis
                        </a>
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
                <form method="POST" action="{{ route(Auth::user()->isAdmin() ? 'admin.analisis.update' : 'medico.analisis.update', $analisis->id_analisis) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tipo o nombre del estudio -->
                        <div class="md:col-span-2">
                            <label for="tipo_estudio" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo o Nombre del Estudio *
                            </label>
                            <input type="text" 
                                   name="tipo_estudio" 
                                   id="tipo_estudio"
                                   value="{{ old('tipo_estudio', $analisis->tipo_estudio) }}"
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
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $analisis->descripcion) }}</textarea>
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
                                   value="{{ old('fecha_analisis', $analisis->fecha_analisis->format('Y-m-d')) }}"
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
                                            {{ old('id_paciente', $analisis->id_paciente) == $paciente->id_paciente ? 'selected' : '' }}>
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
                                            {{ (old('id_medico', $analisis->id_medico) == $medico->id_medico) || (Auth::user()->isMedico() && Auth::user()->medico && Auth::user()->medico->id_medico == $medico->id_medico) ? 'selected' : '' }}>
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
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('valores_obtenidos') border-red-500 @enderror">{{ old('valores_obtenidos', $analisis->valores_obtenidos) }}</textarea>
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
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('observaciones_clinicas') border-red-500 @enderror">{{ old('observaciones_clinicas', $analisis->observaciones_clinicas) }}</textarea>
                            <div class="mt-1 flex justify-between text-sm text-gray-500">
                                <span>Opcional - Máximo 2000 caracteres</span>
                                <span id="char-count-obs">0/2000</span>
                            </div>
                            @error('observaciones_clinicas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                            Actualizar Análisis
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

