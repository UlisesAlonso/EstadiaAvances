<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Diagnostico;
use App\Models\Tratamiento;
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
        
        if ($user->isMedico()) {
            // Médicos ven sus preguntas
            $query = Pregunta::with(['paciente.usuario', 'medico.usuario', 'diagnostico', 'tratamiento'])
                ->where('id_medico', $user->medico->id_medico);
            
            // Filtros
            if ($request->filled('paciente')) {
                $query->whereHas('paciente.usuario', function($q) use ($request) {
                    $q->where('nombre', 'like', '%' . $request->paciente . '%')
                      ->orWhere('apPaterno', 'like', '%' . $request->paciente . '%')
                      ->orWhere('apMaterno', 'like', '%' . $request->paciente . '%');
                });
            }
            
            if ($request->filled('tipo')) {
                $query->where('tipo', $request->tipo);
            }
            
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha_asignacion', '>=', $request->fecha_desde);
            }
            
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha_asignacion', '<=', $request->fecha_hasta);
            }
            
            if ($request->filled('id_diagnostico')) {
                $query->where('id_diagnostico', $request->id_diagnostico);
            }
            
            if ($request->filled('especialidad')) {
                $query->where('especialidad_medica', $request->especialidad);
            }
            
            $preguntas = $query->orderBy('fecha_asignacion', 'desc')
                ->orderBy('fecha_creacion', 'desc')
                ->paginate(15);
                
        } elseif ($user->isAdmin()) {
            // Administradores ven todas las preguntas
            $query = Pregunta::with(['paciente.usuario', 'medico.usuario', 'diagnostico', 'tratamiento']);
            
            // Filtros
            if ($request->filled('paciente')) {
                $query->whereHas('paciente.usuario', function($q) use ($request) {
                    $q->where('nombre', 'like', '%' . $request->paciente . '%')
                      ->orWhere('apPaterno', 'like', '%' . $request->paciente . '%')
                      ->orWhere('apMaterno', 'like', '%' . $request->paciente . '%');
                });
            }
            
            if ($request->filled('tipo')) {
                $query->where('tipo', $request->tipo);
            }
            
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha_asignacion', '>=', $request->fecha_desde);
            }
            
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha_asignacion', '<=', $request->fecha_hasta);
            }
            
            if ($request->filled('id_diagnostico')) {
                $query->where('id_diagnostico', $request->id_diagnostico);
            }
            
            if ($request->filled('especialidad')) {
                $query->where('especialidad_medica', $request->especialidad);
            }
            
            $preguntas = $query->orderBy('fecha_asignacion', 'desc')
                ->orderBy('fecha_creacion', 'desc')
                ->paginate(15);
        } else {
            // Pacientes ven sus preguntas asignadas
            $query = Pregunta::with(['medico.usuario', 'diagnostico', 'tratamiento'])
                ->where('id_paciente', $user->paciente->id_paciente)
                ->where('estado', 'activa');
            
            // Filtros para pacientes
            if ($request->filled('tipo')) {
                $query->where('tipo', $request->tipo);
            }
            
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha_asignacion', '>=', $request->fecha_desde);
            }
            
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha_asignacion', '<=', $request->fecha_hasta);
            }
            
            $preguntas = $query->orderBy('fecha_asignacion', 'desc')
                ->paginate(15);
        }

        // Datos para filtros
        $diagnosticos = null;
        $especialidades = null;
        if ($user->isMedico() || $user->isAdmin()) {
            $diagnosticos = Diagnostico::with('catalogoDiagnostico')->get();
            $especialidades = Pregunta::distinct()->pluck('especialidad_medica')->filter();
        }

        return view('preguntas.index', compact('preguntas', 'diagnosticos', 'especialidades'));
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
        
        // Obtener especialidades médicas de los médicos
        $especialidades = Medico::distinct()->pluck('especialidad')->filter();

        return view('preguntas.create', compact('pacientes', 'especialidades'));
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

        $validator = Validator::make($request->all(), [
            'texto' => 'required|string|max:1000',
            'descripcion' => 'nullable|string|max:2000',
            'tipo' => 'required|in:abierta,opcion_multiple',
            'categoria' => 'required|string|max:100',
            'especialidad_medica' => 'required|string|max:100',
            'fecha_asignacion' => 'required|date',
            'id_paciente' => 'nullable|exists:pacientes,id_paciente',
            'opciones' => 'required_if:tipo,opcion_multiple|nullable|array',
            'opciones.*' => 'nullable|string|max:255',
        ], [
            'texto.required' => 'El texto de la pregunta es obligatorio.',
            'tipo.required' => 'El tipo de pregunta es obligatorio.',
            'tipo.in' => 'El tipo debe ser "abierta" u "opcion_multiple".',
            'categoria.required' => 'La categoría es obligatoria.',
            'especialidad_medica.required' => 'La especialidad médica es obligatoria.',
            'fecha_asignacion.required' => 'La fecha de asignación es obligatoria.',
            'opciones.required_if' => 'Debe proporcionar al menos una opción para preguntas de opción múltiple.',
        ]);

        // Filtrar opciones vacías si es pregunta de opción múltiple
        $opciones = null;
        if ($request->tipo === 'opcion_multiple' && $request->has('opciones')) {
            $opciones = array_filter($request->opciones, function($opcion) {
                return !empty(trim($opcion));
            });
            $opciones = array_values($opciones); // Reindexar el array
            
            // Validar que haya al menos una opción
            if (empty($opciones)) {
                return redirect()->back()
                    ->withErrors(['opciones' => 'Debe proporcionar al menos una opción para preguntas de opción múltiple.'])
                    ->withInput();
            }
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Determinar el médico
        $id_medico = null;
        if ($user->isMedico()) {
            $id_medico = $user->medico->id_medico;
        } else {
            // Para administradores, usar el primero disponible o null
            $medico = Medico::first();
            $id_medico = $medico ? $medico->id_medico : null;
        }

        $pregunta = Pregunta::create([
            'texto' => $request->texto,
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            'categoria' => $request->categoria,
            'especialidad_medica' => $request->especialidad_medica,
            'fecha_asignacion' => $request->fecha_asignacion,
            'id_diagnostico' => null,
            'id_tratamiento' => null,
            'id_paciente' => $request->id_paciente,
            'id_medico' => $id_medico,
            'opciones' => $opciones,
            'estado' => 'activa',
            'fecha_creacion' => now(),
        ]);

        return redirect()->route($user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index')
            ->with('success', 'Pregunta creada exitosamente.');
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
            'respuestas.paciente.usuario',
            'respuestas.usuario'
        ])->findOrFail($id);

        // Verificar permisos para pacientes
        if ($user->isPaciente()) {
            // Permitir si es su pregunta o si es una pregunta general (id_paciente null)
            if ($pregunta->id_paciente !== null && $pregunta->id_paciente !== $user->paciente->id_paciente) {
                return redirect()->route('paciente.preguntas.index')
                    ->with('error', 'No tienes permisos para ver esta pregunta.');
            }
        }

        if ($user->isMedico() && $pregunta->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index')
                ->with('error', 'No tienes permisos para ver esta pregunta.');
        }

        // Obtener todas las respuestas (para todos los usuarios)
        $todasLasRespuestas = $pregunta->respuestas()
            ->with(['paciente.usuario', 'usuario'])
            ->orderBy('fecha_hora', 'desc')
            ->orderBy('fecha', 'desc')
            ->get();

        return view('preguntas.show', compact('pregunta', 'todasLasRespuestas'));
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
        if ($user->isMedico() && $pregunta->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index')
                ->with('error', 'No tienes permisos para editar esta pregunta.');
        }

        $pacientes = Paciente::with('usuario')->get();
        $especialidades = Medico::distinct()->pluck('especialidad')->filter();

        return view('preguntas.edit', compact('pregunta', 'pacientes', 'especialidades'));
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
        if ($user->isMedico() && $pregunta->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index')
                ->with('error', 'No tienes permisos para editar esta pregunta.');
        }

        $validator = Validator::make($request->all(), [
            'texto' => 'required|string|max:1000',
            'descripcion' => 'nullable|string|max:2000',
            'tipo' => 'required|in:abierta,opcion_multiple',
            'categoria' => 'required|string|max:100',
            'especialidad_medica' => 'required|string|max:100',
            'fecha_asignacion' => 'required|date',
            'id_paciente' => 'nullable|exists:pacientes,id_paciente',
            'opciones' => 'required_if:tipo,opcion_multiple|nullable|array',
            'opciones.*' => 'nullable|string|max:255',
            'estado' => 'required|in:activa,inactiva',
        ], [
            'texto.required' => 'El texto de la pregunta es obligatorio.',
            'tipo.required' => 'El tipo de pregunta es obligatorio.',
            'tipo.in' => 'El tipo debe ser "abierta" u "opcion_multiple".',
            'categoria.required' => 'La categoría es obligatoria.',
            'especialidad_medica.required' => 'La especialidad médica es obligatoria.',
            'fecha_asignacion.required' => 'La fecha de asignación es obligatoria.',
            'opciones.required_if' => 'Debe proporcionar al menos una opción para preguntas de opción múltiple.',
            'opciones.*.required' => 'Cada opción es obligatoria.',
            'estado.required' => 'El estado es obligatorio.',
        ]);

        // Filtrar opciones vacías si es pregunta de opción múltiple
        $opciones = null;
        if ($request->tipo === 'opcion_multiple' && $request->has('opciones')) {
            $opciones = array_filter($request->opciones, function($opcion) {
                return !empty(trim($opcion));
            });
            $opciones = array_values($opciones); // Reindexar el array
            
            // Validar que haya al menos una opción
            if (empty($opciones)) {
                return redirect()->back()
                    ->withErrors(['opciones' => 'Debe proporcionar al menos una opción para preguntas de opción múltiple.'])
                    ->withInput();
            }
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $pregunta->update([
            'texto' => $request->texto,
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            'categoria' => $request->categoria,
            'especialidad_medica' => $request->especialidad_medica,
            'fecha_asignacion' => $request->fecha_asignacion,
            'id_diagnostico' => null,
            'id_tratamiento' => null,
            'id_paciente' => $request->id_paciente,
            'opciones' => $opciones,
            'estado' => $request->estado,
        ]);

        return redirect()->route($user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index')
            ->with('success', 'Pregunta actualizada exitosamente.');
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
        if ($user->isMedico() && $pregunta->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index')
                ->with('error', 'No tienes permisos para eliminar esta pregunta.');
        }

        // Eliminar la pregunta (las respuestas se eliminarán automáticamente por cascade)
        $pregunta->delete();

        return redirect()->route($user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index')
            ->with('success', 'Pregunta eliminada exitosamente.');
    }

    /**
     * Vista de preguntas para pacientes
     */
    public function paciente(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $query = Pregunta::with(['medico.usuario', 'diagnostico', 'tratamiento'])
            ->where(function($q) use ($user) {
                $q->where('id_paciente', $user->paciente->id_paciente)
                  ->orWhereNull('id_paciente'); // Preguntas generales (sin paciente específico)
            })
            ->where('estado', 'activa');
        
        // Filtros
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_asignacion', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_asignacion', '<=', $request->fecha_hasta);
        }

        $preguntas = $query->orderBy('fecha_asignacion', 'desc')->paginate(15);

        return view('paciente.preguntas.index', compact('preguntas'));
    }

    /**
     * Mostrar pregunta para responder (paciente)
     */
    public function pacienteShow($id)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $pregunta = Pregunta::with(['medico.usuario', 'diagnostico', 'tratamiento'])
            ->where(function($q) use ($user) {
                $q->where('id_paciente', $user->paciente->id_paciente)
                  ->orWhereNull('id_paciente'); // Preguntas generales (sin paciente específico)
            })
            ->where('estado', 'activa')
            ->findOrFail($id);

        // Cargar todas las respuestas de todos los pacientes (como comentarios públicos)
        $todasLasRespuestas = $pregunta->respuestas()
            ->with(['paciente.usuario'])
            ->orderBy('fecha_hora', 'desc')
            ->orderBy('fecha', 'desc')
            ->get();

        // Verificar si puede responder (máximo 3 respuestas en total)
        $puedeResponder = $pregunta->puedeResponder();
        $totalRespuestas = $todasLasRespuestas->count();

        return view('paciente.preguntas.show', compact('pregunta', 'puedeResponder', 'todasLasRespuestas', 'totalRespuestas'));
    }

    /**
     * Guardar respuesta del paciente
     */
    public function responder(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $pregunta = Pregunta::where(function($q) use ($user) {
                $q->where('id_paciente', $user->paciente->id_paciente)
                  ->orWhereNull('id_paciente'); // Preguntas generales (sin paciente específico)
            })
            ->where('estado', 'activa')
            ->findOrFail($id);

        // Verificar si puede responder (máximo 3 respuestas en total)
        if (!$pregunta->puedeResponder()) {
            return redirect()->route('paciente.preguntas.show', $id)
                ->with('error', 'Esta pregunta ya ha alcanzado el límite de 3 respuestas. No se pueden agregar más respuestas.');
        }

        $validator = Validator::make($request->all(), [
            'respuesta' => 'required|string|max:2000',
            'cumplimiento' => 'nullable|boolean',
        ], [
            'respuesta.required' => 'La respuesta es obligatoria.',
            'respuesta.max' => 'La respuesta no puede exceder 2000 caracteres.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validar que la respuesta sea una opción válida si es de opción múltiple
        if ($pregunta->tipo === 'opcion_multiple' && $pregunta->opciones) {
            if (!in_array($request->respuesta, $pregunta->opciones)) {
                return redirect()->back()
                    ->withErrors(['respuesta' => 'La respuesta seleccionada no es válida.'])
                    ->withInput();
            }
        }

        Respuesta::create([
            'id_pregunta' => $pregunta->id_pregunta,
            'id_usuario' => $user->id_usuario,
            'id_paciente' => $user->paciente->id_paciente,
            'respuesta' => $request->respuesta,
            'fecha' => now()->toDateString(),
            'fecha_hora' => now(),
            'cumplimiento' => $request->cumplimiento ?? false,
        ]);

        return redirect()->route('paciente.preguntas.show', $id)
            ->with('success', 'Respuesta guardada exitosamente.');
    }

    /**
     * Reporte de preguntas y respuestas
     */
    public function reporte(Request $request, $idPaciente = null)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para ver reportes.');
        }

        // Determinar el paciente
        if ($idPaciente) {
            $paciente = Paciente::with('usuario')->findOrFail($idPaciente);
        } else {
            if ($request->filled('id_paciente')) {
                $paciente = Paciente::with('usuario')->findOrFail($request->id_paciente);
            } else {
                return redirect()->route($user->isAdmin() ? 'admin.preguntas.index' : 'medico.preguntas.index')
                    ->with('error', 'Debe especificar un paciente para el reporte.');
            }
        }

        // Obtener preguntas del paciente
        $query = Pregunta::with(['medico.usuario', 'diagnostico', 'tratamiento', 'respuestas.paciente.usuario'])
            ->where('id_paciente', $paciente->id_paciente);

        // Filtros
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_asignacion', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_asignacion', '<=', $request->fecha_hasta);
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $preguntas = $query->orderBy('fecha_asignacion', 'desc')->get();

        return view('preguntas.reporte', compact('paciente', 'preguntas'));
    }
}

