<?php

namespace App\Http\Controllers;

use App\Models\PublicacionForo;
use App\Models\ComentarioForo;
use App\Models\ReaccionForo;
use App\Models\FavoritoForo;
use App\Models\Paciente;
use App\Models\Actividad;
use App\Models\Tratamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ForoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Solo pacientes y administradores pueden ver el foro
        if (!$user->isPaciente() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder al foro.');
        }

        $query = PublicacionForo::with(['paciente.usuario', 'actividad', 'tratamiento'])
                                 ->withCount(['reacciones', 'comentarios', 'favoritos']);

        // Pacientes solo ven publicaciones aprobadas
        if ($user->isPaciente()) {
            $query->aprobadas();
        }
        // Administradores ven todas (incluyendo pendientes y ocultas)

        // Filtros de búsqueda
        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }

        // Filtro por etiqueta específica
        if ($request->filled('etiqueta')) {
            $query->where('etiquetas', 'like', '%' . $request->etiqueta . '%');
        }

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_publicacion', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_publicacion', '<=', $request->fecha_hasta);
        }

        // Filtro por estado (solo admin)
        // Si es admin y se especifica un estado válido, filtrar por ese estado
        // Si no se especifica estado o está vacío, mostrar todas (no aplicar filtro)
        if ($user->isAdmin()) {
            $estado = $request->input('estado');
            if (!empty($estado) && in_array($estado, ['aprobada', 'pendiente', 'oculta'])) {
                if ($estado == 'aprobada') {
                    $query->aprobadas();
                } elseif ($estado == 'pendiente') {
                    $query->pendientes();
                } elseif ($estado == 'oculta') {
                    $query->ocultas();
                }
            }
            // Si el estado está vacío o es "Todos", no aplicar filtro (mostrar todas)
        }

        // Filtro por actividad relacionada
        if ($request->filled('id_actividad')) {
            $query->where('id_actividad', $request->id_actividad);
        }

        // Filtro por tratamiento relacionado
        if ($request->filled('id_tratamiento')) {
            $query->where('id_tratamiento', $request->id_tratamiento);
        }

        // Filtro por popularidad (mínimo número de reacciones)
        if ($request->filled('popularidad_min')) {
            $minReacciones = (int) $request->popularidad_min;
            // Filtrar publicaciones que tienen al menos X reacciones usando subquery
            $query->whereRaw('(SELECT COUNT(*) FROM reacciones_foro WHERE reacciones_foro.id_publicacion = publicaciones_foro.id_publicacion) >= ?', [$minReacciones]);
        }

        // Ordenamiento
        $orden = $request->get('orden', 'fecha');
        if ($orden == 'fecha') {
            $query->porFecha('desc');
        } elseif ($orden == 'relevancia') {
            $query->porRelevancia();
        } elseif ($orden == 'popularidad') {
            // Ordenar por número de reacciones (más reacciones primero)
            $query->orderByRaw('(SELECT COUNT(*) FROM reacciones_foro WHERE reacciones_foro.id_publicacion = publicaciones_foro.id_publicacion) DESC')
                  ->orderBy('fecha_publicacion', 'desc');
        } elseif ($orden == 'comentarios') {
            // Ordenar por número de comentarios (más comentarios primero)
            $query->orderByRaw('(SELECT COUNT(*) FROM comentarios_foro WHERE comentarios_foro.id_publicacion = publicaciones_foro.id_publicacion) DESC')
                  ->orderBy('fecha_publicacion', 'desc');
        } else {
            $query->porFecha('desc');
        }

        $publicaciones = $query->paginate(15);

        // Estadísticas
        $stats = $this->getStats($user);

        // Opciones para filtros (etiquetas únicas, actividades, tratamientos)
        $etiquetas = PublicacionForo::whereNotNull('etiquetas')
                                   ->where('etiquetas', '!=', '')
                                   ->selectRaw('DISTINCT etiquetas')
                                   ->get()
                                   ->flatMap(function($pub) {
                                       return explode(',', $pub->etiquetas);
                                   })
                                   ->map(function($tag) {
                                       return trim($tag);
                                   })
                                   ->filter()
                                   ->unique()
                                   ->sort()
                                   ->values();

        // Actividades relacionadas (solo las que tienen publicaciones)
        $actividadesRelacionadas = Actividad::whereHas('publicacionesForo')
                                           ->with('paciente.usuario')
                                           ->get();

        // Tratamientos relacionados (solo los que tienen publicaciones)
        $tratamientosRelacionados = Tratamiento::whereHas('publicacionesForo')
                                               ->with('paciente.usuario')
                                               ->get();

        return view('foro.index', compact('publicaciones', 'stats', 'etiquetas', 'actividadesRelacionadas', 'tratamientosRelacionados'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('foro.index')->with('error', 'Solo los pacientes pueden crear publicaciones.');
        }

        // Obtener actividades y tratamientos del paciente para enlazar opcionalmente
        $paciente = $user->paciente;
        $actividades = Actividad::where('id_paciente', $paciente->id_paciente)
                               ->where('completada', true)
                               ->orderBy('fecha_asignacion', 'desc')
                               ->get();
        $tratamientos = Tratamiento::where('id_paciente', $paciente->id_paciente)
                                   ->where('activo', true)
                                   ->orderBy('fecha_inicio', 'desc')
                                   ->get();

        return view('foro.create', compact('actividades', 'tratamientos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('foro.index')->with('error', 'Solo los pacientes pueden crear publicaciones.');
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'fecha_publicacion' => 'required|date',
            'id_actividad' => 'nullable|exists:actividades,id_actividad',
            'id_tratamiento' => 'nullable|exists:tratamientos,id_tratamiento',
            'etiquetas' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Verificar que la actividad o tratamiento pertenezca al paciente
        if ($request->filled('id_actividad')) {
            $actividad = Actividad::where('id_actividad', $request->id_actividad)
                                 ->where('id_paciente', $user->paciente->id_paciente)
                                 ->first();
            if (!$actividad) {
                return redirect()->back()->withErrors(['id_actividad' => 'La actividad seleccionada no te pertenece.'])->withInput();
            }
        }

        if ($request->filled('id_tratamiento')) {
            $tratamiento = Tratamiento::where('id_tratamiento', $request->id_tratamiento)
                                     ->where('id_paciente', $user->paciente->id_paciente)
                                     ->first();
            if (!$tratamiento) {
                return redirect()->back()->withErrors(['id_tratamiento' => 'El tratamiento seleccionado no te pertenece.'])->withInput();
            }
        }

        $publicacion = PublicacionForo::create([
            'id_paciente' => $user->paciente->id_paciente,
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'fecha_publicacion' => $request->fecha_publicacion,
            'estado' => 'pendiente', // Por defecto pendiente hasta aprobación
            'id_actividad' => $request->id_actividad,
            'id_tratamiento' => $request->id_tratamiento,
            'etiquetas' => $request->etiquetas,
        ]);

        return redirect()->route('foro.index')->with('success', 'Publicación creada exitosamente. Estará visible después de ser aprobada por un administrador.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $publicacion = PublicacionForo::with([
            'paciente.usuario',
            'actividad',
            'tratamiento',
            'comentarios.paciente.usuario',
            'reacciones.paciente.usuario'
        ])
        ->withCount(['reacciones', 'comentarios', 'favoritos'])
        ->findOrFail($id);

        // Pacientes solo pueden ver publicaciones aprobadas (excepto las suyas)
        if ($user->isPaciente()) {
            if ($publicacion->estado !== 'aprobada' && $publicacion->id_paciente !== $user->paciente->id_paciente) {
                return redirect()->route('foro.index')->with('error', 'Esta publicación no está disponible.');
            }
        }

        // Verificar si el usuario actual reaccionó o marcó como favorito
        $usuarioReacciono = false;
        $esFavorito = false;
        if ($user->isPaciente()) {
            $usuarioReacciono = $publicacion->tieneReaccionDePaciente($user->paciente->id_paciente);
            $esFavorito = $publicacion->esFavoritoDePaciente($user->paciente->id_paciente);
        }

        return view('foro.show', compact('publicacion', 'usuarioReacciono', 'esFavorito'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('foro.index')->with('error', 'Solo los pacientes pueden editar publicaciones.');
        }

        $publicacion = PublicacionForo::findOrFail($id);

        // Solo el autor puede editar
        if ($publicacion->id_paciente !== $user->paciente->id_paciente) {
            return redirect()->route('foro.index')->with('error', 'No tienes permisos para editar esta publicación.');
        }

        // Obtener actividades y tratamientos del paciente
        $paciente = $user->paciente;
        $actividades = Actividad::where('id_paciente', $paciente->id_paciente)
                               ->where('completada', true)
                               ->orderBy('fecha_asignacion', 'desc')
                               ->get();
        $tratamientos = Tratamiento::where('id_paciente', $paciente->id_paciente)
                                  ->where('activo', true)
                                  ->orderBy('fecha_inicio', 'desc')
                                  ->get();

        return view('foro.edit', compact('publicacion', 'actividades', 'tratamientos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('foro.index')->with('error', 'Solo los pacientes pueden editar publicaciones.');
        }

        $publicacion = PublicacionForo::findOrFail($id);

        // Solo el autor puede editar
        if ($publicacion->id_paciente !== $user->paciente->id_paciente) {
            return redirect()->route('foro.index')->with('error', 'No tienes permisos para editar esta publicación.');
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'fecha_publicacion' => 'required|date',
            'id_actividad' => 'nullable|exists:actividades,id_actividad',
            'id_tratamiento' => 'nullable|exists:tratamientos,id_tratamiento',
            'etiquetas' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Verificar que la actividad o tratamiento pertenezca al paciente
        if ($request->filled('id_actividad')) {
            $actividad = Actividad::where('id_actividad', $request->id_actividad)
                                 ->where('id_paciente', $user->paciente->id_paciente)
                                 ->first();
            if (!$actividad) {
                return redirect()->back()->withErrors(['id_actividad' => 'La actividad seleccionada no te pertenece.'])->withInput();
            }
        }

        if ($request->filled('id_tratamiento')) {
            $tratamiento = Tratamiento::where('id_tratamiento', $request->id_tratamiento)
                                     ->where('id_paciente', $user->paciente->id_paciente)
                                     ->first();
            if (!$tratamiento) {
                return redirect()->back()->withErrors(['id_tratamiento' => 'El tratamiento seleccionado no te pertenece.'])->withInput();
            }
        }

        // Si se edita, volver a estado pendiente para revisión
        $publicacion->update([
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'fecha_publicacion' => $request->fecha_publicacion,
            'estado' => 'pendiente', // Volver a pendiente al editar
            'id_actividad' => $request->id_actividad,
            'id_tratamiento' => $request->id_tratamiento,
            'etiquetas' => $request->etiquetas,
        ]);

        return redirect()->route('foro.show', $publicacion->id_publicacion)->with('success', 'Publicación actualizada exitosamente. Estará visible después de ser aprobada nuevamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        $publicacion = PublicacionForo::findOrFail($id);

        // Solo el autor o un administrador pueden eliminar
        if ($user->isPaciente() && $publicacion->id_paciente !== $user->paciente->id_paciente) {
            return redirect()->route('foro.index')->with('error', 'No tienes permisos para eliminar esta publicación.');
        }

        if (!$user->isPaciente() && !$user->isAdmin()) {
            return redirect()->route('foro.index')->with('error', 'No tienes permisos para eliminar publicaciones.');
        }

        $publicacion->delete();

        return redirect()->route('foro.index')->with('success', 'Publicación eliminada exitosamente.');
    }

    /**
     * Ver mis publicaciones (para pacientes)
     */
    public function misPublicaciones(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('foro.index')->with('error', 'Solo los pacientes pueden ver sus publicaciones.');
        }

        $query = PublicacionForo::with(['actividad', 'tratamiento'])
                                 ->withCount(['reacciones', 'comentarios', 'favoritos'])
                                 ->where('id_paciente', $user->paciente->id_paciente);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_publicacion', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_publicacion', '<=', $request->fecha_hasta);
        }

        $publicaciones = $query->porFecha('desc')->paginate(15);

        return view('foro.mis-publicaciones', compact('publicaciones'));
    }

    /**
     * Panel de moderación (solo administradores)
     */
    public function moderacion(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('foro.index')->with('error', 'Solo los administradores pueden acceder a la moderación.');
        }

        $query = PublicacionForo::with(['paciente.usuario', 'actividad', 'tratamiento'])
                                 ->withCount(['reacciones', 'comentarios', 'favoritos']);

        // Filtro por estado
        $estado = $request->input('estado');
        if (!empty($estado) && in_array($estado, ['aprobada', 'pendiente', 'oculta'])) {
            // Si se especifica un estado válido, filtrar por ese estado
            if ($estado == 'aprobada') {
                $query->aprobadas();
            } elseif ($estado == 'pendiente') {
                $query->pendientes();
            } elseif ($estado == 'oculta') {
                $query->ocultas();
            }
        } else {
            // Si no se especifica estado o está vacío ("Todos"), mostrar todas las publicaciones
            // No aplicar ningún filtro de estado
        }

        $publicaciones = $query->porFecha('desc')->paginate(15);

        // Estadísticas de moderación
        $stats = [
            'pendientes' => PublicacionForo::pendientes()->count(),
            'aprobadas' => PublicacionForo::aprobadas()->count(),
            'ocultas' => PublicacionForo::ocultas()->count(),
            'total' => PublicacionForo::count(),
        ];

        return view('foro.moderacion', compact('publicaciones', 'stats'));
    }

    /**
     * Aprobar publicación (solo administradores)
     */
    public function aprobar($id)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('foro.index')->with('error', 'Solo los administradores pueden aprobar publicaciones.');
        }

        $publicacion = PublicacionForo::findOrFail($id);
        $publicacion->update(['estado' => 'aprobada']);

        return redirect()->back()->with('success', 'Publicación aprobada exitosamente.');
    }

    /**
     * Ocultar publicación (solo administradores)
     */
    public function ocultar($id)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('foro.index')->with('error', 'Solo los administradores pueden ocultar publicaciones.');
        }

        $publicacion = PublicacionForo::findOrFail($id);
        $publicacion->update(['estado' => 'oculta']);

        return redirect()->back()->with('success', 'Publicación ocultada exitosamente.');
    }

    /**
     * Obtener estadísticas del foro
     */
    private function getStats($user)
    {
        if ($user->isAdmin()) {
            return [
                'total' => PublicacionForo::count(),
                'aprobadas' => PublicacionForo::aprobadas()->count(),
                'pendientes' => PublicacionForo::pendientes()->count(),
                'ocultas' => PublicacionForo::ocultas()->count(),
            ];
        } else {
            // Estadísticas para pacientes
            return [
                'total' => PublicacionForo::aprobadas()->count(),
                'mis_publicaciones' => PublicacionForo::where('id_paciente', $user->paciente->id_paciente)->count(),
            ];
        }
    }

    /**
     * Crear un comentario en una publicación
     */
    public function storeComentario(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('foro.show', $id)->with('error', 'Solo los pacientes pueden comentar.');
        }

        $validator = Validator::make($request->all(), [
            'contenido' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->route('foro.show', $id)
                           ->withErrors($validator)
                           ->withInput();
        }

        // Verificar que la publicación existe y está aprobada (o es del mismo paciente)
        $publicacion = PublicacionForo::findOrFail($id);
        
        if ($publicacion->estado !== 'aprobada' && $publicacion->id_paciente !== $user->paciente->id_paciente) {
            return redirect()->route('foro.show', $id)->with('error', 'No puedes comentar en esta publicación.');
        }

        $comentario = ComentarioForo::create([
            'id_publicacion' => $id,
            'id_paciente' => $user->paciente->id_paciente,
            'contenido' => $request->contenido,
            'fecha_comentario' => now(),
        ]);

        return redirect()->route('foro.show', $id)->with('success', 'Comentario agregado exitosamente.');
    }

    /**
     * Actualizar un comentario
     */
    public function updateComentario(Request $request, $id, $idComentario)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('foro.show', $id)->with('error', 'Solo los pacientes pueden editar comentarios.');
        }

        $comentario = ComentarioForo::findOrFail($idComentario);

        // Verificar que el comentario pertenece a la publicación
        if ($comentario->id_publicacion != $id) {
            return redirect()->route('foro.show', $id)->with('error', 'Comentario no válido.');
        }

        // Solo el autor puede editar
        if ($comentario->id_paciente !== $user->paciente->id_paciente) {
            return redirect()->route('foro.show', $id)->with('error', 'No tienes permisos para editar este comentario.');
        }

        $validator = Validator::make($request->all(), [
            'contenido' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->route('foro.show', $id)
                           ->withErrors($validator)
                           ->withInput();
        }

        $comentario->update([
            'contenido' => $request->contenido,
        ]);

        return redirect()->route('foro.show', $id)->with('success', 'Comentario actualizado exitosamente.');
    }

    /**
     * Eliminar un comentario
     */
    public function destroyComentario($id, $idComentario)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente() && !$user->isAdmin()) {
            return redirect()->route('foro.show', $id)->with('error', 'No tienes permisos para eliminar comentarios.');
        }

        $comentario = ComentarioForo::findOrFail($idComentario);

        // Verificar que el comentario pertenece a la publicación
        if ($comentario->id_publicacion != $id) {
            return redirect()->route('foro.show', $id)->with('error', 'Comentario no válido.');
        }

        // Solo el autor o un administrador pueden eliminar
        if ($user->isPaciente() && $comentario->id_paciente !== $user->paciente->id_paciente) {
            return redirect()->route('foro.show', $id)->with('error', 'No tienes permisos para eliminar este comentario.');
        }

        $comentario->delete();

        return redirect()->route('foro.show', $id)->with('success', 'Comentario eliminado exitosamente.');
    }

    /**
     * Toggle reacción (me gusta) en una publicación
     */
    public function toggleReaccion($id)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('foro.show', $id)->with('error', 'Solo los pacientes pueden reaccionar.');
        }

        // Verificar que la publicación existe y está aprobada (o es del mismo paciente)
        $publicacion = PublicacionForo::findOrFail($id);
        
        if ($publicacion->estado !== 'aprobada' && $publicacion->id_paciente !== $user->paciente->id_paciente) {
            return redirect()->route('foro.show', $id)->with('error', 'No puedes reaccionar a esta publicación.');
        }

        // Buscar si ya existe una reacción del paciente en esta publicación
        $reaccion = ReaccionForo::where('id_publicacion', $id)
                                ->where('id_paciente', $user->paciente->id_paciente)
                                ->first();

        if ($reaccion) {
            // Si existe, eliminar la reacción (toggle off)
            $reaccion->delete();
            $mensaje = 'Reacción eliminada exitosamente.';
            $reaccionado = false;
        } else {
            // Si no existe, crear la reacción (toggle on)
            ReaccionForo::create([
                'id_publicacion' => $id,
                'id_paciente' => $user->paciente->id_paciente,
                'tipo_reaccion' => 'me_gusta', // Por defecto "me gusta"
            ]);
            $mensaje = 'Reacción agregada exitosamente.';
            $reaccionado = true;
        }

        return redirect()->route('foro.show', $id)->with('success', $mensaje);
    }

    /**
     * Obtener contador de reacciones (para AJAX)
     */
    public function getReacciones($id)
    {
        $publicacion = PublicacionForo::findOrFail($id);
        
        $totalReacciones = $publicacion->reacciones()->count();
        
        $usuarioReacciono = false;
        if (Auth::check() && Auth::user()->isPaciente()) {
            $usuarioReacciono = $publicacion->tieneReaccionDePaciente(Auth::user()->paciente->id_paciente);
        }

        return response()->json([
            'total' => $totalReacciones,
            'usuario_reacciono' => $usuarioReacciono,
        ]);
    }

    /**
     * Toggle favorito (marcar/desmarcar como favorito)
     */
    public function toggleFavorito($id)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('foro.show', $id)->with('error', 'Solo los pacientes pueden marcar favoritos.');
        }

        // Verificar que la publicación existe y está aprobada (o es del mismo paciente)
        $publicacion = PublicacionForo::findOrFail($id);
        
        if ($publicacion->estado !== 'aprobada' && $publicacion->id_paciente !== $user->paciente->id_paciente) {
            return redirect()->route('foro.show', $id)->with('error', 'No puedes marcar como favorito esta publicación.');
        }

        // Buscar si ya existe un favorito del paciente en esta publicación
        $favorito = FavoritoForo::where('id_publicacion', $id)
                                ->where('id_paciente', $user->paciente->id_paciente)
                                ->first();

        if ($favorito) {
            // Si existe, eliminar el favorito (toggle off)
            $favorito->delete();
            $mensaje = 'Publicación eliminada de favoritos.';
            $esFavorito = false;
        } else {
            // Si no existe, crear el favorito (toggle on)
            FavoritoForo::create([
                'id_publicacion' => $id,
                'id_paciente' => $user->paciente->id_paciente,
            ]);
            $mensaje = 'Publicación agregada a favoritos.';
            $esFavorito = true;
        }

        return redirect()->route('foro.show', $id)->with('success', $mensaje);
    }

    /**
     * Ver publicaciones favoritas del usuario
     */
    public function favoritos(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('foro.index')->with('error', 'Solo los pacientes pueden ver sus favoritos.');
        }

        // Verificar que el usuario tenga relación con paciente
        if (!$user->paciente) {
            return redirect()->route('foro.index')->with('error', 'No se encontró información del paciente.');
        }

        // Obtener IDs de publicaciones favoritas del usuario
        $favoritosIds = FavoritoForo::where('id_paciente', $user->paciente->id_paciente)
                                   ->pluck('id_publicacion');

        // Si no hay favoritos, retornar query vacío
        if ($favoritosIds->isEmpty()) {
            $publicaciones = PublicacionForo::whereRaw('1 = 0')->paginate(15);
            $stats = [
                'total_favoritos' => 0,
                'total_publicaciones' => PublicacionForo::aprobadas()->count(),
            ];
            $etiquetas = collect();
            return view('foro.favoritos', compact('publicaciones', 'stats', 'etiquetas'));
        }

        $query = PublicacionForo::with(['paciente.usuario', 'actividad', 'tratamiento'])
                                 ->withCount(['reacciones', 'comentarios', 'favoritos'])
                                 ->whereIn('id_publicacion', $favoritosIds->toArray())
                                 ->aprobadas(); // Solo mostrar aprobadas (importante: solo se muestran favoritos de publicaciones aprobadas)

        // Filtros de búsqueda
        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_publicacion', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_publicacion', '<=', $request->fecha_hasta);
        }

        // Filtro por etiqueta
        if ($request->filled('etiqueta')) {
            $query->where('etiquetas', 'like', '%' . $request->etiqueta . '%');
        }

        // Ordenamiento
        $orden = $request->get('orden', 'fecha');
        if ($orden == 'fecha') {
            $query->porFecha('desc');
        } elseif ($orden == 'relevancia') {
            $query->porRelevancia();
        } elseif ($orden == 'popularidad') {
            $query->orderByRaw('(SELECT COUNT(*) FROM reacciones_foro WHERE reacciones_foro.id_publicacion = publicaciones_foro.id_publicacion) DESC')
                  ->orderBy('fecha_publicacion', 'desc');
        } else {
            $query->porFecha('desc');
        }

        $publicaciones = $query->paginate(15);

        // Estadísticas de favoritos
        $stats = [
            'total_favoritos' => $favoritosIds->count(),
            'total_publicaciones' => PublicacionForo::aprobadas()->count(),
        ];

        // Opciones para filtros
        $etiquetas = PublicacionForo::whereIn('id_publicacion', $favoritosIds)
                                   ->whereNotNull('etiquetas')
                                   ->where('etiquetas', '!=', '')
                                   ->selectRaw('DISTINCT etiquetas')
                                   ->get()
                                   ->flatMap(function($pub) {
                                       return explode(',', $pub->etiquetas);
                                   })
                                   ->map(function($tag) {
                                       return trim($tag);
                                   })
                                   ->filter()
                                   ->unique()
                                   ->sort()
                                   ->values();

        return view('foro.favoritos', compact('publicaciones', 'stats', 'etiquetas'));
    }
}
