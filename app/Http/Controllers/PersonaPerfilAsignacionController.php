<?php

namespace App\Http\Controllers;

use App\Models\PersonaPerfilAsignacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonaPerfilAsignacionController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'perfil_id'  => 'required|exists:perfiles,id',
            'nivel_id'   => 'required|exists:niveles,id',
            'menciones'  => 'nullable|array',
            'menciones.*'=> 'integer|exists:menciones,id',
            'oficina_id' => 'nullable|exists:oficinas,id',
            'puesto_id'  => 'nullable|exists:puestos,id',
            'estado'     => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            $asignacion = PersonaPerfilAsignacion::create([
                'usuario_id' => $request->usuario_id,
                'perfil_id'  => $request->perfil_id,
                'nivel_id'   => $request->nivel_id,
                'menciones'  => $request->menciones ?? [],
                'oficina_id' => $request->oficina_id,
                'puesto_id'  => $request->puesto_id,
                'estado'     => $request->estado,
            ]);

            DB::commit();

            return response()->json([
                'message'    => 'Asignación creada correctamente',
                'asignacion' => $asignacion,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear la asignación',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'perfil_id'  => 'required|exists:perfiles,id',
            'nivel_id'   => 'required|exists:niveles,id',
            'menciones'  => 'nullable|array',
            'menciones.*'=> 'integer|exists:menciones,id',
            'oficina_id' => 'nullable|exists:oficinas,id',
            'puesto_id'  => 'nullable|exists:puestos,id',
            'estado'     => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            $asignacion = PersonaPerfilAsignacion::findOrFail($id);

            $asignacion->update([
                'usuario_id' => $request->usuario_id,
                'perfil_id'  => $request->perfil_id,
                'nivel_id'   => $request->nivel_id,
                'menciones'  => $request->menciones ?? [],
                'oficina_id' => $request->oficina_id,
                'puesto_id'  => $request->puesto_id,
                'estado'     => $request->estado,
            ]);

            DB::commit();

            return response()->json([
                'message'    => 'Asignación actualizada correctamente',
                'asignacion' => $asignacion,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar la asignación',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


}
