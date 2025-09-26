@extends('layouts.app')

@section('title', 'Crear Actividad')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Crear Nueva Actividad</h2>
                        <p class="text-gray-600">Asigna una nueva actividad clínica a un paciente</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.actividades.index' : 'medico.actividades.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route(Auth::user()->isAdmin() ? 'admin.actividades.store' : 'medico.actividades.store') }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre de la actividad -->
                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de la Actividad *
                            </label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre"
                                   value="{{ old('nombre') }}"
                                   placeholder="Ej: Ejercicios de respiración, Control de peso, etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('nombre') border-red-500 @enderror"
                                   required>
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Paciente -->
                        <div class="md:col-span-2">
                            <label for="id_paciente" class="block text-sm font-medium text-gray-700 mb-2">
                                Paciente *
                            </label>
                            <select name="id_paciente" 
                                    id="id_paciente"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('id_paciente') border-red-500 @enderror"
                                    required>
                                <option value="">Selecciona un paciente</option>
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
                                      required>{{ old('descripcion') }}</textarea>
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
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('instrucciones') border-red-500 @enderror">{{ old('instrucciones') }}</textarea>
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
                                   value="{{ old('fecha_asignacion', date('Y-m-d')) }}"
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
                                   value="{{ old('fecha_limite') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('fecha_limite') border-red-500 @enderror"
                                   required>
                            @error('fecha_limite')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Periodicidad -->
                        <div class="md:col-span-2">
                            <label for="periodicidad" class="block text-sm font-medium text-gray-700 mb-2">
                                Periodicidad *
                            </label>
                            <select name="periodicidad" 
                                    id="periodicidad"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('periodicidad') border-red-500 @enderror"
                                    required>
                                <option value="">Selecciona la periodicidad</option>
                                <option value="Diaria" {{ old('periodicidad') == 'Diaria' ? 'selected' : '' }}>Diaria</option>
                                <option value="Cada 2 días" {{ old('periodicidad') == 'Cada 2 días' ? 'selected' : '' }}>Cada 2 días</option>
                                <option value="Cada 3 días" {{ old('periodicidad') == 'Cada 3 días' ? 'selected' : '' }}>Cada 3 días</option>
                                <option value="Semanal" {{ old('periodicidad') == 'Semanal' ? 'selected' : '' }}>Semanal</option>
                                <option value="Quincenal" {{ old('periodicidad') == 'Quincenal' ? 'selected' : '' }}>Quincenal</option>
                                <option value="Mensual" {{ old('periodicidad') == 'Mensual' ? 'selected' : '' }}>Mensual</option>
                                <option value="Una sola vez" {{ old('periodicidad') == 'Una sola vez' ? 'selected' : '' }}>Una sola vez</option>
                            </select>
                            @error('periodicidad')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.actividades.index' : 'medico.actividades.index') }}" 
                           class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Crear Actividad
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
