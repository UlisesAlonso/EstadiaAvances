<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mensaje;
use App\Models\Chat;
use App\Models\User;

class MensajesController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirigir a la vista correspondiente según el rol del usuario.
        if ($user->isMedico() || $user->isAdmin()) {
            return redirect()->route('mensajes.medico');
        }

        return redirect()->route('mensajes.paciente');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_chat' => 'required|exists:chats,id_chat',
            'mensaje' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        
        $mensaje = Mensaje::create([
            'id_chat' => $request->id_chat,
            'id_usuario' => $user->id_usuario,
            'mensaje' => $request->mensaje,
            'fecha_envio' => now(),
        ]);

        return redirect()->back()->with('success', 'Mensaje enviado correctamente');
    }

    public function mensajesPaciente()
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        $chats = Chat::where('user_one_id', $user->id_usuario)
                     ->orWhere('user_two_id', $user->id_usuario)
                     ->with(['userOne', 'userTwo'])
                     ->get();

        return view('chats.mensajesPaciente', compact('chats'));
    }

    public function mensajesMedico()
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        $chats = Chat::where('user_one_id', $user->id_usuario)
                     ->orWhere('user_two_id', $user->id_usuario)
                     ->with(['userOne', 'userTwo'])
                     ->get();

        // Obtener pacientes para iniciar nuevos chats
        $pacientes = User::where('rol', 'paciente')
                        ->where('activo', true)
                        ->get();

        return view('chats.mensajesMedico', compact('chats', 'pacientes'));
    }

    public function getMensajes($id_chat)
    {
        $user = Auth::user();
        
        // Verificar que el usuario tiene acceso a este chat
        $chat = Chat::where('id_chat', $id_chat)
                    ->where(function($query) use ($user) {
                        $query->where('user_one_id', $user->id_usuario)
                              ->orWhere('user_two_id', $user->id_usuario);
                    })
                    ->first();

        if (!$chat) {
            return response()->json(['error' => 'Chat no encontrado'], 404);
        }

        $mensajes = Mensaje::where('id_chat', $id_chat)
                          ->with('usuario')
                          ->orderBy('fecha_envio', 'asc')
                          ->get()
                          ->map(function($mensaje) {
                              return [
                                  'id_mensaje' => $mensaje->id_mensaje,
                                  'id_chat' => $mensaje->id_chat,
                                  'id_usuario' => $mensaje->id_usuario,
                                  'mensaje' => $mensaje->mensaje,
                                  'fecha_envio' => $mensaje->fecha_envio,
                                  'nombre' => $mensaje->usuario->nombre ?? '',
                                  'apPaterno' => $mensaje->usuario->apPaterno ?? '',
                                  'apMaterno' => $mensaje->usuario->apMaterno ?? '',
                                  'rol' => $mensaje->usuario->rol ?? '',
                              ];
                          });

        return response()->json($mensajes);
    }

    public function postMensajes(Request $request)
    {
        $user = Auth::user();
        $mensaje = Mensaje::create([
            'id_chat' => $request->id_chat,
            'id_usuario' => $user->id_usuario,
            'mensaje' => $request->mensaje,
            'fecha_envio' => now(),
        ]);
        return redirect()->back()->with('success', 'Mensaje enviado correctamente');
    }

    public function iniciarChat($id_usuario)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin() && !$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        // Verificar si ya existe un chat entre estos usuarios
        $chatExistente = Chat::where(function($query) use ($user, $id_usuario) {
            $query->where('user_one_id', $user->id_usuario)
                  ->where('user_two_id', $id_usuario);
        })->orWhere(function($query) use ($user, $id_usuario) {
            $query->where('user_one_id', $id_usuario)
                  ->where('user_two_id', $user->id_usuario);
        })->first();

        if ($chatExistente) {
            // Si ya existe, redirigir al chat existente
            if ($user->isMedico() || $user->isAdmin()) {
                return redirect()->route('mensajes.medico')->with('info', 'Ya tienes una conversación con este usuario.');
            } else {
                return redirect()->route('mensajes.paciente')->with('info', 'Ya tienes una conversación con este usuario.');
            }
        }

        // Crear nuevo chat
        $chat = Chat::create([
            'user_one_id' => $user->id_usuario,
            'user_two_id' => $id_usuario,
        ]);

        // Redirigir según el rol
        if ($user->isMedico() || $user->isAdmin()) {
            return redirect()->route('mensajes.medico')->with('success', 'Chat iniciado correctamente.');
        } else {
            return redirect()->route('mensajes.paciente')->with('success', 'Chat iniciado correctamente.');
        }
    }
}