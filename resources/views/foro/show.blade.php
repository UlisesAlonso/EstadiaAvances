@extends('layouts.app')

@section('title', $publicacion->titulo)

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <a href="{{ route('foro.index') }}" 
                           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-2">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver al Foro
                        </a>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $publicacion->titulo }}</h2>
                    </div>
                    @if(auth()->user()->isPaciente() && $publicacion->puedeEditar(auth()->user()->paciente->id_paciente))
                        <div class="flex space-x-2">
                            <a href="{{ route('paciente.foro.edit', $publicacion->id_publicacion) }}" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar
                            </a>
                            <form method="POST" action="{{ route('paciente.foro.destroy', $publicacion->id_publicacion) }}" 
                                  onsubmit="return confirm('¿Estás seguro de eliminar esta publicación?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Información de la publicación -->
                <div class="mb-6 bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="font-medium">{{ $publicacion->paciente->usuario->nombre }} {{ $publicacion->paciente->usuario->apPaterno }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $publicacion->fecha_publicacion->format('d/m/Y H:i') }}
                            </div>
                            @if($publicacion->estado == 'pendiente')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pendiente de aprobación
                                </span>
                            @elseif($publicacion->estado == 'oculta')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Ocultada
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($publicacion->actividad)
                        <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded">
                            <div class="flex items-center text-sm text-blue-800">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <strong>Actividad relacionada:</strong> {{ $publicacion->actividad->nombre }}
                            </div>
                        </div>
                    @endif

                    @if($publicacion->tratamiento)
                        <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded">
                            <div class="flex items-center text-sm text-green-800">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                                <strong>Tratamiento relacionado:</strong> {{ $publicacion->tratamiento->nombre }}
                            </div>
                        </div>
                    @endif

                    @if($publicacion->etiquetas)
                        <div class="mb-3">
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $publicacion->etiquetas) as $tag)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ trim($tag) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Contenido de la publicación -->
                <div class="mb-6 prose max-w-none">
                    <div class="text-gray-700 whitespace-pre-wrap">{{ $publicacion->contenido }}</div>
                </div>

                <!-- Acciones (Reacciones y Favoritos) -->
                @if(auth()->user()->isPaciente() && $publicacion->estado == 'aprobada')
                <div class="mb-6 flex items-center justify-between border-t border-b border-gray-200 py-4">
                    <div class="flex items-center space-x-4">
                        <!-- Botón de reacción -->
                        <form method="POST" action="{{ route('paciente.foro.reaccion.toggle', $publicacion->id_publicacion) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md {{ $usuarioReacciono ? 'bg-red-50 text-red-700 border-red-300' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                <svg class="h-5 w-5 mr-2 {{ $usuarioReacciono ? 'text-red-500 fill-current' : 'text-gray-400' }}" fill="{{ $usuarioReacciono ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                {{ $publicacion->reacciones_count ?? 0 }} Me gusta
                            </button>
                        </form>

                        <!-- Botón de favorito -->
                        <form method="POST" action="{{ route('paciente.foro.favorito.toggle', $publicacion->id_publicacion) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md {{ $esFavorito ? 'bg-yellow-50 text-yellow-700 border-yellow-300' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                <svg class="h-5 w-5 mr-2 {{ $esFavorito ? 'text-yellow-500 fill-current' : 'text-gray-400' }}" fill="{{ $esFavorito ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                {{ $esFavorito ? 'En favoritos' : 'Agregar a favoritos' }}
                            </button>
                        </form>
                    </div>
                    <div class="text-sm text-gray-600">
                        <svg class="h-5 w-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        {{ $publicacion->comentarios_count ?? 0 }} Comentarios
                    </div>
                </div>
                @endif

                <!-- Mensajes -->
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Comentarios -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Comentarios ({{ $publicacion->comentarios_count ?? 0 }})</h3>

                    @if(auth()->user()->isPaciente() && $publicacion->estado == 'aprobada')
                        <!-- Formulario para agregar comentario -->
                        <div class="mb-6 bg-gray-50 rounded-lg p-4">
                            <form method="POST" action="{{ route('paciente.foro.comentarios.store', $publicacion->id_publicacion) }}">
                                @csrf
                                <div>
                                    <label for="contenido" class="block text-sm font-medium text-gray-700 mb-2">Agregar comentario</label>
                                    <textarea name="contenido" 
                                              id="contenido"
                                              rows="3"
                                              required
                                              placeholder="Escribe tu comentario..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    @error('contenido')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mt-3">
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        Comentar
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Lista de comentarios -->
                    @if($publicacion->comentarios->count() > 0)
                        <div class="space-y-4">
                            @foreach($publicacion->comentarios as $comentario)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center space-x-2">
                                            <div class="flex items-center text-sm font-medium text-gray-900">
                                                {{ $comentario->paciente->usuario->nombre }} {{ $comentario->paciente->usuario->apPaterno }}
                                            </div>
                                            <span class="text-xs text-gray-500">
                                                {{ $comentario->fecha_comentario->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                        @if(auth()->user()->isPaciente() && $comentario->puedeEditar(auth()->user()->paciente->id_paciente))
                                            <div class="flex space-x-2">
                                                <form method="POST" action="{{ route('paciente.foro.comentarios.destroy', [$publicacion->id_publicacion, $comentario->id_comentario]) }}" 
                                                      onsubmit="return confirm('¿Eliminar este comentario?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-800 text-sm">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="text-gray-700 whitespace-pre-wrap">{{ $comentario->contenido }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>No hay comentarios aún. Sé el primero en comentar.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

