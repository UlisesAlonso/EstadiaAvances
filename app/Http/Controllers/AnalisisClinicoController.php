<?php

namespace App\Http\Controllers;

use App\Models\AnalisisClinico;
use App\Models\Paciente;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AnalisisClinicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isMedico()) {
            // Médicos ven sus análisis
            $query = AnalisisClinico::with(['paciente.usuario', 'medico.usuario'])
                ->where('id_medico', $user->medico->id_medico);
            
            // Filtros
            if ($request->filled('paciente')) {
                $query->whereHas('paciente.usuario', function($q) use ($request) {
                    $q->where('nombre', 'like', '%' . $request->paciente . '%')
                      ->orWhere('apPaterno', 'like', '%' . $request->paciente . '%')
                      ->orWhere('apMaterno', 'like', '%' . $request->paciente . '%');
                });
            }
            
            if ($request->filled('tipo_analisis')) {
                $query->where('tipo_analisis', 'like', '%' . $request->tipo_analisis . '%');
            }
            
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha', '>=', $request->fecha_desde);
            }
            
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha', '<=', $request->fecha_hasta);
            }
            
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            
            $analisis = $query->orderBy('fecha', 'desc')->paginate(15);
            
        } elseif ($user->isAdmin()) {
            // Administradores ven todos los análisis
            $query = AnalisisClinico::with(['paciente.usuario', 'medico.usuario']);
            
            // Filtros
            if ($request->filled('paciente')) {
                $query->whereHas('paciente.usuario', function($q) use ($request) {
                    $q->where('nombre', 'like', '%' . $request->paciente . '%')
                      ->orWhere('apPaterno', 'like', '%' . $request->paciente . '%')
                      ->orWhere('apMaterno', 'like', '%' . $request->paciente . '%');
                });
            }
            
            if ($request->filled('tipo_analisis')) {
                $query->where('tipo_analisis', 'like', '%' . $request->tipo_analisis . '%');
            }
            
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha', '>=', $request->fecha_desde);
            }
            
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha', '<=', $request->fecha_hasta);
            }
            
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            
            $analisis = $query->orderBy('fecha', 'desc')->paginate(15);
        } else {
            // Pacientes ven sus propios análisis
            $query = AnalisisClinico::with(['medico.usuario'])
                ->where('id_paciente', $user->paciente->id_paciente);
            
            if ($request->filled('tipo_analisis')) {
                $query->where('tipo_analisis', 'like', '%' . $request->tipo_analisis . '%');
            }
            
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha', '>=', $request->fecha_desde);
            }
            
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha', '<=', $request->fecha_hasta);
            }
            
            $analisis = $query->orderBy('fecha', 'desc')->paginate(15);
        }

        // Obtener tipos de análisis únicos para filtros
        $tiposAnalisis = AnalisisClinico::distinct()->pluck('tipo_analisis')->filter();

        return view('analisis-clinicos.index', compact('analisis', 'tiposAnalisis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden crear análisis clínicos.');
        }

        $pacientes = Paciente::with('usuario')->get();

        return view('analisis-clinicos.create', compact('pacientes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden crear análisis clínicos.');
        }

        $validator = Validator::make($request->all(), [
            'id_paciente' => 'required|exists:pacientes,id_paciente',
            'tipo_analisis' => 'required|string|max:255',
            'descripcion' => 'required|string|max:2000',
            'fecha' => 'required|date',
            'resultado' => 'nullable|string|max:2000',
            'valores_cuantitativos' => 'nullable|array',
            'valores_cuantitativos.*.nombre' => 'required_with:valores_cuantitativos|string|max:255',
            'valores_cuantitativos.*.valor' => 'required_with:valores_cuantitativos|numeric',
            'valores_cuantitativos.*.unidad' => 'nullable|string|max:50',
            'observaciones_clinicas' => 'nullable|string|max:2000',
        ], [
            'id_paciente.required' => 'El paciente es obligatorio.',
            'tipo_analisis.required' => 'El tipo de análisis es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'fecha.required' => 'La fecha del análisis es obligatoria.',
        ]);

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

        // Procesar valores cuantitativos
        $valoresCuantitativos = null;
        if ($request->filled('valores_cuantitativos')) {
            $valores = [];
            foreach ($request->valores_cuantitativos as $valor) {
                if (!empty($valor['nombre']) && isset($valor['valor'])) {
                    $valores[] = [
                        'nombre' => $valor['nombre'],
                        'valor' => $valor['valor'],
                        'unidad' => $valor['unidad'] ?? null,
                    ];
                }
            }
            $valoresCuantitativos = !empty($valores) ? $valores : null;
        }

        AnalisisClinico::create([
            'id_paciente' => $request->id_paciente,
            'id_medico' => $id_medico,
            'tipo_analisis' => $request->tipo_analisis,
            'descripcion' => $request->descripcion,
            'resultado' => $request->resultado,
            'valores_cuantitativos' => $valoresCuantitativos,
            'observaciones_clinicas' => $request->observaciones_clinicas,
            'fecha' => $request->fecha,
            'estado' => 'activo',
        ]);

        return redirect()->route($user->isAdmin() ? 'admin.analisis-clinicos.index' : 'medico.analisis-clinicos.index')
            ->with('success', 'Análisis clínico creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $analisis = AnalisisClinico::with(['paciente.usuario', 'medico.usuario'])->findOrFail($id);

        // Verificar permisos
        if ($user->isPaciente() && $analisis->id_paciente !== $user->paciente->id_paciente) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para ver este análisis.');
        }

        if ($user->isMedico() && $analisis->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.analisis-clinicos.index' : 'medico.analisis-clinicos.index')
                ->with('error', 'No tienes permisos para ver este análisis.');
        }

        return view('analisis-clinicos.show', compact('analisis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden editar análisis clínicos.');
        }

        $analisis = AnalisisClinico::findOrFail($id);
        
        // Verificar permisos
        if ($user->isMedico() && $analisis->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.analisis-clinicos.index' : 'medico.analisis-clinicos.index')
                ->with('error', 'No tienes permisos para editar este análisis.');
        }

        $pacientes = Paciente::with('usuario')->get();

        return view('analisis-clinicos.edit', compact('analisis', 'pacientes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden editar análisis clínicos.');
        }

        $analisis = AnalisisClinico::findOrFail($id);
        
        // Verificar permisos
        if ($user->isMedico() && $analisis->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.analisis-clinicos.index' : 'medico.analisis-clinicos.index')
                ->with('error', 'No tienes permisos para editar este análisis.');
        }

        // Determinar el estado (solo administradores pueden cambiarlo)
        $estado = $analisis->estado ?? 'activo';
        if ($user->isAdmin() && $request->filled('estado')) {
            $estado = $request->estado;
        }

        $validator = Validator::make($request->all(), [
            'id_paciente' => 'required|exists:pacientes,id_paciente',
            'tipo_analisis' => 'required|string|max:255',
            'descripcion' => 'required|string|max:2000',
            'fecha' => 'required|date',
            'resultado' => 'nullable|string|max:2000',
            'valores_cuantitativos' => 'nullable|array',
            'valores_cuantitativos.*.nombre' => 'required_with:valores_cuantitativos|string|max:255',
            'valores_cuantitativos.*.valor' => 'required_with:valores_cuantitativos|numeric',
            'valores_cuantitativos.*.unidad' => 'nullable|string|max:50',
            'observaciones_clinicas' => 'nullable|string|max:2000',
            'estado' => $user->isAdmin() ? 'required|in:activo,inactivo' : 'nullable|in:activo,inactivo',
        ], [
            'id_paciente.required' => 'El paciente es obligatorio.',
            'tipo_analisis.required' => 'El tipo de análisis es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'fecha.required' => 'La fecha del análisis es obligatoria.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Procesar valores cuantitativos
        $valoresCuantitativos = null;
        if ($request->filled('valores_cuantitativos')) {
            $valores = [];
            foreach ($request->valores_cuantitativos as $valor) {
                if (!empty($valor['nombre']) && isset($valor['valor'])) {
                    $valores[] = [
                        'nombre' => $valor['nombre'],
                        'valor' => $valor['valor'],
                        'unidad' => $valor['unidad'] ?? null,
                    ];
                }
            }
            $valoresCuantitativos = !empty($valores) ? $valores : null;
        }

        $analisis->update([
            'id_paciente' => $request->id_paciente,
            'tipo_analisis' => $request->tipo_analisis,
            'descripcion' => $request->descripcion,
            'resultado' => $request->resultado,
            'valores_cuantitativos' => $valoresCuantitativos,
            'observaciones_clinicas' => $request->observaciones_clinicas,
            'fecha' => $request->fecha,
            'estado' => $estado,
        ]);

        return redirect()->route($user->isAdmin() ? 'admin.analisis-clinicos.index' : 'medico.analisis-clinicos.index')
            ->with('success', 'Análisis clínico actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden eliminar análisis clínicos.');
        }

        $analisis = AnalisisClinico::findOrFail($id);
        
        // Verificar permisos
        if ($user->isMedico() && $analisis->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.analisis-clinicos.index' : 'medico.analisis-clinicos.index')
                ->with('error', 'No tienes permisos para eliminar este análisis.');
        }

        $analisis->delete();

        return redirect()->route($user->isAdmin() ? 'admin.analisis-clinicos.index' : 'medico.analisis-clinicos.index')
            ->with('success', 'Análisis clínico eliminado exitosamente.');
    }
}
