<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Obtener todos los chats donde el usuario participa
        $chats = Chat::where('user_one_id', $user->id_usuario)
            ->orWhere('user_two_id', $user->id_usuario)
            ->pluck('id_chat');
        
        // Obtener mensajes recibidos (donde el usuario NO es el remitente)
        $notificaciones = Mensaje::whereIn('id_chat', $chats)
            ->where('id_usuario', '!=', $user->id_usuario)
            ->with(['usuario', 'chat'])
            ->orderBy('fecha_envio', 'desc')
            ->get();
        
        // Calcular estadísticas
        $stats = [
            'recibidas' => $notificaciones->count(),
        ];
        
        return view('notificaciones.notificaciones', compact('notificaciones', 'stats'));
    }

    public function actualizarTabla()
    {
        $user = Auth::user();
        
        // Obtener todos los chats donde el usuario participa
        $chats = Chat::where('user_one_id', $user->id_usuario)
            ->orWhere('user_two_id', $user->id_usuario)
            ->pluck('id_chat');
        
        // Obtener mensajes recibidos (donde el usuario NO es el remitente)
        $notificaciones = Mensaje::whereIn('id_chat', $chats)
            ->where('id_usuario', '!=', $user->id_usuario)
            ->with(['usuario', 'chat'])
            ->orderBy('fecha_envio', 'desc')
            ->get();

        return view('notificaciones._tabla', compact('notificaciones'));
    }

}