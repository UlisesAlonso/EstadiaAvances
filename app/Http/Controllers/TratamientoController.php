<?php

namespace App\Http\Controllers;

use App\Models\Tratamiento;
use App\Models\Paciente;
use App\Models\Diagnostico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\CambioTratamiento;

class TratamientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isMedico()) {
            // Médicos ven sus tratamientos
            $tratamientos = Tratamiento::with(['paciente.usuario', 'diagnostico'])
                ->where('id_medico', $user->medico->id_medico)
                ->when($request->filled('estado'), function($query) use ($request) {
                    return $query->where('activo', $request->estado === 'activo');
                })
                ->when($request->filled('paciente'), function($query) use ($request) {
                    return $query->whereHas('paciente.usuario', function($q) use ($request) {
                        $q->where('nombre', 'like', '%' . $request->paciente . '%');
                    });
                })
                ->orderBy('fecha_inicio', 'desc')
                ->paginate(15);
        } else {
            // Pacientes ven sus propios tratamientos
            $tratamientos = Tratamiento::with(['medico.usuario', 'diagnostico'])
                ->where('id_paciente', $user->paciente->id_paciente)
                ->orderBy('fecha_inicio', 'desc')
                ->paginate(15);
        }

        return view('tratamientos.index', compact('tratamientos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isMedico()) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'Solo los médicos pueden crear tratamientos.');
        }

        $pacientes = Paciente::with('usuario')->get();
        $diagnosticos = Diagnostico::where('id_medico', $user->medico->id_medico)->get();

        return view('tratamientos.create', compact('pacientes', 'diagnosticos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isMedico()) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'Solo los médicos pueden crear tratamientos.');
        }

        $validator = Validator::make($request->all(), [
            'id_paciente' => 'required|exists:pacientes,id_paciente',
            'id_diagnostico' => 'required|exists:diagnosticos,id_diagnostico',
            'nombre' => 'required|string|max:255',
            'dosis' => 'required|string|max:255',
            'frecuencia' => 'required|string|max:255',
            'duracion' => 'required|string|max:255',
            'observaciones' => 'nullable|string',
            'fecha_inicio' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $tratamiento = Tratamiento::create([
            'id_paciente' => $request->id_paciente,
            'id_medico' => $user->medico->id_medico,
            'id_diagnostico' => $request->id_diagnostico,
            'nombre' => $request->nombre,
            'dosis' => $request->dosis,
            'frecuencia' => $request->frecuencia,
            'duracion' => $request->duracion,
            'observaciones' => $request->observaciones,
            'fecha_inicio' => $request->fecha_inicio,
            'activo' => true,
        ]);

        return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('success', 'Tratamiento creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $tratamiento = Tratamiento::with(['paciente.usuario', 'medico.usuario', 'diagnostico'])->findOrFail($id);

        // Verificar permisos
        if ($user->isPaciente() && $tratamiento->id_paciente !== $user->paciente->id_paciente) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'No tienes permisos para ver este tratamiento.');
        }

        if ($user->isMedico() && $tratamiento->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'No tienes permisos para ver este tratamiento.');
        }

        return view('tratamientos.show', compact('tratamiento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico()) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'Solo los médicos pueden editar tratamientos.');
        }

        $tratamiento = Tratamiento::findOrFail($id);
        
        if ($tratamiento->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'No tienes permisos para editar este tratamiento.');
        }

        $pacientes = Paciente::with('usuario')->get();
        $diagnosticos = Diagnostico::where('id_medico', $user->medico->id_medico)->get();

        return view('tratamientos.edit', compact('tratamiento', 'pacientes', 'diagnosticos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico()) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'Solo los médicos pueden editar tratamientos.');
        }

        $tratamiento = Tratamiento::findOrFail($id);
        
        if ($tratamiento->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'No tienes permisos para editar este tratamiento.');
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'dosis' => 'required|string|max:255',
            'frecuencia' => 'required|string|max:255',
            'duracion' => 'required|string|max:255',
            'observaciones' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $tratamiento->update($request->all());
        Mail::to($tratamiento->paciente->usuario->correo) 
                ->send(new CambioTratamiento($tratamiento, $tratamiento->paciente->usuario));

        return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('success', 'Tratamiento actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico()) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'Solo los médicos pueden eliminar tratamientos.');
        }

        $tratamiento = Tratamiento::findOrFail($id);
        
        if ($tratamiento->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'No tienes permisos para eliminar este tratamiento.');
        }

        $tratamiento->delete();

        return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('success', 'Tratamiento eliminado exitosamente.');
    }

    /**
     * Método para pacientes ver sus tratamientos
     */
    public function paciente()
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        $tratamientos = Tratamiento::with(['medico.usuario', 'diagnostico'])
            ->where('id_paciente', $user->paciente->id_paciente)
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(15);

        return view('paciente.tratamientos.index', compact('tratamientos'));
    }

    /**
     * Método para pacientes ver detalles de un tratamiento
     */
    public function pacienteShow($id)
    {
        $user = Auth::user();
        
        if (!$user->isPaciente()) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        $tratamiento = Tratamiento::with(['medico.usuario', 'diagnostico'])
            ->where('id_paciente', $user->paciente->id_paciente)
            ->findOrFail($id);

        return view('paciente.tratamientos.show', compact('tratamiento'));
    }

    /**
     * Cambiar estado del tratamiento (finalizar o suspender)
     */
    public function toggleStatus($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico()) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'Solo los médicos pueden cambiar el estado de tratamientos.');
        }

        $tratamiento = Tratamiento::findOrFail($id);
        
        if ($tratamiento->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'No tienes permisos para modificar este tratamiento.');
        }

        // Cambiar estado: activo -> suspendido, suspendido -> activo
        $nuevoEstado = $tratamiento->activo ? false : true;
        $tratamiento->update(['activo' => $nuevoEstado]);

        $status = $nuevoEstado ? 'activado' : 'suspendido';
        $mensaje = $nuevoEstado ? 
            'Tratamiento activado exitosamente.' : 
            'Tratamiento suspendido exitosamente.';

        return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('success', $mensaje);
    }

    /**
     * Finalizar tratamiento
     */
    public function finalizar($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico()) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'Solo los médicos pueden finalizar tratamientos.');
        }

        $tratamiento = Tratamiento::findOrFail($id);
        
        if ($tratamiento->id_medico !== $user->medico->id_medico) {
            return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('error', 'No tienes permisos para modificar este tratamiento.');
        }

        // Finalizar tratamiento (marcar como inactivo)
        $tratamiento->update(['activo' => false]);

        return redirect()->route($user->isAdmin() ? 'admin.tratamientos.index' : 'medico.tratamientos.index')->with('success', 'Tratamiento finalizado exitosamente.');
    }
}
