<?php

namespace App\Http\Controllers;

use App\Models\CatalogoDiagnostico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CatalogoDiagnosticoController extends Controller
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

        $query = CatalogoDiagnostico::with(['usuarioCreador', 'usuarioModificador']);

        // Filtros
        if ($request->filled('categoria')) {
            $query->where('categoria_medica', 'like', '%' . $request->categoria . '%');
        }

        if ($request->filled('codigo')) {
            $query->where('codigo', 'like', '%' . $request->codigo . '%');
        }

        if ($request->filled('descripcion')) {
            $query->where('descripcion_clinica', 'like', '%' . $request->descripcion . '%');
        }

        $catalogoDiagnosticos = $query->orderBy('categoria_medica')
            ->orderBy('descripcion_clinica')
            ->paginate(20);

        return view('catalogo-diagnosticos.index', compact('catalogoDiagnosticos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para crear diagnósticos en el catálogo.');
        }

        return view('catalogo-diagnosticos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para crear diagnósticos en el catálogo.');
        }

        $validator = Validator::make($request->all(), [
            'codigo' => 'nullable|string|max:50|unique:catalogo_diagnosticos,codigo',
            'descripcion_clinica' => 'required|string|max:1000',
            'categoria_medica' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $catalogoDiagnostico = CatalogoDiagnostico::create([
            'codigo' => $request->codigo,
            'descripcion_clinica' => $request->descripcion_clinica,
            'categoria_medica' => $request->categoria_medica,
            'id_usuario_creador' => $user->id_usuario,
            'fecha_creacion' => now(),
        ]);

        return redirect()->route($user->isAdmin() ? 'admin.catalogo-diagnosticos.index' : 'medico.catalogo-diagnosticos.index')
            ->with('success', 'Diagnóstico agregado al catálogo exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para ver este diagnóstico.');
        }

        $catalogoDiagnostico = CatalogoDiagnostico::with(['usuarioCreador', 'usuarioModificador', 'diagnosticos'])->findOrFail($id);

        return view('catalogo-diagnosticos.show', compact('catalogoDiagnostico'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para editar diagnósticos en el catálogo.');
        }

        $catalogoDiagnostico = CatalogoDiagnostico::findOrFail($id);

        return view('catalogo-diagnosticos.edit', compact('catalogoDiagnostico'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isMedico() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para editar diagnósticos en el catálogo.');
        }

        $catalogoDiagnostico = CatalogoDiagnostico::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'codigo' => 'nullable|string|max:50|unique:catalogo_diagnosticos,codigo,' . $id . ',id_diagnostico',
            'descripcion_clinica' => 'required|string|max:1000',
            'categoria_medica' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $catalogoDiagnostico->update([
            'codigo' => $request->codigo,
            'descripcion_clinica' => $request->descripcion_clinica,
            'categoria_medica' => $request->categoria_medica,
            'id_usuario_modificador' => $user->id_usuario,
            'fecha_modificacion' => now(),
        ]);

        return redirect()->route($user->isAdmin() ? 'admin.catalogo-diagnosticos.index' : 'medico.catalogo-diagnosticos.index')
            ->with('success', 'Diagnóstico del catálogo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Solo los administradores pueden eliminar diagnósticos del catálogo.');
        }

        $catalogoDiagnostico = CatalogoDiagnostico::findOrFail($id);

        // Verificar si hay diagnósticos asociados
        if ($catalogoDiagnostico->diagnosticos()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar este diagnóstico del catálogo porque tiene diagnósticos asociados.');
        }

        $catalogoDiagnostico->delete();

        return redirect()->route('admin.catalogo-diagnosticos.index')
            ->with('success', 'Diagnóstico eliminado del catálogo exitosamente.');
    }
}


