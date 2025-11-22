<?php

namespace App\Http\Controllers;

use App\Models\Formato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormatoController extends Controller
{
    public function index()
    {
        $formatos = Formato::with('membrete')->get();

        $data = $formatos->map(function ($formato) {
            return [
                'id' => $formato->id,
                'estado' => $formato->estado,
                'nombre' => $formato->nombre,
                'membrete' => $formato->membrete_id,
                'utilizado' => $formato->utilizado,
                'tipo' => $formato->tipo,
                'contenido' => $formato->contenido,
            ];
        });

        return response()->json($data);
    }

    public function active()
    {
        $formatos = Formato::activos()->with('membrete')->get();

        $data = $formatos->map(function ($formato) {
            return [
                'id' => $formato->id,
                'estado' => $formato->estado,
                'nombre' => $formato->nombre,
                'membrete' => $formato->membrete_id,
                'utilizado' => $formato->utilizado,
                'tipo' => $formato->tipo,
                'contenido' => $formato->contenido,
            ];
        });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'estado' => 'required|boolean',
            'membrete' => 'required|exists:membretes,id',
            'utilizado' => 'required|boolean',
            'tipo' => 'required|string',
            'contenido' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $formato = new Formato();
            $formato->nombre = $request->nombre;
            $formato->estado = $request->estado;
            $formato->membrete_id = $request->membrete;
            $formato->utilizado = $request->utilizado;
            $formato->tipo = $request->tipo;
            $formato->contenido = $request->contenido;
            $formato->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Formato creado correctamente',
                'data' => $formato
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error al crear el formato: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $formato = Formato::find($id);

        if (!$formato) {
            return response()->json([
                'status' => false,
                'message' => 'Formato no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'estado' => 'required|boolean',
            'membrete' => 'required|exists:membretes,id',
            'utilizado' => 'required|boolean',
            'tipo' => 'required|string',
            'contenido' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $formato->nombre = $request->nombre;
            $formato->estado = $request->estado;
            $formato->membrete_id = $request->membrete;
            $formato->utilizado = $request->utilizado;
            $formato->tipo = $request->tipo;
            $formato->contenido = $request->contenido;
            $formato->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Formato actualizado correctamente',
                'data' => $formato
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el formato: ' . $e->getMessage()
            ], 500);
        }
    }
}
