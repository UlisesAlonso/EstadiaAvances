@extends('layouts.app')

@section('title', 'Mensajes')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Mensajes</h2>
            <p class="text-gray-600">Comunícate con tu médico</p>
        </div>

        <!-- Alerta de notificación -->
        <div id="alertaMensaje" 
             style="display:none; 
                    position:fixed; 
                    top:20px; 
                    right:20px; 
                    padding:15px 20px; 
                    background:#10b981; 
                    color:white; 
                    border-radius:8px; 
                    font-weight:bold;
                    z-index:9999;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    animation: slideInRight 0.3s ease-out;">
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="flex h-[600px]">
                <!-- Lista de Chats (Sidebar) -->
                <div class="w-1/3 border-r border-gray-200 bg-gray-50 overflow-y-auto">
                    <div class="p-4 border-b border-gray-200 bg-white">
                        <h3 class="text-lg font-semibold text-gray-900">Conversaciones</h3>
                    </div>
                    
                    @if($chats && $chats->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($chats as $chat)
                                @php
                                    $otroUsuario = $chat->user_one_id == auth()->user()->id_usuario 
                                        ? $chat->userTwo 
                                        : $chat->userOne;
                                @endphp
                                <button 
                                    onclick="cargarChat({{ $chat->id_chat }}, {{ $otroUsuario->id_usuario }}, '{{ $otroUsuario->nombre }} {{ $otroUsuario->apPaterno }}')"
                                    class="w-full text-left p-4 hover:bg-blue-50 transition-colors chat-item"
                                    data-chat-id="{{ $chat->id_chat }}">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold">
                                                {{ substr($otroUsuario->nombre, 0, 1) }}{{ substr($otroUsuario->apPaterno ?? '', 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="ml-3 flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $otroUsuario->nombre }} {{ $otroUsuario->apPaterno }}
                                            </p>
                                            <p class="text-xs text-gray-500 truncate">
                                                {{ $otroUsuario->rol == 'medico' ? 'Médico' : 'Usuario' }}
                                            </p>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <p class="text-gray-500">No tienes conversaciones aún</p>
                        </div>
                    @endif
                </div>

                <!-- Área de Chat -->
                <div class="flex-1 flex flex-col">
                    <!-- Header del Chat -->
                    <div id="chat-header" class="p-4 border-b border-gray-200 bg-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold mr-3" id="chat-avatar">
                                    ?
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900" id="chat-name">Selecciona una conversación</h3>
                                    <p class="text-sm text-gray-500" id="chat-status">En línea</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mensajes -->
                    <div id="mensajes-container" class="flex-1 overflow-y-auto p-4 bg-gray-50 space-y-4">
                        <div class="text-center text-gray-500 py-8">
                            <p>Selecciona una conversación para comenzar</p>
                        </div>
                    </div>

                    <!-- Formulario de Envío -->
                    <div id="chat-form-container" class="p-4 border-t border-gray-200 bg-white" style="display: none;">
                        <form id="form-mensaje" class="flex space-x-2">
                            <input 
                                type="hidden" 
                                id="current-chat-id" 
                                name="id_chat">
                            <input 
                                type="text" 
                                id="mensaje-input" 
                                name="mensaje"
                                placeholder="Escribe un mensaje..."
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                autocomplete="off">
                            <button 
                                type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Socket.io -->
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    // Variables globales
    const socket = io('http://localhost:3001');
    const idUsuario = {{ auth()->user()->id_usuario }};
    let currentChatId = null;
    let currentOtherUserId = null;

    // Conexión establecida
    socket.on('connect', () => {
        console.log('Conectado a Socket.io con id:', socket.id);
    });

    // Desconexión
    socket.on('disconnect', () => {
        console.log('Desconectado de Socket.io');
    });

    // Recibir nuevo mensaje
    socket.on('new_message', (data) => {
        console.log('Nuevo mensaje recibido:', data);
        
        // Si el mensaje es del chat actual, agregarlo al DOM
        if (data.id_chat == currentChatId) {
            agregarMensajeAlDOM(data);
        }
        
        // Mostrar notificación si el mensaje NO es del usuario actual
        if (data.id_usuario != idUsuario) {
            const nombreRemitente = (data.nombre || 'Usuario') + ' ' + (data.apPaterno || '');
            mostrarAlerta('Nuevo mensaje de ' + nombreRemitente);
        }
    });

    // Error al enviar mensaje
    socket.on('error_message', (data) => {
        alert('Error: ' + data.message);
    });

    // Cargar un chat
    function cargarChat(idChat, idOtroUsuario, nombreOtroUsuario) {
        currentChatId = idChat;
        currentOtherUserId = idOtroUsuario;

        // Actualizar header
        document.getElementById('chat-name').textContent = nombreOtroUsuario;
        document.getElementById('chat-avatar').textContent = nombreOtroUsuario.charAt(0);
        document.getElementById('current-chat-id').value = idChat;

        // Mostrar formulario
        document.getElementById('chat-form-container').style.display = 'block';

        // Limpiar mensajes
        document.getElementById('mensajes-container').innerHTML = '';

        // Unirse al chat
        socket.emit('join_chat', { 
            id_chat: idChat, 
            id_usuario: idUsuario 
        });

        // Cargar mensajes existentes
        cargarMensajesExistentes(idChat);

        // Resaltar chat seleccionado
        document.querySelectorAll('.chat-item').forEach(item => {
            item.classList.remove('bg-blue-100');
        });
        document.querySelector(`[data-chat-id="${idChat}"]`).classList.add('bg-blue-100');
    }

    // Cargar mensajes existentes desde Laravel
    async function cargarMensajesExistentes(idChat) {
        try {
            const response = await fetch(`/api/mensajes/${idChat}`);
            const mensajes = await response.json();
            
            mensajes.forEach(mensaje => {
                agregarMensajeAlDOM(mensaje);
            });

            // Scroll al final
            scrollToBottom();
        } catch (error) {
            console.error('Error al cargar mensajes:', error);
        }
    }

    // Agregar mensaje al DOM
    function agregarMensajeAlDOM(data) {
        const container = document.getElementById('mensajes-container');
        const esPropio = data.id_usuario == idUsuario;
        
        const mensajeDiv = document.createElement('div');
        mensajeDiv.className = `flex ${esPropio ? 'justify-end' : 'justify-start'}`;
        
        mensajeDiv.innerHTML = `
            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${esPropio ? 'bg-blue-600 text-white' : 'bg-white text-gray-900 border border-gray-200'}">
                ${!esPropio ? `<p class="text-xs font-semibold mb-1 ${esPropio ? 'text-blue-100' : 'text-gray-600'}">${data.nombre || 'Usuario'} ${data.apPaterno || ''}</p>` : ''}
                <p class="text-sm">${escapeHtml(data.mensaje)}</p>
                <p class="text-xs mt-1 ${esPropio ? 'text-blue-100' : 'text-gray-500'}">
                    ${formatearFecha(data.fecha_envio)}
                </p>
            </div>
        `;
        
        container.appendChild(mensajeDiv);
        scrollToBottom();
    }

    // Formulario de envío
    document.getElementById('form-mensaje').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const mensajeInput = document.getElementById('mensaje-input');
        const mensaje = mensajeInput.value.trim();
        
        if (!mensaje || !currentChatId) {
            return;
        }

        // Enviar mensaje vía Socket.io
        socket.emit('send_message', {
            id_chat: currentChatId,
            id_usuario: idUsuario,
            mensaje: mensaje
        });

        // Limpiar input
        mensajeInput.value = '';
    });

    // Funciones auxiliares
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatearFecha(fecha) {
        const date = new Date(fecha);
        const ahora = new Date();
        const diff = ahora - date;
        const minutos = Math.floor(diff / 60000);

        if (minutos < 1) return 'Ahora';
        if (minutos < 60) return `Hace ${minutos} min`;
        if (minutos < 1440) return `Hace ${Math.floor(minutos / 60)} h`;
        
        return date.toLocaleDateString('es-ES', { 
            day: '2-digit', 
            month: '2-digit', 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    function scrollToBottom() {
        const container = document.getElementById('mensajes-container');
        container.scrollTop = container.scrollHeight;
    }

    // Función para mostrar alerta por 3 segundos
    function mostrarAlerta(texto) {
        const alerta = document.getElementById("alertaMensaje");
        alerta.innerText = texto;
        alerta.style.display = "block";

        setTimeout(() => {
            alerta.style.display = "none";
        }, 3000);
    }
</script>

<style>
    /* Scrollbar personalizado */
    #mensajes-container::-webkit-scrollbar,
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    #mensajes-container::-webkit-scrollbar-track,
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #mensajes-container::-webkit-scrollbar-thumb,
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    #mensajes-container::-webkit-scrollbar-thumb:hover,
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Animación para nuevos mensajes */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #mensajes-container > div:last-child {
        animation: slideIn 0.3s ease-out;
    }

    /* Animación para la alerta */
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>
@endsection
