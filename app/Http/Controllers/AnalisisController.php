<?php

namespace App\Http\Controllers;

use App\Models\Analisis;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Diagnostico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AnalisisController extends Controller
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

        $query = Analisis::with(['paciente.usuario', 'medico.usuario']);
        
        // Filtros
        if ($request->filled('paciente')) {
            $query->whereHas('paciente.usuario', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->paciente . '%')
                  ->orWhere('apPaterno', 'like', '%' . $request->paciente . '%')
                  ->orWhere('apMaterno', 'like', '%' . $request->paciente . '%');
            });
        }
        
        if ($request->filled('tipo_estudio')) {
            $query->where('tipo_estudio', 'like', '%' . $request->tipo_estudio . '%');
        }
        
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_analisis', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_analisis', '<=', $request->fecha_hasta);
        }

        // Médicos solo ven sus análisis, administradores ven todos
        if ($user->isMedico() && $user->medico) {
            $query->where('id_medico', $user->medico->id_medico);
        }
        
        $analisis = $query->orderBy('fecha_analisis', 'desc')
                          ->orderBy('fecha_creacion', 'desc')
                          ->paginate(15);
        
        // Estadísticas
        $stats = $this->getStats($user);
        
        // Opciones para filtros
        $tiposEstudio = Analisis::distinct()->pluck('tipo_estudio')->filter();
        $pacientes = Paciente::with('usuario')->get();
        
        return view('analisis.index', compact('analisis', 'stats', 'tiposEstudio', 'pacientes'));
    }

    /**
     * Obtener estadísticas de análisis
     */
    private function getStats($user)
    {
        $query = Analisis::query();
        
        if ($user->isMedico() && $user->medico) {
            $query->where('id_medico', $user->medico->id_medico);
        }
        
        $total = $query->count();
        $esteMes = $query->whereMonth('fecha_analisis', now()->month)
                        ->whereYear('fecha_analisis', now()->year)
                        ->count();
        $conValores = $query->whereNotNull('valores_obtenidos')->count();
        $conObservaciones = $query->whereNotNull('observaciones_clinicas')->count();
        
        return [
            'total' => $total,
            'este_mes' => $esteMes,
            'con_valores' => $conValores,
            'con_observaciones' => $conObservaciones,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden crear análisis.');
        }

        $pacientes = Paciente::with('usuario')->get();
        $medicos = Medico::with('usuario')->get();
        
        return view('analisis.create', compact('pacientes', 'medicos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden crear análisis.');
        }

        $validator = Validator::make($request->all(), [
            'tipo_estudio' => 'required|string|max:255',
            'descripcion' => 'required|string|max:2000',
            'fecha_analisis' => 'required|date',
            'id_paciente' => 'required|exists:pacientes,id_paciente',
            'id_medico' => 'required|exists:medicos,id_medico',
            'valores_obtenidos' => 'nullable|string|max:2000',
            'observaciones_clinicas' => 'nullable|string|max:2000',
        ], [
            'tipo_estudio.required' => 'El tipo o nombre del estudio es obligatorio.',
            'descripcion.required' => 'La descripción detallada es obligatoria.',
            'fecha_analisis.required' => 'La fecha del análisis es obligatoria.',
            'fecha_analisis.date' => 'La fecha del análisis debe ser una fecha válida.',
            'id_paciente.required' => 'Debe seleccionar un paciente.',
            'id_paciente.exists' => 'El paciente seleccionado no existe.',
            'id_medico.required' => 'Debe seleccionar un médico responsable.',
            'id_medico.exists' => 'El médico seleccionado no existe.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Si es médico, usar su id_medico automáticamente
        $id_medico = $request->id_medico;
        if ($user->isMedico() && $user->medico) {
            $id_medico = $user->medico->id_medico;
        }

        $analisis = Analisis::create([
            'tipo_estudio' => $request->tipo_estudio,
            'descripcion' => $request->descripcion,
            'fecha_analisis' => $request->fecha_analisis,
            'id_paciente' => $request->id_paciente,
            'id_medico' => $id_medico,
            'valores_obtenidos' => $request->valores_obtenidos,
            'observaciones_clinicas' => $request->observaciones_clinicas,
            'fecha_creacion' => now(),
        ]);

        $route = $user->isAdmin() ? 'admin.analisis.index' : 'medico.analisis.index';
        return redirect()->route($route)->with('success', 'Análisis clínico creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $analisis = Analisis::with([
            'paciente.usuario', 
            'medico.usuario'
        ])->findOrFail($id);

        // Verificar permisos
        if ($user->isMedico() && $user->medico && $analisis->id_medico !== $user->medico->id_medico) {
            return redirect()->route('medico.analisis.index')
                            ->with('error', 'No tienes permisos para ver este análisis.');
        }

        // Si es paciente, verificar que sea su análisis
        if ($user->isPaciente() && $user->paciente && $analisis->id_paciente !== $user->paciente->id_paciente) {
            return redirect()->route('paciente.analisis.index')
                            ->with('error', 'No tienes permisos para ver este análisis.');
        }

        return view('analisis.show', compact('analisis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden editar análisis.');
        }

        $analisis = Analisis::findOrFail($id);
        
        // Verificar permisos
        if ($user->isMedico() && $user->medico && $analisis->id_medico !== $user->medico->id_medico) {
            return redirect()->route('medico.analisis.index')
                            ->with('error', 'No tienes permisos para editar este análisis.');
        }

        $pacientes = Paciente::with('usuario')->get();
        $medicos = Medico::with('usuario')->get();
        
        return view('analisis.edit', compact('analisis', 'pacientes', 'medicos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden editar análisis.');
        }

        $analisis = Analisis::findOrFail($id);
        
        // Verificar permisos
        if ($user->isMedico() && $user->medico && $analisis->id_medico !== $user->medico->id_medico) {
            return redirect()->route('medico.analisis.index')
                            ->with('error', 'No tienes permisos para editar este análisis.');
        }

        $validator = Validator::make($request->all(), [
            'tipo_estudio' => 'required|string|max:255',
            'descripcion' => 'required|string|max:2000',
            'fecha_analisis' => 'required|date',
            'id_paciente' => 'required|exists:pacientes,id_paciente',
            'id_medico' => 'required|exists:medicos,id_medico',
            'valores_obtenidos' => 'nullable|string|max:2000',
            'observaciones_clinicas' => 'nullable|string|max:2000',
        ], [
            'tipo_estudio.required' => 'El tipo o nombre del estudio es obligatorio.',
            'descripcion.required' => 'La descripción detallada es obligatoria.',
            'fecha_analisis.required' => 'La fecha del análisis es obligatoria.',
            'fecha_analisis.date' => 'La fecha del análisis debe ser una fecha válida.',
            'id_paciente.required' => 'Debe seleccionar un paciente.',
            'id_paciente.exists' => 'El paciente seleccionado no existe.',
            'id_medico.required' => 'Debe seleccionar un médico responsable.',
            'id_medico.exists' => 'El médico seleccionado no existe.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Si es médico, usar su id_medico automáticamente
        $id_medico = $request->id_medico;
        if ($user->isMedico() && $user->medico) {
            $id_medico = $user->medico->id_medico;
        }

        $analisis->update([
            'tipo_estudio' => $request->tipo_estudio,
            'descripcion' => $request->descripcion,
            'fecha_analisis' => $request->fecha_analisis,
            'id_paciente' => $request->id_paciente,
            'id_medico' => $id_medico,
            'valores_obtenidos' => $request->valores_obtenidos,
            'observaciones_clinicas' => $request->observaciones_clinicas,
        ]);

        $route = $user->isAdmin() ? 'admin.analisis.index' : 'medico.analisis.index';
        return redirect()->route($route)->with('success', 'Análisis clínico actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden eliminar análisis.');
        }

        $analisis = Analisis::findOrFail($id);
        
        // Verificar permisos
        if ($user->isMedico() && $user->medico && $analisis->id_medico !== $user->medico->id_medico) {
            return redirect()->route('medico.analisis.index')
                            ->with('error', 'No tienes permisos para eliminar este análisis.');
        }

        $analisis->delete();

        $route = $user->isAdmin() ? 'admin.analisis.index' : 'medico.analisis.index';
        return redirect()->route($route)->with('success', 'Análisis clínico eliminado exitosamente.');
    }

    /**
     * Vista de análisis para pacientes
     */
    public function paciente(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        $query = Analisis::with(['medico.usuario'])
            ->where('id_paciente', $user->paciente->id_paciente);

        // Filtros
        if ($request->filled('tipo_estudio')) {
            $query->where('tipo_estudio', 'like', '%' . $request->tipo_estudio . '%');
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_analisis', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_analisis', '<=', $request->fecha_hasta);
        }

        $analisis = $query->orderBy('fecha_analisis', 'desc')
                          ->orderBy('fecha_creacion', 'desc')
                          ->paginate(15);

        // Estadísticas del paciente
        $totalAnalisis = Analisis::where('id_paciente', $user->paciente->id_paciente)->count();
        $esteMes = Analisis::where('id_paciente', $user->paciente->id_paciente)
            ->whereMonth('fecha_analisis', now()->month)
            ->whereYear('fecha_analisis', now()->year)
            ->count();
        $conValores = Analisis::where('id_paciente', $user->paciente->id_paciente)
            ->whereNotNull('valores_obtenidos')
            ->count();

        $stats = [
            'total' => $totalAnalisis,
            'este_mes' => $esteMes,
            'con_valores' => $conValores,
        ];

        $tiposEstudio = Analisis::where('id_paciente', $user->paciente->id_paciente)
            ->distinct()
            ->pluck('tipo_estudio')
            ->filter();

        return view('paciente.analisis.index', compact('analisis', 'stats', 'tiposEstudio'));
    }

    /**
     * Vista de detalles de análisis para pacientes
     */
    public function pacienteShow($id)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        $analisis = Analisis::with(['medico.usuario'])
            ->where('id_paciente', $user->paciente->id_paciente)
            ->findOrFail($id);

        return view('paciente.analisis.show', compact('analisis'));
    }
}
