<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notificación</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ver</th>
        </tr>
    </thead>

    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($notificaciones as $mensaje)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-900">
                            {{ $mensaje->usuario ? ($mensaje->usuario->nombre . ' ' . $mensaje->usuario->apPaterno) : 'Usuario desconocido' }}
                        </span>
                        <span class="text-sm text-gray-500 truncate max-w-md">
                            {{ Str::limit($mensaje->mensaje, 50) }}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    {{ $mensaje->fecha_envio ? $mensaje->fecha_envio->format('d/m/Y H:i') : 'N/A' }}
                </td>
                <td class="px-6 py-4">
                    @if(auth()->user()->isPaciente())
                        <a href="{{ route('mensajes.paciente', ['id_chat' => $mensaje->id_chat]) }}" 
                           class="text-blue-600 hover:text-blue-900">
                            Ver Chat
                        </a>
                    @elseif(auth()->user()->isMedico())
                        <a href="{{ route('mensajes.medico', ['id_chat' => $mensaje->id_chat]) }}" 
                           class="text-blue-600 hover:text-blue-900">
                            Ver Chat
                        </a>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                    No tienes mensajes recibidos.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
