@extends('layouts.app')

@section('title', 'Editar Observación Médica')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Editar Observación Médica</h2>
                        <p class="text-gray-600">Modifica la observación médica del paciente</p>
                    </div>
                    <a href="{{ route(auth()->user()->isAdmin() ? 'admin.seguimiento.index' : 'medico.seguimiento.index', $observacion->id_paciente) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver
                    </a>
                </div>

                <!-- Información del Paciente -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h3 class="text-sm font-medium text-blue-900 mb-2">Paciente</h3>
                    <p class="text-lg font-semibold text-blue-900">
                        {{ $observacion->paciente->usuario->nombre }} {{ $observacion->paciente->usuario->apPaterno }} {{ $observacion->paciente->usuario->apMaterno }}
                    </p>
                    <p class="text-sm text-blue-700">{{ $observacion->paciente->usuario->correo }}</p>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route(auth()->user()->isAdmin() ? 'admin.seguimiento.observaciones.update' : 'medico.seguimiento.observaciones.update', $observacion->id_observacion) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Observación -->
                    <div>
                        <label for="observacion" class="block text-sm font-medium text-gray-700 mb-2">
                            Observación Médica <span class="text-red-500">*</span>
                        </label>
                        <textarea name="observacion" 
                                  id="observacion" 
                                  rows="6"
                                  required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('observacion') border-red-500 @enderror"
                                  placeholder="Describe la observación médica...">{{ old('observacion', $observacion->observacion) }}</textarea>
                        @error('observacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de Observación -->
                    <div>
                        <label for="fecha_observacion" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Observación <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="fecha_observacion" 
                               id="fecha_observacion"
                               value="{{ old('fecha_observacion', $observacion->fecha_observacion->format('Y-m-d')) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('fecha_observacion') border-red-500 @enderror">
                        @error('fecha_observacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo de Observación -->
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Observación
                        </label>
                        <select name="tipo" 
                                id="tipo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tipo') border-red-500 @enderror">
                            <option value="">Seleccionar tipo (opcional)</option>
                            <option value="general" {{ old('tipo', $observacion->tipo) == 'general' ? 'selected' : '' }}>General</option>
                            <option value="evolucion" {{ old('tipo', $observacion->tipo) == 'evolucion' ? 'selected' : '' }}>Evolución</option>
                            <option value="alerta" {{ old('tipo', $observacion->tipo) == 'alerta' ? 'selected' : '' }}>Alerta</option>
                            <option value="seguimiento" {{ old('tipo', $observacion->tipo) == 'seguimiento' ? 'selected' : '' }}>Seguimiento</option>
                            <option value="recomendacion" {{ old('tipo', $observacion->tipo) == 'recomendacion' ? 'selected' : '' }}>Recomendación</option>
                        </select>
                        @error('tipo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.seguimiento.index' : 'medico.seguimiento.index', $observacion->id_paciente) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Actualizar Observación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

