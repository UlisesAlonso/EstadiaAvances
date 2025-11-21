<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\Paciente;
use App\Models\Diagnostico;
use App\Models\Tratamiento;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PreguntaController extends Controller
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

        $query = Pregunta::with(['paciente.usuario', 'medico.usuario', 'diagnostico', 'tratamiento']);
        
        // Filtros
        if ($request->filled('paciente')) {
            $query->whereHas('paciente.usuario', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->paciente . '%')
                  ->orWhere('apPaterno', 'like', '%' . $request->paciente . '%')
                  ->orWhere('apMaterno', 'like', '%' . $request->paciente . '%');
            });
        }
        
        if ($request->filled('especialidad')) {
            $query->where('especialidad_medica', $request->especialidad);
        }
        
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        
        if ($request->filled('diagnostico')) {
            $query->where('id_diagnostico', $request->diagnostico);
        }
        
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_asignacion', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_asignacion', '<=', $request->fecha_hasta);
        }
        
        if ($request->filled('estado')) {
            $query->where('activa', $request->estado == '1');
        }

        // Médicos solo ven sus preguntas, administradores ven todas
        if ($user->isMedico() && $user->medico) {
            $query->where(function($q) use ($user) {
                $q->where('id_medico', $user->medico->id_medico)
                  ->orWhereNull('id_medico');
            });
        }
        
        $preguntas = $query->orderBy('fecha_asignacion', 'desc')
                          ->orderBy('fecha_creacion', 'desc')
                          ->paginate(15);
        
        // Estadísticas
        $stats = $this->getStats($user);
        
        // Opciones para filtros
        $especialidades = Pregunta::distinct()->pluck('especialidad_medica')->filter();
        $diagnosticos = Diagnostico::with('catalogoDiagnostico')->get();
        
        return view('preguntas.index', compact('preguntas', 'stats', 'especialidades', 'diagnosticos'));
    }

    /**
     * Obtener estadísticas de preguntas
     */
    private function getStats($user)
    {
        $query = Pregunta::query();
        
        if ($user->isMedico() && $user->medico) {
            $query->where(function($q) use ($user) {
                $q->where('id_medico', $user->medico->id_medico)
                  ->orWhereNull('id_medico');
            });
        }
        
        $total = $query->count();
        $activas = $query->where('activa', true)->count();
        $abiertas = $query->where('tipo', 'abierta')->count();
        $opcionMultiple = $query->where('tipo', 'opcion_multiple')->count();
        $conRespuestas = $query->whereHas('respuestas')->count();
        
        return [
            'total' => $total,
            'activas' => $activas,
            'abiertas' => $abiertas,
            'opcion_multiple' => $opcionMultiple,
            'con_respuestas' => $conRespuestas,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden crear preguntas.');
        }

        $pacientes = Paciente::with('usuario')->get();
        $diagnosticos = Diagnostico::with('catalogoDiagnostico')->get();
        $tratamientos = Tratamiento::with(['paciente.usuario', 'medico.usuario'])->get();
        $especialidades = Medico::distinct()->pluck('especialidad')->filter();
        
        return view('preguntas.create', compact('pacientes', 'diagnosticos', 'tratamientos', 'especialidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden crear preguntas.');
        }

        // Si el tipo es abierta, eliminar opciones_multiple del request para evitar validación
        if ($request->tipo === 'abierta') {
            $request->merge(['opciones_multiple' => null]);
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:2000',
            'tipo' => 'required|in:abierta,opcion_multiple',
            'opciones_multiple' => 'required_if:tipo,opcion_multiple|nullable|array|min:2',
            'opciones_multiple.*' => 'required_if:tipo,opcion_multiple|nullable|string|max:255',
            'especialidad_medica' => 'nullable|string|max:100',
            'fecha_asignacion' => 'required|date',
            'id_diagnostico' => 'nullable|exists:diagnosticos,id_diagnostico',
            'id_tratamiento' => 'nullable|exists:tratamientos,id_tratamiento',
            'id_paciente' => 'nullable|exists:pacientes,id_paciente',
        ], [
            'descripcion.required' => 'La descripción de la pregunta es obligatoria.',
            'tipo.required' => 'El tipo de pregunta es obligatorio.',
            'tipo.in' => 'El tipo debe ser abierta u opción múltiple.',
            'opciones_multiple.required_if' => 'Debe proporcionar al menos 2 opciones para preguntas de opción múltiple.',
            'opciones_multiple.min' => 'Debe proporcionar al menos 2 opciones.',
            'opciones_multiple.*.required_if' => 'Cada opción es obligatoria para preguntas de opción múltiple.',
            'fecha_asignacion.required' => 'La fecha de asignación es obligatoria.',
            'fecha_asignacion.date' => 'La fecha de asignación debe ser una fecha válida.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Determinar el médico
        $id_medico = null;
        if ($user->isMedico() && $user->medico) {
            $id_medico = $user->medico->id_medico;
        } elseif ($user->isAdmin()) {
            // Para administradores, usar el médico del diagnóstico o tratamiento, o el primero disponible
            if ($request->id_diagnostico) {
                $diagnostico = Diagnostico::find($request->id_diagnostico);
                $id_medico = $diagnostico ? $diagnostico->id_medico : null;
            } elseif ($request->id_tratamiento) {
                $tratamiento = Tratamiento::find($request->id_tratamiento);
                $id_medico = $tratamiento ? $tratamiento->id_medico : null;
            }
            
            // Si aún no hay médico, obtener el primero disponible
            if ($id_medico === null) {
                $primerMedico = Medico::first();
                if ($primerMedico) {
                    $id_medico = $primerMedico->id_medico;
                }
            }
        }
        
        // Validar que se tenga un médico válido
        if ($id_medico === null) {
            return redirect()->back()
                            ->withErrors(['error' => 'No se pudo determinar el médico asignado. Por favor, asegúrate de que exista al menos un médico en el sistema o selecciona un diagnóstico/tratamiento vinculado.'])
                            ->withInput();
        }

        // Procesar opciones múltiples
        $opciones = null;
        if ($request->tipo === 'opcion_multiple' && $request->has('opciones_multiple')) {
            $opciones = $request->opciones_multiple;
        }

        $pregunta = Pregunta::create([
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            'opciones_multiple' => $opciones,
            'especialidad_medica' => $request->especialidad_medica,
            'fecha_asignacion' => $request->fecha_asignacion,
            'id_diagnostico' => $request->id_diagnostico,
            'id_tratamiento' => $request->id_tratamiento,
            'id_paciente' => $request->id_paciente,
            'id_medico' => $id_medico,
            'fecha_creacion' => now(),
            'activa' => true,
        ]);

        $route = $user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index';
        return redirect()->route($route)->with('success', 'Pregunta creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $pregunta = Pregunta::with([
            'paciente.usuario', 
            'medico.usuario', 
            'diagnostico.catalogoDiagnostico', 
            'tratamiento',
            'respuestas.usuario'
        ])->findOrFail($id);

        // Verificar permisos
        if ($user->isMedico() && $user->medico && $pregunta->id_medico !== $user->medico->id_medico) {
            return redirect()->route('medico.preguntas.index')
                            ->with('error', 'No tienes permisos para ver esta pregunta.');
        }

        // Obtener respuestas con información del paciente
        $respuestas = Respuesta::with('usuario')
            ->where('id_pregunta', $id)
            ->orderBy('fecha_respuesta', 'desc')
            ->get();

        return view('preguntas.show', compact('pregunta', 'respuestas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden editar preguntas.');
        }

        $pregunta = Pregunta::findOrFail($id);
        
        // Verificar permisos
        if ($user->isMedico() && $user->medico && $pregunta->id_medico !== $user->medico->id_medico) {
            return redirect()->route('medico.preguntas.index')
                            ->with('error', 'No tienes permisos para editar esta pregunta.');
        }

        // Verificar si tiene respuestas consolidadas
        if ($pregunta->hasRespuestas()) {
            return redirect()->route($user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index')
                            ->with('error', 'No se puede editar una pregunta que ya tiene respuestas.');
        }

        $pacientes = Paciente::with('usuario')->get();
        $diagnosticos = Diagnostico::with('catalogoDiagnostico')->get();
        $tratamientos = Tratamiento::with(['paciente.usuario', 'medico.usuario'])->get();
        $especialidades = Medico::distinct()->pluck('especialidad')->filter();
        
        return view('preguntas.edit', compact('pregunta', 'pacientes', 'diagnosticos', 'tratamientos', 'especialidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden editar preguntas.');
        }

        $pregunta = Pregunta::findOrFail($id);
        
        // Verificar permisos
        if ($user->isMedico() && $user->medico && $pregunta->id_medico !== $user->medico->id_medico) {
            return redirect()->route('medico.preguntas.index')
                            ->with('error', 'No tienes permisos para editar esta pregunta.');
        }

        // Verificar si tiene respuestas consolidadas
        if ($pregunta->hasRespuestas()) {
            return redirect()->route($user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index')
                            ->with('error', 'No se puede editar una pregunta que ya tiene respuestas.');
        }

        // Si el tipo es abierta, eliminar opciones_multiple del request para evitar validación
        if ($request->tipo === 'abierta') {
            $request->merge(['opciones_multiple' => null]);
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:2000',
            'tipo' => 'required|in:abierta,opcion_multiple',
            'opciones_multiple' => 'required_if:tipo,opcion_multiple|nullable|array|min:2',
            'opciones_multiple.*' => 'required_if:tipo,opcion_multiple|nullable|string|max:255',
            'especialidad_medica' => 'nullable|string|max:100',
            'fecha_asignacion' => 'required|date',
            'id_diagnostico' => 'nullable|exists:diagnosticos,id_diagnostico',
            'id_tratamiento' => 'nullable|exists:tratamientos,id_tratamiento',
            'id_paciente' => 'nullable|exists:pacientes,id_paciente',
        ], [
            'descripcion.required' => 'La descripción de la pregunta es obligatoria.',
            'tipo.required' => 'El tipo de pregunta es obligatorio.',
            'tipo.in' => 'El tipo debe ser abierta u opción múltiple.',
            'opciones_multiple.required_if' => 'Debe proporcionar al menos 2 opciones para preguntas de opción múltiple.',
            'opciones_multiple.min' => 'Debe proporcionar al menos 2 opciones.',
            'opciones_multiple.*.required_if' => 'Cada opción es obligatoria para preguntas de opción múltiple.',
            'fecha_asignacion.required' => 'La fecha de asignación es obligatoria.',
            'fecha_asignacion.date' => 'La fecha de asignación debe ser una fecha válida.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Procesar opciones múltiples
        $opciones = null;
        if ($request->tipo === 'opcion_multiple' && $request->has('opciones_multiple')) {
            $opciones = $request->opciones_multiple;
        }

        $pregunta->update([
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            'opciones_multiple' => $opciones,
            'especialidad_medica' => $request->especialidad_medica,
            'fecha_asignacion' => $request->fecha_asignacion,
            'id_diagnostico' => $request->id_diagnostico,
            'id_tratamiento' => $request->id_tratamiento,
            'id_paciente' => $request->id_paciente,
        ]);

        $route = $user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index';
        return redirect()->route($route)->with('success', 'Pregunta actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden eliminar preguntas.');
        }

        $pregunta = Pregunta::findOrFail($id);
        
        // Verificar permisos
        if ($user->isMedico() && $user->medico && $pregunta->id_medico !== $user->medico->id_medico) {
            return redirect()->route('medico.preguntas.index')
                            ->with('error', 'No tienes permisos para eliminar esta pregunta.');
        }

        // Verificar si tiene respuestas consolidadas
        if ($pregunta->hasRespuestas()) {
            return redirect()->route($user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index')
                            ->with('error', 'No se puede eliminar una pregunta que ya tiene respuestas clínicas consolidadas.');
        }

        $pregunta->delete();

        $route = $user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index';
        return redirect()->route($route)->with('success', 'Pregunta eliminada exitosamente.');
    }

    /**
     * Vista de preguntas para pacientes
     */
    public function paciente(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        $query = Pregunta::with(['medico.usuario', 'diagnostico', 'tratamiento'])
            ->porPaciente($user->paciente->id_paciente)
            ->where('activa', true)
            ->where('fecha_asignacion', '<=', now()->toDateString());

        // Filtros
        if ($request->filled('estado')) {
            if ($request->estado == 'respondidas') {
                $query->whereHas('respuestas', function($q) use ($user) {
                    $q->where('id_usuario', $user->id_usuario);
                });
            } elseif ($request->estado == 'pendientes') {
                $query->whereDoesntHave('respuestas', function($q) use ($user) {
                    $q->where('id_usuario', $user->id_usuario);
                });
            }
        }

        if ($request->filled('especialidad')) {
            $query->where('especialidad_medica', $request->especialidad);
        }

        $preguntas = $query->orderBy('fecha_asignacion', 'desc')
                          ->paginate(15);

        // Cargar las respuestas del paciente para cada pregunta
        $preguntas->getCollection()->transform(function($pregunta) use ($user) {
            $pregunta->yaRespondida = $pregunta->hasRespuestaByUsuario($user->id_usuario);
            $pregunta->respuestaUsuario = $pregunta->getRespuestaByUsuario($user->id_usuario);
            return $pregunta;
        });

        // Estadísticas del paciente
        $totalPreguntas = Pregunta::porPaciente($user->paciente->id_paciente)
            ->where('activa', true)
            ->where('fecha_asignacion', '<=', now()->toDateString())
            ->count();
        
        $respondidas = Pregunta::porPaciente($user->paciente->id_paciente)
            ->where('activa', true)
            ->whereHas('respuestas', function($q) use ($user) {
                $q->where('id_usuario', $user->id_usuario);
            })
            ->count();

        $stats = [
            'total' => $totalPreguntas,
            'respondidas' => $respondidas,
            'pendientes' => $totalPreguntas - $respondidas,
        ];

        $especialidades = Pregunta::porPaciente($user->paciente->id_paciente)
            ->where('activa', true)
            ->distinct()
            ->pluck('especialidad_medica')
            ->filter();

        return view('paciente.preguntas.index', compact('preguntas', 'stats', 'especialidades', 'user'));
    }

    /**
     * Vista de detalles de pregunta para pacientes
     */
    public function pacienteShow($id)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        $pregunta = Pregunta::with(['medico.usuario', 'diagnostico', 'tratamiento'])
            ->porPaciente($user->paciente->id_paciente)
            ->where('activa', true)
            ->findOrFail($id);

        // Verificar si ya tiene respuesta
        $respuesta = Respuesta::where('id_pregunta', $id)
            ->where('id_usuario', $user->id_usuario)
            ->first();

        return view('paciente.preguntas.show', compact('pregunta', 'respuesta'));
    }

    /**
     * Responder pregunta (para pacientes)
     */
    public function responder(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('paciente.preguntas.index')
                            ->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $pregunta = Pregunta::porPaciente($user->paciente->id_paciente)
            ->where('activa', true)
            ->findOrFail($id);

        // Verificar si ya tiene respuesta
        $respuestaExistente = Respuesta::where('id_pregunta', $id)
            ->where('id_usuario', $user->id_usuario)
            ->first();

        if ($respuestaExistente) {
            return redirect()->route('paciente.preguntas.show', $id)
                            ->with('error', 'Ya has respondido esta pregunta.');
        }

        $validator = Validator::make($request->all(), [
            'respuesta' => 'required|string|max:2000',
            'cumplimiento' => 'nullable|boolean',
        ], [
            'respuesta.required' => 'Debe proporcionar una respuesta.',
            'respuesta.max' => 'La respuesta no puede exceder 2000 caracteres.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validar respuesta si es opción múltiple
        if ($pregunta->esOpcionMultiple() && $pregunta->opciones_multiple) {
            if (!in_array($request->respuesta, $pregunta->opciones_multiple)) {
                return redirect()->back()
                                ->withErrors(['respuesta' => 'La respuesta seleccionada no es válida.'])
                                ->withInput();
            }
        }

        Respuesta::create([
            'id_pregunta' => $id,
            'id_usuario' => $user->id_usuario,
            'respuesta' => $request->respuesta,
            'fecha_respuesta' => now(),
            'cumplimiento' => $request->cumplimiento ?? false,
        ]);

        return redirect()->route('paciente.preguntas.show', $id)
                        ->with('success', 'Respuesta registrada exitosamente.');
    }

    /**
     * Generar reporte de preguntas y respuestas
     */
    public function reporte(Request $request, $id_paciente = null)
    {
        $user = Auth::user();
        
        // Determinar el paciente para el reporte
        if ($id_paciente) {
            $paciente = Paciente::with('usuario')->findOrFail($id_paciente);
            
            // Verificar permisos
            if ($user->isPaciente() && $paciente->id_paciente !== $user->paciente->id_paciente) {
                return redirect()->route('paciente.preguntas.index')
                                ->with('error', 'No tienes permisos para ver este reporte.');
            }
        } else {
            if ($user->isPaciente()) {
                $paciente = $user->paciente;
            } else {
                return redirect()->route($user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index')
                                ->with('error', 'Debe especificar un paciente para el reporte.');
            }
        }

        // Obtener preguntas del paciente
        $query = Pregunta::with(['medico.usuario', 'diagnostico', 'tratamiento', 'respuestas.usuario'])
            ->porPaciente($paciente->id_paciente);

        // Filtros opcionales
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_asignacion', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_asignacion', '<=', $request->fecha_hasta);
        }
        
        if ($request->filled('cumplimiento')) {
            $query->whereHas('respuestas', function($q) use ($request, $paciente) {
                $q->where('id_usuario', $paciente->usuario->id_usuario)
                  ->where('cumplimiento', $request->cumplimiento == '1');
            });
        }

        $preguntas = $query->orderBy('fecha_asignacion', 'desc')->get();

        return view('preguntas.reporte', compact('paciente', 'preguntas'));
    }
}

