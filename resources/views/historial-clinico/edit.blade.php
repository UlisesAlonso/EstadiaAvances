@extends('layouts.app')

@section('title', 'Editar Evento Clínico')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Editar Evento Clínico</h2>
                            <p class="text-gray-600">Modifica la información del evento clínico</p>
                        </div>
                        <a href="{{ route('historial-clinico.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver al Historial
                        </a>
                    </div>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route('historial-clinico.update', $historial->id_historial) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Paciente (solo lectura) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Paciente
                            </label>
                            <input type="text" 
                                   value="{{ $historial->paciente->usuario->nombre }} 
                                          @if($historial->paciente->usuario->apPaterno) {{ $historial->paciente->usuario->apPaterno }} @endif
                                          @if($historial->paciente->usuario->apMaterno) {{ $historial->paciente->usuario->apMaterno }} @endif"
                                   disabled
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                        </div>

                        <!-- Fecha del Evento -->
                        <div>
                            <label for="fecha_evento" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha del Evento <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="fecha_evento" 
                                   id="fecha_evento"
                                   value="{{ old('fecha_evento', $historial->fecha_evento ? $historial->fecha_evento->format('Y-m-d') : $historial->fecha_registro->format('Y-m-d')) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('fecha_evento') border-red-500 @enderror">
                            @error('fecha_evento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Diagnóstico -->
                        <div>
                            <label for="id_diagnostico" class="block text-sm font-medium text-gray-700 mb-2">
                                Diagnóstico
                            </label>
                            <select name="id_diagnostico" 
                                    id="id_diagnostico"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('id_diagnostico') border-red-500 @enderror">
                                <option value="">Selecciona un diagnóstico (opcional)</option>
                                @foreach($diagnosticos as $diagnostico)
                                    <option value="{{ $diagnostico->id_diagnostico }}" 
                                            {{ old('id_diagnostico', $historial->id_diagnostico) == $diagnostico->id_diagnostico ? 'selected' : '' }}>
                                        @if($diagnostico->catalogoDiagnostico)
                                            {{ $diagnostico->catalogoDiagnostico->codigo }} - {{ $diagnostico->catalogoDiagnostico->descripcion_clinica }}
                                        @else
                                            {{ $diagnostico->descripcion }}
                                        @endif
                                        ({{ $diagnostico->paciente->usuario->nombre }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_diagnostico')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tratamiento -->
                        <div>
                            <label for="id_tratamiento" class="block text-sm font-medium text-gray-700 mb-2">
                                Tratamiento
                            </label>
                            <select name="id_tratamiento" 
                                    id="id_tratamiento"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('id_tratamiento') border-red-500 @enderror">
                                <option value="">Selecciona un tratamiento (opcional)</option>
                                @foreach($tratamientos as $tratamiento)
                                    <option value="{{ $tratamiento->id_tratamiento }}" 
                                            {{ old('id_tratamiento', $historial->id_tratamiento) == $tratamiento->id_tratamiento ? 'selected' : '' }}>
                                        {{ $tratamiento->nombre }} - {{ $tratamiento->paciente->usuario->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_tratamiento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Observaciones Médicas -->
                        <div class="md:col-span-2">
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                                Observaciones Médicas <span class="text-red-500">*</span>
                            </label>
                            <textarea name="observaciones" 
                                      id="observaciones"
                                      rows="5"
                                      required
                                      placeholder="Ingrese las observaciones médicas del evento clínico..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('observaciones') border-red-500 @enderror">{{ old('observaciones', $historial->observaciones) }}</textarea>
                            @error('observaciones')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Máximo 5000 caracteres</p>
                        </div>

                        <!-- Resultados de Análisis -->
                        <div class="md:col-span-2">
                            <label for="resultados_analisis" class="block text-sm font-medium text-gray-700 mb-2">
                                Resultados de Análisis o Estudios Médicos
                            </label>
                            <textarea name="resultados_analisis" 
                                      id="resultados_analisis"
                                      rows="5"
                                      placeholder="Ingrese los resultados de análisis o estudios médicos realizados..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('resultados_analisis') border-red-500 @enderror">{{ old('resultados_analisis', $historial->resultados_analisis) }}</textarea>
                            @error('resultados_analisis')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Máximo 5000 caracteres</p>
                        </div>

                        <!-- Archivos Existentes -->
                        @if($historial->archivos_adjuntos && count($historial->archivos_adjuntos) > 0)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Archivos Adjuntos Existentes
                                </label>
                                <div class="space-y-2">
                                    @foreach($historial->archivos_adjuntos as $archivo)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                                            <div class="flex items-center">
                                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-sm text-gray-700">{{ basename($archivo) }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ asset('storage/' . $archivo) }}" 
                                                   target="_blank"
                                                   class="text-blue-600 hover:text-blue-800">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="checkbox" 
                                                           name="archivos_eliminar[]" 
                                                           value="{{ $archivo }}"
                                                           class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                    <span class="ml-2 text-sm text-red-600">Eliminar</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Nuevos Archivos Adjuntos -->
                        <div class="md:col-span-2">
                            <label for="archivos_adjuntos" class="block text-sm font-medium text-gray-700 mb-2">
                                Agregar Nuevos Archivos Adjuntos
                            </label>
                            <input type="file" 
                                   name="archivos_adjuntos[]" 
                                   id="archivos_adjuntos"
                                   multiple
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('archivos_adjuntos.*') border-red-500 @enderror">
                            @error('archivos_adjuntos.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Formatos permitidos: PDF, Word (DOC, DOCX), Imágenes (JPG, PNG). Máximo 10MB por archivo.
                            </p>
                            <div id="archivos-preview" class="mt-2 space-y-2"></div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('historial-clinico.index') }}" 
                           class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Actualizar Evento Clínico
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview de archivos seleccionados
    document.getElementById('archivos_adjuntos').addEventListener('change', function(e) {
        const preview = document.getElementById('archivos-preview');
        preview.innerHTML = '';
        
        if (this.files.length > 0) {
            const lista = document.createElement('ul');
            lista.className = 'list-disc list-inside text-sm text-gray-600';
            
            Array.from(this.files).forEach((archivo, indice) => {
                const item = document.createElement('li');
                item.textContent = `${indice + 1}. ${archivo.name} (${(archivo.size / 1024 / 1024).toFixed(2)} MB)`;
                lista.appendChild(item);
            });
            
            preview.appendChild(lista);
        }
    });
</script>
@endsection

