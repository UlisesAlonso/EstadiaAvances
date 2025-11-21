@extends('layouts.app')

@section('title', 'Crear Nuevo Evento Clínico')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Crear Nuevo Evento Clínico</h2>
                            <p class="text-gray-600">Registra un nuevo evento en el historial clínico de un paciente</p>
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
                <form method="POST" action="{{ route('historial-clinico.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Paciente -->
                        <div class="md:col-span-2">
                            <label for="id_paciente" class="block text-sm font-medium text-gray-700 mb-2">
                                Paciente <span class="text-red-500">*</span>
                            </label>
                            <select name="id_paciente" 
                                    id="id_paciente"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('id_paciente') border-red-500 @enderror">
                                <option value="">Selecciona un paciente</option>
                                @foreach($pacientes as $paciente)
                                    <option value="{{ $paciente->id_paciente }}" 
                                            {{ old('id_paciente') == $paciente->id_paciente ? 'selected' : '' }}>
                                        {{ $paciente->usuario->nombre }} 
                                        @if($paciente->usuario->apPaterno) {{ $paciente->usuario->apPaterno }} @endif
                                        @if($paciente->usuario->apMaterno) {{ $paciente->usuario->apMaterno }} @endif
                                        - {{ $paciente->usuario->correo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_paciente')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha del Evento -->
                        <div>
                            <label for="fecha_evento" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha del Evento <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="fecha_evento" 
                                   id="fecha_evento"
                                   value="{{ old('fecha_evento', date('Y-m-d')) }}"
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
                                            {{ old('id_diagnostico') == $diagnostico->id_diagnostico ? 'selected' : '' }}>
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
                                            {{ old('id_tratamiento') == $tratamiento->id_tratamiento ? 'selected' : '' }}>
                                        {{ $tratamiento->nombre }} - {{ $tratamiento->paciente->usuario->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_tratamiento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Análisis -->
                        <div class="md:col-span-2">
                            <label for="id_analisis" class="block text-sm font-medium text-gray-700 mb-2">
                                Análisis o Estudio Médico
                            </label>
                            <select name="id_analisis" 
                                    id="id_analisis"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('id_analisis') border-red-500 @enderror">
                                <option value="">Selecciona un análisis (opcional)</option>
                                @foreach($analisis as $analisisItem)
                                    <option value="{{ $analisisItem->id_analisis }}" 
                                            {{ old('id_analisis') == $analisisItem->id_analisis ? 'selected' : '' }}>
                                        {{ $analisisItem->tipo_estudio }} - {{ $analisisItem->paciente->usuario->nombre }} 
                                        ({{ $analisisItem->fecha_analisis ? $analisisItem->fecha_analisis->format('d/m/Y') : 'Sin fecha' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_analisis')
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
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('observaciones') border-red-500 @enderror">{{ old('observaciones') }}</textarea>
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
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('resultados_analisis') border-red-500 @enderror">{{ old('resultados_analisis') }}</textarea>
                            @error('resultados_analisis')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Máximo 5000 caracteres</p>
                        </div>

                        <!-- Archivos Adjuntos -->
                        <div class="md:col-span-2">
                            <label for="archivos_adjuntos" class="block text-sm font-medium text-gray-700 mb-2">
                                Archivos Adjuntos
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

                    <!-- Sección de Antecedentes Médicos (solo para primer historial) -->
                    <div id="antecedentes-section" class="hidden mt-8 pt-8 border-t-2 border-blue-200">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-blue-900">Antecedentes Médicos</h3>
                            </div>
                            <p class="mt-2 text-sm text-blue-700">
                                Este es el primer historial clínico de este paciente. Por favor, complete los antecedentes médicos para tener un registro completo.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Alergias -->
                            <div class="md:col-span-2">
                                <label for="alergias" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alergias
                                </label>
                                <textarea name="alergias" 
                                          id="alergias"
                                          rows="3"
                                          placeholder="Especifique las alergias conocidas del paciente (medicamentos, alimentos, etc.)..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('alergias') border-red-500 @enderror">{{ old('alergias') }}</textarea>
                                @error('alergias')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Enfermedades Familiares -->
                            <div class="md:col-span-2">
                                <label for="enfermedades_familiares" class="block text-sm font-medium text-gray-700 mb-2">
                                    Enfermedades Familiares Crónicas
                                </label>
                                <textarea name="enfermedades_familiares" 
                                          id="enfermedades_familiares"
                                          rows="3"
                                          placeholder="Indique si hay antecedentes familiares de enfermedades crónicas (diabetes, hipertensión, cardiopatías, etc.)..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('enfermedades_familiares') border-red-500 @enderror">{{ old('enfermedades_familiares') }}</textarea>
                                @error('enfermedades_familiares')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Cirugías Previas -->
                            <div class="md:col-span-2">
                                <label for="cirugias_previas" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cirugías Previas
                                </label>
                                <textarea name="cirugias_previas" 
                                          id="cirugias_previas"
                                          rows="3"
                                          placeholder="Indique las cirugías previas del paciente con fecha aproximada..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('cirugias_previas') border-red-500 @enderror">{{ old('cirugias_previas') }}</textarea>
                                @error('cirugias_previas')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Consumo de Tabaco -->
                            <div>
                                <label for="consumo_tabaco" class="block text-sm font-medium text-gray-700 mb-2">
                                    Consumo de Tabaco
                                </label>
                                <select name="consumo_tabaco" 
                                        id="consumo_tabaco"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('consumo_tabaco') border-red-500 @enderror">
                                    <option value="">Seleccione una opción</option>
                                    <option value="no" {{ old('consumo_tabaco') == 'no' ? 'selected' : '' }}>No</option>
                                    <option value="si" {{ old('consumo_tabaco') == 'si' ? 'selected' : '' }}>Sí</option>
                                    <option value="ex_fumador" {{ old('consumo_tabaco') == 'ex_fumador' ? 'selected' : '' }}>Ex fumador</option>
                                </select>
                                @error('consumo_tabaco')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Consumo de Alcohol -->
                            <div>
                                <label for="consumo_alcohol" class="block text-sm font-medium text-gray-700 mb-2">
                                    Consumo de Alcohol
                                </label>
                                <select name="consumo_alcohol" 
                                        id="consumo_alcohol"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('consumo_alcohol') border-red-500 @enderror">
                                    <option value="">Seleccione una opción</option>
                                    <option value="no" {{ old('consumo_alcohol') == 'no' ? 'selected' : '' }}>No</option>
                                    <option value="ocasional" {{ old('consumo_alcohol') == 'ocasional' ? 'selected' : '' }}>Ocasional</option>
                                    <option value="si" {{ old('consumo_alcohol') == 'si' ? 'selected' : '' }}>Sí</option>
                                </select>
                                @error('consumo_alcohol')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Realiza Ejercicio -->
                            <div>
                                <label for="realiza_ejercicio" class="block text-sm font-medium text-gray-700 mb-2">
                                    Realiza Ejercicio
                                </label>
                                <select name="realiza_ejercicio" 
                                        id="realiza_ejercicio"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('realiza_ejercicio') border-red-500 @enderror">
                                    <option value="">Seleccione una opción</option>
                                    <option value="si" {{ old('realiza_ejercicio') == 'si' ? 'selected' : '' }}>Sí</option>
                                    <option value="ocasional" {{ old('realiza_ejercicio') == 'ocasional' ? 'selected' : '' }}>Ocasional</option>
                                    <option value="no" {{ old('realiza_ejercicio') == 'no' ? 'selected' : '' }}>No</option>
                                </select>
                                @error('realiza_ejercicio')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tipo de Alimentación -->
                            <div>
                                <label for="tipo_alimentacion" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Alimentación
                                </label>
                                <textarea name="tipo_alimentacion" 
                                          id="tipo_alimentacion"
                                          rows="3"
                                          placeholder="Describa el tipo de alimentación del paciente (dieta balanceada, vegetariana, restricciones, etc.)..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tipo_alimentacion') border-red-500 @enderror">{{ old('tipo_alimentacion') }}</textarea>
                                @error('tipo_alimentacion')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Observaciones de Antecedentes -->
                            <div class="md:col-span-2">
                                <label for="observaciones_antecedentes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Observaciones Adicionales sobre Antecedentes
                                </label>
                                <textarea name="observaciones_antecedentes" 
                                          id="observaciones_antecedentes"
                                          rows="3"
                                          placeholder="Cualquier otra información relevante sobre los antecedentes del paciente..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('observaciones_antecedentes') border-red-500 @enderror">{{ old('observaciones_antecedentes') }}</textarea>
                                @error('observaciones_antecedentes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
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
                            Guardar Evento Clínico
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables globales
    const esPrimerHistorial = @json($esPrimerHistorial ?? false);
    const idPacienteSeleccionado = @json($idPacienteSeleccionado ?? null);
    const antecedentesSection = document.getElementById('antecedentes-section');

    // Mostrar/ocultar sección de antecedentes si es el primer historial
    if (esPrimerHistorial && idPacienteSeleccionado) {
        antecedentesSection.classList.remove('hidden');
    }

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

    // Verificar si es primer historial cuando se selecciona un paciente
    document.getElementById('id_paciente').addEventListener('change', function() {
        const pacienteId = this.value;
        
        if (pacienteId) {
            // Hacer petición AJAX para verificar si es el primer historial
            fetch(`{{ route('historial-clinico.create') }}?id_paciente=${pacienteId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.es_primer_historial) {
                    antecedentesSection.classList.remove('hidden');
                } else {
                    antecedentesSection.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error al verificar historial:', error);
                // Si hay error, ocultar la sección por seguridad
                antecedentesSection.classList.add('hidden');
            });
        } else {
            antecedentesSection.classList.add('hidden');
        }
    });
</script>
@endsection


