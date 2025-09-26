@extends('layouts.app')

@section('title', 'Editar Actividad')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Editar Actividad</h2>
                        <p class="text-gray-600">Modifica la información de la actividad clínica</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.actividades.show' : 'medico.actividades.show', $actividad->id_actividad) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver Detalles
                        </a>
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.actividades.index' : 'medico.actividades.index') }}" 
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
                                <p>Puedes modificar: <strong>instrucciones, fechas, periodicidad y estado</strong>.</p>
                                <p>El paciente y médico asignador no pueden ser cambiados una vez creada la actividad.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route(Auth::user()->isAdmin() ? 'admin.actividades.update' : 'medico.actividades.update', $actividad->id_actividad) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre de la actividad -->
                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de la Actividad *
                            </label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre"
                                   value="{{ old('nombre', $actividad->nombre) }}"
                                   placeholder="Ej: Ejercicios de respiración, Control de peso, etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('nombre') border-red-500 @enderror"
                                   required>
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Paciente (solo lectura) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Paciente</label>
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
                                {{ $actividad->paciente->usuario->nombre }} {{ $actividad->paciente->usuario->apPaterno }} {{ $actividad->paciente->usuario->apMaterno }}
                                - {{ $actividad->paciente->usuario->correo }}
                            </div>
                            <p class="mt-1 text-sm text-gray-500">El paciente no puede ser cambiado una vez creada la actividad</p>
                            <!-- Campo oculto para mantener el id_paciente -->
                            <input type="hidden" name="id_paciente" value="{{ $actividad->id_paciente }}">
                        </div>

                        <!-- Descripción -->
                        <div class="md:col-span-2">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción *
                            </label>
                            <textarea name="descripcion" 
                                      id="descripcion"
                                      rows="3"
                                      placeholder="Describe detalladamente la actividad clínica..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('descripcion') border-red-500 @enderror"
                                      required>{{ old('descripcion', $actividad->descripcion) }}</textarea>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Instrucciones -->
                        <div class="md:col-span-2">
                            <label for="instrucciones" class="block text-sm font-medium text-gray-700 mb-2">
                                Instrucciones Específicas
                            </label>
                            <textarea name="instrucciones" 
                                      id="instrucciones"
                                      rows="4"
                                      placeholder="Proporciona instrucciones detalladas sobre cómo realizar la actividad..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('instrucciones') border-red-500 @enderror">{{ old('instrucciones', $actividad->instrucciones) }}</textarea>
                            @error('instrucciones')
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
                                   value="{{ old('fecha_asignacion', $actividad->fecha_asignacion->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('fecha_asignacion') border-red-500 @enderror"
                                   required>
                            @error('fecha_asignacion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha límite -->
                        <div>
                            <label for="fecha_limite" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha Límite *
                            </label>
                            <input type="date" 
                                   name="fecha_limite" 
                                   id="fecha_limite"
                                   value="{{ old('fecha_limite', $actividad->fecha_limite->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('fecha_limite') border-red-500 @enderror"
                                   required>
                            @error('fecha_limite')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Periodicidad -->
                        <div>
                            <label for="periodicidad" class="block text-sm font-medium text-gray-700 mb-2">
                                Periodicidad *
                            </label>
                            <select name="periodicidad" 
                                    id="periodicidad"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('periodicidad') border-red-500 @enderror"
                                    required>
                                <option value="">Selecciona la periodicidad</option>
                                <option value="Diaria" {{ old('periodicidad', $actividad->periodicidad) == 'Diaria' ? 'selected' : '' }}>Diaria</option>
                                <option value="Cada 2 días" {{ old('periodicidad', $actividad->periodicidad) == 'Cada 2 días' ? 'selected' : '' }}>Cada 2 días</option>
                                <option value="Cada 3 días" {{ old('periodicidad', $actividad->periodicidad) == 'Cada 3 días' ? 'selected' : '' }}>Cada 3 días</option>
                                <option value="Semanal" {{ old('periodicidad', $actividad->periodicidad) == 'Semanal' ? 'selected' : '' }}>Semanal</option>
                                <option value="Quincenal" {{ old('periodicidad', $actividad->periodicidad) == 'Quincenal' ? 'selected' : '' }}>Quincenal</option>
                                <option value="Mensual" {{ old('periodicidad', $actividad->periodicidad) == 'Mensual' ? 'selected' : '' }}>Mensual</option>
                                <option value="Una sola vez" {{ old('periodicidad', $actividad->periodicidad) == 'Una sola vez' ? 'selected' : '' }}>Una sola vez</option>
                            </select>
                            @error('periodicidad')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estado de completada -->
                        <div>
                            <label for="completada" class="block text-sm font-medium text-gray-700 mb-2">
                                Estado de la Actividad
                            </label>
                            <select name="completada" 
                                    id="completada"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('completada') border-red-500 @enderror">
                                <option value="0" {{ old('completada', $actividad->completada) == '0' ? 'selected' : '' }}>Pendiente</option>
                                <option value="1" {{ old('completada', $actividad->completada) == '1' ? 'selected' : '' }}>Completada</option>
                            </select>
                            @error('completada')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.actividades.show' : 'medico.actividades.show', $actividad->id_actividad) }}" 
                           class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validación de fechas
document.getElementById('fecha_asignacion').addEventListener('change', function() {
    const fechaAsignacion = new Date(this.value);
    const fechaLimite = document.getElementById('fecha_limite');
    
    if (fechaAsignacion) {
        fechaLimite.min = this.value;
        if (fechaLimite.value && new Date(fechaLimite.value) < fechaAsignacion) {
            fechaLimite.value = this.value;
        }
    }
});
</script>
@endsection
