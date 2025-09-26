<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actividad;
use App\Models\Paciente;
use App\Models\Medico;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $query = Actividad::with(['paciente.usuario', 'medico.usuario']);
        
        // Filtro por médico (solo para médicos)
        if ($user->isMedico()) {
            $query->where('id_medico', $user->medico->id_medico);
        }
        
        // Filtros de búsqueda
        if ($request->filled('paciente')) {
            $query->whereHas('paciente.usuario', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->paciente . '%')
                  ->orWhere('apPaterno', 'like', '%' . $request->paciente . '%')
                  ->orWhere('apMaterno', 'like', '%' . $request->paciente . '%')
                  ->orWhere('correo', 'like', '%' . $request->paciente . '%');
            });
        }
        
        if ($request->filled('estado')) {
            if ($request->estado == '1') {
                $query->where('completada', true);
            } elseif ($request->estado == '0') {
                $query->where('completada', false);
            } elseif ($request->estado == 'vencida') {
                $query->where('completada', false)
                      ->where('fecha_limite', '<', now()->toDateString());
            }
        }
        
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_asignacion', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_asignacion', '<=', $request->fecha_hasta);
        }
        
        if ($request->filled('periodicidad')) {
            $query->where('periodicidad', $request->periodicidad);
        }
        
        if ($request->filled('tipo_actividad')) {
            $query->where('nombre', 'like', '%' . $request->tipo_actividad . '%');
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort_by', 'fecha_asignacion');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $actividades = $query->paginate(15);
        
        // Estadísticas
        $stats = $this->getActivityStats($user);
        
        // Opciones para filtros
        $periodicidades = Actividad::distinct()->pluck('periodicidad')->filter();
        $tiposActividad = Actividad::distinct()->pluck('nombre')->take(10);
        
        return view('actividades.index', compact('actividades', 'stats', 'periodicidades', 'tiposActividad'));
    }
    
    /**
     * Obtener estadísticas de actividades
     */
    private function getActivityStats($user)
    {
        $query = Actividad::query();
        
        if ($user->isMedico()) {
            $query->where('id_medico', $user->medico->id_medico);
        }
        
        $total = $query->count();
        $completadas = $query->where('completada', true)->count();
        $pendientes = $query->where('completada', false)->count();
        $vencidas = $query->where('completada', false)
                         ->where('fecha_limite', '<', now()->toDateString())
                         ->count();
        
        return [
            'total' => $total,
            'completadas' => $completadas,
            'pendientes' => $pendientes,
            'vencidas' => $vencidas,
            'porcentaje_cumplimiento' => $total > 0 ? round(($completadas / $total) * 100, 1) : 0
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden crear actividades.');
        }

        $pacientes = Paciente::with('usuario')->get();
        
        return view('actividades.create', compact('pacientes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden crear actividades.');
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'instrucciones' => 'nullable|string',
            'fecha_asignacion' => 'required|date',
            'fecha_limite' => 'required|date|after_or_equal:fecha_asignacion',
            'periodicidad' => 'required|string|max:50',
            'id_paciente' => 'required|exists:pacientes,id_paciente',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Para administradores, usar médico por defecto (ID 1)
        $id_medico = $user->isMedico() ? $user->medico->id_medico : 1;

        $actividad = Actividad::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'instrucciones' => $request->instrucciones,
            'fecha_asignacion' => $request->fecha_asignacion,
            'fecha_limite' => $request->fecha_limite,
            'periodicidad' => $request->periodicidad,
            'id_paciente' => $request->id_paciente,
            'id_medico' => $id_medico,
            'completada' => false,
        ]);

        return redirect()->route($user->isAdmin() ? 'admin.actividades.index' : 'medico.actividades.index')->with('success', 'Actividad creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $actividad = Actividad::with(['paciente.usuario', 'medico.usuario'])->findOrFail($id);
        
        return view('actividades.show', compact('actividad'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden editar actividades.');
        }

        $actividad = Actividad::findOrFail($id);
        
        // Los médicos solo pueden editar sus propias actividades
        if ($user->isMedico() && $actividad->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.actividades.index' : 'medico.actividades.index')->with('error', 'No tienes permisos para editar esta actividad.');
        }

        $pacientes = Paciente::with('usuario')->get();
        
        return view('actividades.edit', compact('actividad', 'pacientes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden editar actividades.');
        }

        $actividad = Actividad::findOrFail($id);
        
        // Los médicos solo pueden editar sus propias actividades
        if ($user->isMedico() && $actividad->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.actividades.index' : 'medico.actividades.index')->with('error', 'No tienes permisos para editar esta actividad.');
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'instrucciones' => 'nullable|string',
            'fecha_asignacion' => 'required|date',
            'fecha_limite' => 'required|date|after_or_equal:fecha_asignacion',
            'periodicidad' => 'required|string|max:50',
            'id_paciente' => 'required|exists:pacientes,id_paciente',
            'completada' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $actividad->update($request->all());

        return redirect()->route($user->isAdmin() ? 'admin.actividades.index' : 'medico.actividades.index')->with('success', 'Actividad actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route($user->isAdmin() ? 'admin.actividades.index' : 'medico.actividades.index')->with('error', 'Solo los médicos y administradores pueden eliminar actividades.');
        }

        $actividad = Actividad::findOrFail($id);
        
        // Los médicos solo pueden eliminar sus propias actividades
        if ($user->isMedico() && $actividad->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.actividades.index' : 'medico.actividades.index')->with('error', 'No tienes permisos para eliminar esta actividad.');
        }

        $actividad->delete();

        return redirect()->route($user->isAdmin() ? 'admin.actividades.index' : 'medico.actividades.index')->with('success', 'Actividad eliminada exitosamente.');
    }

    /**
     * Cambiar estado de completada de la actividad
     */
    public function toggleCompletada($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico()) {
            return redirect()->route($user->isAdmin() ? 'admin.actividades.index' : 'medico.actividades.index')->with('error', 'Solo los médicos pueden cambiar el estado de actividades.');
        }

        $actividad = Actividad::findOrFail($id);
        
        if ($actividad->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.actividades.index' : 'medico.actividades.index')->with('error', 'No tienes permisos para modificar esta actividad.');
        }

        $actividad->update(['completada' => !$actividad->completada]);

        $estado = $actividad->completada ? 'completada' : 'pendiente';
        return redirect()->route('medico.actividades.show', $actividad->id_actividad)->with('success', "Actividad marcada como {$estado} exitosamente.");
    }
    
    /**
     * Ver actividades por paciente
     */
    public function porPaciente($idPaciente, Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }
        
        $paciente = Paciente::with('usuario')->findOrFail($idPaciente);
        
        $query = Actividad::with(['paciente.usuario', 'medico.usuario'])
            ->where('id_paciente', $idPaciente);
        
        // Filtro por médico (solo para médicos)
        if ($user->isMedico()) {
            $query->where('id_medico', $user->medico->id_medico);
        }
        
        // Filtros adicionales
        if ($request->filled('estado')) {
            if ($request->estado == '1') {
                $query->where('completada', true);
            } elseif ($request->estado == '0') {
                $query->where('completada', false);
            } elseif ($request->estado == 'vencida') {
                $query->where('completada', false)
                      ->where('fecha_limite', '<', now()->toDateString());
            }
        }
        
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_asignacion', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_asignacion', '<=', $request->fecha_hasta);
        }
        
        $actividades = $query->orderBy('fecha_asignacion', 'desc')->paginate(15);
        
        // Estadísticas del paciente
        $statsPaciente = $this->getPatientActivityStats($idPaciente, $user);
        
        return view('actividades.por-paciente', compact('actividades', 'paciente', 'statsPaciente'));
    }
    
    /**
     * Obtener estadísticas de actividades por paciente
     */
    private function getPatientActivityStats($idPaciente, $user)
    {
        $query = Actividad::where('id_paciente', $idPaciente);
        
        if ($user->isMedico()) {
            $query->where('id_medico', $user->medico->id_medico);
        }
        
        $total = $query->count();
        $completadas = $query->where('completada', true)->count();
        $pendientes = $query->where('completada', false)->count();
        $vencidas = $query->where('completada', false)
                         ->where('fecha_limite', '<', now()->toDateString())
                         ->count();
        
        return [
            'total' => $total,
            'completadas' => $completadas,
            'pendientes' => $pendientes,
            'vencidas' => $vencidas,
            'porcentaje_cumplimiento' => $total > 0 ? round(($completadas / $total) * 100, 1) : 0
        ];
    }
    
    /**
     * Vista de actividades para pacientes
     */
    public function paciente(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }
        
        $query = Actividad::with(['medico.usuario'])
            ->where('id_paciente', $user->paciente->id_paciente);
        
        // Filtros
        if ($request->filled('estado')) {
            if ($request->estado == '1') {
                $query->where('completada', true);
            } elseif ($request->estado == '0') {
                $query->where('completada', false);
            } elseif ($request->estado == 'vencida') {
                $query->where('completada', false)
                      ->where('fecha_limite', '<', now()->toDateString());
            }
        }
        
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_asignacion', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_asignacion', '<=', $request->fecha_hasta);
        }
        
        $actividades = $query->orderBy('fecha_asignacion', 'desc')->paginate(15);
        
        // Estadísticas del paciente
        $statsPaciente = $this->getPatientActivityStats($user->paciente->id_paciente, $user);
        
        return view('paciente.actividades.index', compact('actividades', 'statsPaciente'));
    }
    
    /**
     * Vista de detalles de actividad para pacientes
     */
    public function pacienteShow($id)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }
        
        $actividad = Actividad::with(['medico.usuario'])
            ->where('id_paciente', $user->paciente->id_paciente)
            ->findOrFail($id);
        
        return view('paciente.actividades.show', compact('actividad'));
    }
    
    /**
     * Marcar actividad como completada (para pacientes)
     */
    public function marcarCompletada($id, Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('paciente.actividades.index')->with('error', 'No tienes permisos para realizar esta acción.');
        }
        
        $actividad = Actividad::where('id_paciente', $user->paciente->id_paciente)
            ->findOrFail($id);
        
        $actividad->update(['completada' => true]);
        
        return redirect()->route('paciente.actividades.show', $actividad->id_actividad)->with('success', 'Actividad marcada como completada exitosamente.');
    }
    
    /**
     * Agregar comentario a actividad (para pacientes)
     */
    public function agregarComentario($id, Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('paciente.actividades.index')->with('error', 'No tienes permisos para realizar esta acción.');
        }
        
        $request->validate([
            'comentario' => 'required|string|max:1000'
        ]);
        
        $actividad = Actividad::where('id_paciente', $user->paciente->id_paciente)
            ->findOrFail($id);
        
        $actividad->update([
            'comentarios_paciente' => $request->comentario
        ]);
        
        return redirect()->route('paciente.actividades.show', $actividad->id_actividad)->with('success', 'Comentario agregado exitosamente.');
    }
}
