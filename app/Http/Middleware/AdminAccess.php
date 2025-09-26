<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Si no hay usuario autenticado, redirigir al login
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Si el usuario es administrador, permitir acceso a todas las rutas admin
        if ($user->rol === 'administrador') {
            return $next($request);
        }
        
        // Si no es administrador, denegar acceso
        return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
    }
}
