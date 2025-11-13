@extends('layouts.app')

@section('title', 'Editar Tratamiento')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Modificar Tratamiento</h2>
                        <p class="text-gray-600">Ajusta la dosis, duración, estado e indicaciones clínicas del tratamiento</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('medico.tratamientos.show', $tratamiento->id_tratamiento) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver Detalles
                        </a>
                        <a href="{{ route('medico.tratamientos.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>

                <!-- Información sobre modificaciones -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Información sobre modificaciones</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Puedes modificar: <strong>dosis, frecuencia, duración, estado del tratamiento y observaciones clínicas</strong>.</p>
                                <p>El paciente y diagnóstico no pueden ser cambiados una vez creado el tratamiento.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route('medico.tratamientos.update', $tratamiento->id_tratamiento) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Paciente (solo lectura) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Paciente</label>
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
                                {{ $tratamiento->paciente->usuario->nombre }} {{ $tratamiento->paciente->usuario->apPaterno }} {{ $tratamiento->paciente->usuario->apMaterno }}
                                - {{ $tratamiento->paciente->usuario->correo }}
                            </div>
                            <p class="mt-1 text-sm text-gray-500">El paciente no puede ser cambiado una vez creado el tratamiento</p>
                        </div>

                        <!-- Diagnóstico del Catálogo (solo lectura) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Diagnóstico del Catálogo</label>
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
                                @if($tratamiento->diagnostico && $tratamiento->diagnostico->catalogoDiagnostico)
                                    <div>
                                        <span class="font-medium">
                                            {{ $tratamiento->diagnostico->catalogoDiagnostico->codigo ? $tratamiento->diagnostico->catalogoDiagnostico->codigo . ' - ' : '' }}
                                            {{ $tratamiento->diagnostico->catalogoDiagnostico->descripcion_clinica }}
                                        </span>
                                        <div class="mt-1">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $tratamiento->diagnostico->catalogoDiagnostico->categoria_medica }}
                                            </span>
                                            <span class="ml-2 text-sm text-gray-500">
                                                - {{ $tratamiento->diagnostico->fecha->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                @elseif($tratamiento->diagnostico)
                                    {{ $tratamiento->diagnostico->descripcion }} - {{ $tratamiento->diagnostico->fecha->format('d/m/Y') }}
                                @else
                                    Sin diagnóstico relacionado
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-gray-500">El diagnóstico no puede ser cambiado una vez creado el tratamiento</p>
                        </div>

                        <!-- Nombre del Tratamiento -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre del Tratamiento <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre"
                                   value="{{ old('nombre', $tratamiento->nombre) }}"
                                   required
                                   placeholder="Ej: Metformina, Fisioterapia, etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('nombre') border-red-500 @enderror">
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dosis -->
                        <div>
                            <label for="dosis" class="block text-sm font-medium text-gray-700 mb-2">
                                Dosis/Concentración <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="dosis" 
                                   id="dosis"
                                   value="{{ old('dosis', $tratamiento->dosis) }}"
                                   required
                                   placeholder="Ej: 500mg, 10ml, etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('dosis') border-red-500 @enderror">
                            @error('dosis')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Frecuencia -->
                        <div>
                            <label for="frecuencia" class="block text-sm font-medium text-gray-700 mb-2">
                                Frecuencia <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="frecuencia" 
                                   id="frecuencia"
                                   value="{{ old('frecuencia', $tratamiento->frecuencia) }}"
                                   required
                                   placeholder="Ej: Cada 8 horas, 2 veces al día, etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('frecuencia') border-red-500 @enderror">
                            @error('frecuencia')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duración -->
                        <div>
                            <label for="duracion" class="block text-sm font-medium text-gray-700 mb-2">
                                Duración <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="duracion" 
                                   id="duracion"
                                   value="{{ old('duracion', $tratamiento->duracion) }}"
                                   required
                                   placeholder="Ej: 30 días, 2 semanas, etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('duracion') border-red-500 @enderror">
                            @error('duracion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha de Inicio -->
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Inicio <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="fecha_inicio" 
                                   id="fecha_inicio"
                                   value="{{ old('fecha_inicio', $tratamiento->fecha_inicio->format('Y-m-d')) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('fecha_inicio') border-red-500 @enderror">
                            @error('fecha_inicio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div>
                            <label for="activo" class="block text-sm font-medium text-gray-700 mb-2">
                                Estado del Tratamiento
                            </label>
                            <select name="activo" 
                                    id="activo"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('activo') border-red-500 @enderror">
                                <option value="1" {{ old('activo', $tratamiento->activo) == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ old('activo', $tratamiento->activo) == 0 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('activo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Observaciones -->
                        <div class="md:col-span-2">
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                                Observaciones Clínicas y Evolución del Paciente
                            </label>
                            <textarea name="observaciones" 
                                      id="observaciones"
                                      rows="4"
                                      placeholder="Registra la evolución del paciente, efectos secundarios observados, ajustes necesarios, precauciones, etc."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('observaciones') border-red-500 @enderror">{{ old('observaciones', $tratamiento->observaciones) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Documenta la evolución del paciente y cualquier ajuste necesario en el tratamiento</p>
                            @error('observaciones')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('medico.tratamientos.show', $tratamiento->id_tratamiento) }}" 
                           class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Modificaciones
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
