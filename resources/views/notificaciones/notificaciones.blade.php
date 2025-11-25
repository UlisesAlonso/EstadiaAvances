@extends('layouts.app')

@section('title', 'Mis Actividades')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">

                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Mis mensajes</h2>
                        <p class="text-gray-600">Mensajes recibidos en tiempo real</p>
                    </div>
                    <div class="flex space-x-3">
                        @if(auth()->user()->isPaciente())
                            <a href="{{ route('paciente.dashboard') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Volver al Dashboard
                            </a>
                        @elseif(auth()->user()->isMedico())
                            <a href="{{ route('medico.dashboard') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Volver al Dashboard
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Estadísticas del paciente -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-600">Recibidas</p>
                                <p class="text-2xl font-bold text-yellow-900" id="stats-recibidas">{{ $stats['recibidas'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla dinámica -->
                <div id="tablaNotificaciones">
                    @include('notificaciones._tabla', ['notificaciones' => $notificaciones])
                </div>


            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let intervaloActualizacion;
    let estaActualizando = false;

    async function actualizarTabla() {
        // Evitar múltiples actualizaciones simultáneas
        if (estaActualizando) {
            return;
        }

        estaActualizando = true;
        
        try {
            @if(auth()->user()->isPaciente())
                const url = "{{ route('paciente.notificaciones.tabla') }}";
            @elseif(auth()->user()->isMedico())
                const url = "{{ route('medico.notificaciones.tabla') }}";
            @endif

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                cache: 'no-cache'
            });

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const html = await response.text();
            
            // Actualizar la tabla
            const tablaContainer = document.getElementById("tablaNotificaciones");
            if (tablaContainer) {
                tablaContainer.innerHTML = html;
            }

            // Actualizar estadísticas contando las filas de la tabla
            const filas = tablaContainer.querySelectorAll('tbody tr');
            const statsRecibidas = document.getElementById("stats-recibidas");
            if (statsRecibidas) {
                // Contar solo las filas que no sean el mensaje vacío
                const filasConDatos = Array.from(filas).filter(fila => 
                    !fila.textContent.includes('No tienes mensajes recibidos')
                ).length;
                statsRecibidas.textContent = filasConDatos;
            }

        } catch (error) {
            console.error("Error actualizando tabla:", error);
            // No mostrar error al usuario para evitar interrupciones
        } finally {
            estaActualizando = false;
        }
    }

    // Iniciar actualización automática cada 2 segundos
    document.addEventListener('DOMContentLoaded', function() {
        // Primera actualización después de 2 segundos
        setTimeout(actualizarTabla, 2000);
        
        // Luego actualizar cada 2 segundos
        intervaloActualizacion = setInterval(actualizarTabla, 2000);
    });

    // Limpiar intervalo cuando se sale de la página
    window.addEventListener('beforeunload', function() {
        if (intervaloActualizacion) {
            clearInterval(intervaloActualizacion);
        }
    });
</script>
@endsection
