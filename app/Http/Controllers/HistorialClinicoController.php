<?php

namespace App\Http\Controllers;

use App\Models\HistorialClinico;
use App\Models\Paciente;
use App\Models\Diagnostico;
use App\Models\Tratamiento;
use App\Models\CatalogoDiagnostico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class HistorialClinicoController extends Controller
{
    /**
     * Mostrar listado de historiales clínicos con filtros
     */
    public function index(Request $solicitud)
    {
        $usuario = Auth::user();
        
        if ($usuario->isMedico()) {
            // Médicos ven todos los historiales clínicos
            $consulta = HistorialClinico::with([
                'paciente.usuario', 
                'medico.usuario', 
                'diagnostico.catalogoDiagnostico', 
                'tratamiento'
            ]);
            
            // Filtro por nombre de paciente
            if ($solicitud->filled('nombre_paciente')) {
                $consulta->whereHas('paciente.usuario', function($q) use ($solicitud) {
                    $q->where('nombre', 'like', '%' . $solicitud->nombre_paciente . '%');
                });
            }
            
            // Filtro por fecha del evento
            if ($solicitud->filled('fecha_desde')) {
                $consulta->whereDate('fecha_evento', '>=', $solicitud->fecha_desde);
            }
            
            if ($solicitud->filled('fecha_hasta')) {
                $consulta->whereDate('fecha_evento', '<=', $solicitud->fecha_hasta);
            }
            
            // Filtro por diagnóstico
            if ($solicitud->filled('id_diagnostico')) {
                $consulta->where('id_diagnostico', $solicitud->id_diagnostico);
            }
            
            // Filtro por estado
            if ($solicitud->filled('estado')) {
                $consulta->where('estado', $solicitud->estado);
            }
            
            $historiales = $consulta->orderBy('fecha_evento', 'desc')
                ->orderBy('fecha_registro', 'desc')
                ->paginate(15);
                
        } elseif ($usuario->isAdmin()) {
            // Administradores ven todos los historiales para auditoría
            $consulta = HistorialClinico::with([
                'paciente.usuario', 
                'medico.usuario', 
                'diagnostico.catalogoDiagnostico', 
                'tratamiento'
            ]);
            
            // Filtros para administrador
            if ($solicitud->filled('nombre_paciente')) {
                $consulta->whereHas('paciente.usuario', function($q) use ($solicitud) {
                    $q->where('nombre', 'like', '%' . $solicitud->nombre_paciente . '%');
                });
            }
            
            if ($solicitud->filled('fecha_desde')) {
                $consulta->whereDate('fecha_evento', '>=', $solicitud->fecha_desde);
            }
            
            if ($solicitud->filled('fecha_hasta')) {
                $consulta->whereDate('fecha_evento', '<=', $solicitud->fecha_hasta);
            }
            
            if ($solicitud->filled('id_diagnostico')) {
                $consulta->where('id_diagnostico', $solicitud->id_diagnostico);
            }
            
            $historiales = $consulta->orderBy('fecha_evento', 'desc')
                ->orderBy('fecha_registro', 'desc')
                ->paginate(15);
        } else {
            // Pacientes ven su propio historial
            $historiales = HistorialClinico::with([
                'medico.usuario', 
                'diagnostico.catalogoDiagnostico', 
                'tratamiento'
            ])
                ->where('id_paciente', $usuario->paciente->id_paciente)
                ->orderBy('fecha_evento', 'desc')
                ->orderBy('fecha_registro', 'desc')
                ->paginate(15);
        }

        // Obtener todos los diagnósticos para el filtro (médicos y admin ven todos)
        $diagnosticos = null;
        if ($usuario->isMedico() || $usuario->isAdmin()) {
            $diagnosticos = Diagnostico::with('catalogoDiagnostico')->get();
        }

        return view('historial-clinico.index', compact('historiales', 'diagnosticos'));
    }

    /**
     * Mostrar formulario para crear nuevo evento clínico
     */
    public function create()
    {
        $usuario = Auth::user();
        
        if (!$usuario->isMedico() && !$usuario->isAdmin()) {
            return redirect()->route('historial-clinico.index')->with('error', 'Solo los médicos y administradores pueden crear registros de historial clínico.');
        }

        // Obtener todos los pacientes (médicos y admin ven todos)
        $pacientes = Paciente::with('usuario')->get();
            
        // Obtener todos los diagnósticos (médicos y admin ven todos)
        $diagnosticos = Diagnostico::with('catalogoDiagnostico')->get();
            
        // Obtener todos los tratamientos (médicos y admin ven todos)
        $tratamientos = Tratamiento::all();

        return view('historial-clinico.create', compact('pacientes', 'diagnosticos', 'tratamientos'));
    }

    /**
     * Guardar nuevo evento clínico
     */
    public function store(Request $solicitud)
    {
        $usuario = Auth::user();
        
        if (!$usuario->isMedico() && !$usuario->isAdmin()) {
            return redirect()->route('historial-clinico.index')->with('error', 'Solo los médicos y administradores pueden crear registros de historial clínico.');
        }

        $validador = Validator::make($solicitud->all(), [
            'id_paciente' => 'required|exists:pacientes,id_paciente',
            'id_diagnostico' => 'nullable|exists:diagnosticos,id_diagnostico',
            'id_tratamiento' => 'nullable|exists:tratamientos,id_tratamiento',
            'fecha_evento' => 'required|date',
            'observaciones' => 'required|string|max:5000',
            'resultados_analisis' => 'nullable|string|max:5000',
            'archivos_adjuntos.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ], [
            'id_paciente.required' => 'Debe seleccionar un paciente.',
            'id_paciente.exists' => 'El paciente seleccionado no existe.',
            'fecha_evento.required' => 'La fecha del evento es obligatoria.',
            'fecha_evento.date' => 'La fecha del evento debe ser una fecha válida.',
            'observaciones.required' => 'Las observaciones médicas son obligatorias.',
            'observaciones.max' => 'Las observaciones no pueden exceder 5000 caracteres.',
            'resultados_analisis.max' => 'Los resultados de análisis no pueden exceder 5000 caracteres.',
            'archivos_adjuntos.*.file' => 'Los archivos adjuntos deben ser archivos válidos.',
            'archivos_adjuntos.*.max' => 'Cada archivo no puede exceder 10MB.',
            'archivos_adjuntos.*.mimes' => 'Los archivos deben ser PDF, Word o imágenes (JPG, PNG).',
        ]);

        if ($validador->fails()) {
            return redirect()->back()->withErrors($validador)->withInput();
        }

        // Procesar archivos adjuntos
        $archivos_adjuntos = [];
        if ($solicitud->hasFile('archivos_adjuntos')) {
            foreach ($solicitud->file('archivos_adjuntos') as $archivo) {
                $ruta = $archivo->store('historial-clinico', 'public');
                $archivos_adjuntos[] = $ruta;
            }
        }

        // Determinar el médico tratante
        $id_medico = null;
        if ($usuario->isMedico()) {
            $id_medico = $usuario->medico->id_medico;
        } elseif ($solicitud->id_diagnostico) {
            $diagnostico = Diagnostico::find($solicitud->id_diagnostico);
            $id_medico = $diagnostico ? $diagnostico->id_medico : null;
        } elseif ($solicitud->id_tratamiento) {
            $tratamiento = Tratamiento::find($solicitud->id_tratamiento);
            $id_medico = $tratamiento ? $tratamiento->id_medico : null;
        }

        $historial = HistorialClinico::create([
            'id_paciente' => $solicitud->id_paciente,
            'id_medico' => $id_medico,
            'id_diagnostico' => $solicitud->id_diagnostico,
            'id_tratamiento' => $solicitud->id_tratamiento,
            'fecha_evento' => $solicitud->fecha_evento,
            'fecha_registro' => now(),
            'observaciones' => $solicitud->observaciones,
            'resultados_analisis' => $solicitud->resultados_analisis,
            'archivos_adjuntos' => !empty($archivos_adjuntos) ? $archivos_adjuntos : null,
            'estado' => 'activo',
        ]);

        return redirect()->route('historial-clinico.index')->with('success', 'Evento clínico registrado exitosamente.');
    }

    /**
     * Mostrar detalles de un evento clínico
     */
    public function show($id)
    {
        $usuario = Auth::user();
        $historial = HistorialClinico::with([
            'paciente.usuario', 
            'medico.usuario', 
            'diagnostico.catalogoDiagnostico', 
            'tratamiento'
        ])->findOrFail($id);

        // Verificar permisos
        if ($usuario->isPaciente() && $historial->id_paciente !== $usuario->paciente->id_paciente) {
            return redirect()->route('historial-clinico.index')->with('error', 'No tienes permisos para ver este registro.');
        }

        // Los médicos pueden ver todos los historiales

        return view('historial-clinico.show', compact('historial'));
    }

    /**
     * Mostrar formulario para editar evento clínico
     */
    public function edit($id)
    {
        $usuario = Auth::user();
        
        if (!$usuario->isMedico() && !$usuario->isAdmin()) {
            return redirect()->route('historial-clinico.index')->with('error', 'Solo los médicos y administradores pueden editar registros de historial clínico.');
        }

        $historial = HistorialClinico::findOrFail($id);
        
        // Los médicos pueden editar todos los historiales

        // Obtener datos para el formulario (todos los pacientes)
        $pacientes = Paciente::with('usuario')->get();
            
        // Obtener todos los diagnósticos (médicos y admin ven todos)
        $diagnosticos = Diagnostico::with('catalogoDiagnostico')->get();
            
        // Obtener todos los tratamientos (médicos y admin ven todos)
        $tratamientos = Tratamiento::all();

        return view('historial-clinico.edit', compact('historial', 'pacientes', 'diagnosticos', 'tratamientos'));
    }

    /**
     * Actualizar evento clínico
     */
    public function update(Request $solicitud, $id)
    {
        $usuario = Auth::user();
        
        if (!$usuario->isMedico() && !$usuario->isAdmin()) {
            return redirect()->route('historial-clinico.index')->with('error', 'Solo los médicos y administradores pueden editar registros de historial clínico.');
        }

        $historial = HistorialClinico::findOrFail($id);
        
        // Verificar permisos
        if ($usuario->isMedico()) {
            $paciente = $historial->paciente;
            if (!$paciente->medico || $paciente->medico->id_medico !== $usuario->medico->id_medico) {
                return redirect()->route('historial-clinico.index')->with('error', 'No tienes permisos para editar este registro.');
            }
        }

        $validador = Validator::make($solicitud->all(), [
            'id_diagnostico' => 'nullable|exists:diagnosticos,id_diagnostico',
            'id_tratamiento' => 'nullable|exists:tratamientos,id_tratamiento',
            'fecha_evento' => 'required|date',
            'observaciones' => 'required|string|max:5000',
            'resultados_analisis' => 'nullable|string|max:5000',
            'archivos_adjuntos.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ], [
            'fecha_evento.required' => 'La fecha del evento es obligatoria.',
            'fecha_evento.date' => 'La fecha del evento debe ser una fecha válida.',
            'observaciones.required' => 'Las observaciones médicas son obligatorias.',
            'observaciones.max' => 'Las observaciones no pueden exceder 5000 caracteres.',
            'resultados_analisis.max' => 'Los resultados de análisis no pueden exceder 5000 caracteres.',
            'archivos_adjuntos.*.file' => 'Los archivos adjuntos deben ser archivos válidos.',
            'archivos_adjuntos.*.max' => 'Cada archivo no puede exceder 10MB.',
            'archivos_adjuntos.*.mimes' => 'Los archivos deben ser PDF, Word o imágenes (JPG, PNG).',
        ]);

        if ($validador->fails()) {
            return redirect()->back()->withErrors($validador)->withInput();
        }

        // Procesar nuevos archivos adjuntos
        $archivos_existentes = $historial->archivos_adjuntos ?? [];
        if ($solicitud->hasFile('archivos_adjuntos')) {
            foreach ($solicitud->file('archivos_adjuntos') as $archivo) {
                $ruta = $archivo->store('historial-clinico', 'public');
                $archivos_existentes[] = $ruta;
            }
        }

        // Eliminar archivos marcados para eliminar
        if ($solicitud->filled('archivos_eliminar')) {
            $archivos_eliminar = is_array($solicitud->archivos_eliminar) 
                ? $solicitud->archivos_eliminar 
                : [$solicitud->archivos_eliminar];
                
            foreach ($archivos_eliminar as $archivo_eliminar) {
                if (Storage::disk('public')->exists($archivo_eliminar)) {
                    Storage::disk('public')->delete($archivo_eliminar);
                }
                $archivos_existentes = array_filter($archivos_existentes, function($archivo) use ($archivo_eliminar) {
                    return $archivo !== $archivo_eliminar;
                });
            }
            $archivos_existentes = array_values($archivos_existentes);
        }

        $historial->update([
            'id_diagnostico' => $solicitud->id_diagnostico,
            'id_tratamiento' => $solicitud->id_tratamiento,
            'fecha_evento' => $solicitud->fecha_evento,
            'observaciones' => $solicitud->observaciones,
            'resultados_analisis' => $solicitud->resultados_analisis,
            'archivos_adjuntos' => !empty($archivos_existentes) ? $archivos_existentes : null,
        ]);

        return redirect()->route('historial-clinico.index')->with('success', 'Evento clínico actualizado exitosamente.');
    }

    /**
     * Cerrar evento clínico (no eliminar, solo cambiar estado)
     */
    public function cerrar($id)
    {
        $usuario = Auth::user();
        
        if (!$usuario->isMedico() && !$usuario->isAdmin()) {
            return redirect()->route('historial-clinico.index')->with('error', 'Solo los médicos y administradores pueden cerrar eventos clínicos.');
        }

        $historial = HistorialClinico::findOrFail($id);
        
        // Los médicos pueden cerrar todos los eventos clínicos

        $historial->update(['estado' => 'cerrado']);

        return redirect()->route('historial-clinico.index')->with('success', 'Evento clínico cerrado exitosamente.');
    }

    /**
     * Generar reporte del historial clínico
     */
    public function reporte(Request $solicitud, $id_paciente = null)
    {
        $usuario = Auth::user();
        
        // Determinar el paciente para el reporte
        if ($id_paciente) {
            $paciente = Paciente::with('usuario')->findOrFail($id_paciente);
            
            // Verificar permisos
            if ($usuario->isPaciente() && $paciente->id_paciente !== $usuario->paciente->id_paciente) {
                return redirect()->route('historial-clinico.index')->with('error', 'No tienes permisos para ver este reporte.');
            }
            
            // Los médicos pueden ver reportes de todos los pacientes
        } else {
            if ($usuario->isPaciente()) {
                $paciente = $usuario->paciente;
            } else {
                return redirect()->route('historial-clinico.index')->with('error', 'Debe especificar un paciente para el reporte.');
            }
        }

        // Obtener historial del paciente
        $consulta = HistorialClinico::with([
            'medico.usuario', 
            'diagnostico.catalogoDiagnostico', 
            'tratamiento'
        ])
            ->where('id_paciente', $paciente->id_paciente);

        // Filtros opcionales
        if ($solicitud->filled('fecha_desde')) {
            $consulta->whereDate('fecha_evento', '>=', $solicitud->fecha_desde);
        }
        
        if ($solicitud->filled('fecha_hasta')) {
            $consulta->whereDate('fecha_evento', '<=', $solicitud->fecha_hasta);
        }
        
        if ($solicitud->filled('estado')) {
            $consulta->where('estado', $solicitud->estado);
        }

        $historiales = $consulta->orderBy('fecha_evento', 'desc')
            ->orderBy('fecha_registro', 'desc')
            ->get();

        return view('historial-clinico.reporte', compact('paciente', 'historiales'));
    }

    /**
     * Método para pacientes ver su historial clínico
     */
    public function paciente()
    {
        $usuario = Auth::user();
        
        if (!$usuario->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        $historiales = HistorialClinico::with([
            'medico.usuario', 
            'diagnostico.catalogoDiagnostico', 
            'tratamiento'
        ])
            ->where('id_paciente', $usuario->paciente->id_paciente)
            ->orderBy('fecha_evento', 'desc')
            ->orderBy('fecha_registro', 'desc')
            ->paginate(15);

        return view('paciente.historial-clinico.index', compact('historiales'));
    }
}
