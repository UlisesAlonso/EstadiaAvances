<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DiagnosticoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isMedico()) {
            // Médicos ven sus diagnósticos
            $diagnosticos = Diagnostico::with(['paciente.usuario'])
                ->where('id_medico', $user->medico->id_medico)
                ->when($request->filled('paciente'), function($query) use ($request) {
                    return $query->whereHas('paciente.usuario', function($q) use ($request) {
                        $q->where('nombre', 'like', '%' . $request->paciente . '%');
                    });
                })
                ->when($request->filled('fecha'), function($query) use ($request) {
                    return $query->whereDate('fecha', $request->fecha);
                })
                ->orderBy('fecha', 'desc')
                ->paginate(15);
        } elseif ($user->isAdmin()) {
            // Administradores ven todos los diagnósticos
            $diagnosticos = Diagnostico::with(['paciente.usuario', 'medico.usuario'])
                ->when($request->filled('paciente'), function($query) use ($request) {
                    return $query->whereHas('paciente.usuario', function($q) use ($request) {
                        $q->where('nombre', 'like', '%' . $request->paciente . '%');
                    });
                })
                ->when($request->filled('fecha'), function($query) use ($request) {
                    return $query->whereDate('fecha', $request->fecha);
                })
                ->orderBy('fecha', 'desc')
                ->paginate(15);
        } else {
            // Pacientes ven sus propios diagnósticos
            $diagnosticos = Diagnostico::with(['medico.usuario'])
                ->where('id_paciente', $user->paciente->id_paciente)
                ->orderBy('fecha', 'desc')
                ->paginate(15);
        }

        return view('diagnosticos.index', compact('diagnosticos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden crear diagnósticos.');
        }

        $pacientes = Paciente::with('usuario')->get();

        return view('diagnosticos.create', compact('pacientes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden crear diagnósticos.');
        }

        $validator = Validator::make($request->all(), [
            'id_paciente' => 'required|exists:pacientes,id_paciente',
            'fecha' => 'required|date',
            'descripcion' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Para administradores, necesitamos obtener el médico del paciente o usar un médico por defecto
        $id_medico = $user->isMedico() ? $user->medico->id_medico : 1; // Por defecto médico con ID 1

        $diagnostico = Diagnostico::create([
            'id_paciente' => $request->id_paciente,
            'id_medico' => $id_medico,
            'fecha' => $request->fecha,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route($user->isAdmin() ? 'admin.diagnosticos.index' : 'medico.diagnosticos.index')->with('success', 'Diagnóstico creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $diagnostico = Diagnostico::with(['paciente.usuario', 'medico.usuario'])->findOrFail($id);

        // Verificar permisos
        if ($user->isPaciente() && $diagnostico->id_paciente !== $user->paciente->id_paciente) {
            return redirect()->route($user->isAdmin() ? 'admin.diagnosticos.index' : 'medico.diagnosticos.index')->with('error', 'No tienes permisos para ver este diagnóstico.');
        }

        if ($user->isMedico() && $diagnostico->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.diagnosticos.index' : 'medico.diagnosticos.index')->with('error', 'No tienes permisos para ver este diagnóstico.');
        }

        return view('diagnosticos.show', compact('diagnostico'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden editar diagnósticos.');
        }

        $diagnostico = Diagnostico::findOrFail($id);
        
        // Los médicos solo pueden editar sus propios diagnósticos
        if ($user->isMedico() && $diagnostico->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.diagnosticos.index' : 'medico.diagnosticos.index')->with('error', 'No tienes permisos para editar este diagnóstico.');
        }

        $pacientes = Paciente::with('usuario')->get();

        return view('diagnosticos.edit', compact('diagnostico', 'pacientes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden editar diagnósticos.');
        }

        $diagnostico = Diagnostico::findOrFail($id);
        
        // Los médicos solo pueden editar sus propios diagnósticos
        if ($user->isMedico() && $diagnostico->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.diagnosticos.index' : 'medico.diagnosticos.index')->with('error', 'No tienes permisos para editar este diagnóstico.');
        }

        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date',
            'descripcion' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $diagnostico->update($request->all());

        return redirect()->route($user->isAdmin() ? 'admin.diagnosticos.index' : 'medico.diagnosticos.index')->with('success', 'Diagnóstico actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los médicos y administradores pueden eliminar diagnósticos.');
        }

        $diagnostico = Diagnostico::findOrFail($id);
        
        // Los médicos solo pueden eliminar sus propios diagnósticos
        if ($user->isMedico() && $diagnostico->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.diagnosticos.index' : 'medico.diagnosticos.index')->with('error', 'No tienes permisos para eliminar este diagnóstico.');
        }

        $diagnostico->delete();

        return redirect()->route($user->isAdmin() ? 'admin.diagnosticos.index' : 'medico.diagnosticos.index')->with('success', 'Diagnóstico eliminado exitosamente.');
    }
}