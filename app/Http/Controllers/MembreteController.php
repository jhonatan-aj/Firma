<?php

namespace App\Http\Controllers;

use App\Models\Membrete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MembreteController extends Controller
{
    public function index()
    {
        $membretes = Membrete::with('nivel')->get();

        $data = $membretes->map(function ($membrete) {
            return [
                'id' => $membrete->id,
                'nivel' => $membrete->nivel_id,
                'nivelFiltro' => $membrete->nivel_filtro,
                'nombre' => $membrete->nombre,
                'estado' => $membrete->estado,
                'derecha' => $membrete->derecha, // Accessor handles URL
                'izquierda' => $membrete->izquierda, // Accessor handles URL
                'centro' => $membrete->centro,
            ];
        });

        return response()->json($data);
    }

    public function active()
    {
        $membretes = Membrete::activos()->with('nivel')->get();

        $data = $membretes->map(function ($membrete) {
            return [
                'id' => $membrete->id,
                'nivel' => $membrete->nivel_id,
                'nivelFiltro' => $membrete->nivel_filtro,
                'nombre' => $membrete->nombre,
                'estado' => $membrete->estado,
                'derecha' => $membrete->derecha,
                'izquierda' => $membrete->izquierda,
                'centro' => $membrete->centro,
            ];
        });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nivel' => 'required|exists:niveles,id',
            'nivelFiltro' => 'required|string',
            'nombre' => 'required|string',
            'estado' => 'required|boolean',
            'derecha' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'izquierda' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'centro' => 'required|string',
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
            $membrete = new Membrete();
            $membrete->nivel_id = $request->nivel;
            $membrete->nivel_filtro = $request->nivelFiltro;
            $membrete->nombre = $request->nombre;
            $membrete->estado = $request->estado;
            $membrete->centro = $request->centro;

            if ($request->hasFile('derecha')) {
                $file = $request->file('derecha');
                $filename = time() . '_derecha_' . $file->getClientOriginalName();
                $file->storeAs('imagenes_membretados', $filename, 'public');
                $membrete->derecha = $filename;
            }

            if ($request->hasFile('izquierda')) {
                $file = $request->file('izquierda');
                $filename = time() . '_izquierda_' . $file->getClientOriginalName();
                $file->storeAs('imagenes_membretados', $filename, 'public');
                $membrete->izquierda = $filename;
            }

            $membrete->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Membrete creado correctamente',
                'data' => $membrete
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error al crear el membrete: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $membrete = Membrete::find($id);

        if (!$membrete) {
            return response()->json([
                'status' => false,
                'message' => 'Membrete no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nivel' => 'required|exists:niveles,id',
            'nivelFiltro' => 'required|string',
            'nombre' => 'required|string',
            'estado' => 'required|boolean',
            'derecha' => 'nullable', // Can be string (url) or file
            'izquierda' => 'nullable', // Can be string (url) or file
            'centro' => 'required|string',
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
            $membrete->nivel_id = $request->nivel;
            $membrete->nivel_filtro = $request->nivelFiltro;
            $membrete->nombre = $request->nombre;
            $membrete->estado = $request->estado;
            $membrete->centro = $request->centro;

            if ($request->hasFile('derecha')) {
                // Delete old image if exists
                if ($membrete->getRawOriginal('derecha')) {
                    Storage::disk('public')->delete('imagenes_membretados/' . $membrete->getRawOriginal('derecha'));
                }
                $file = $request->file('derecha');
                $filename = time() . '_derecha_' . $file->getClientOriginalName();
                $file->storeAs('imagenes_membretados', $filename, 'public');
                $membrete->derecha = $filename;
            }

            if ($request->hasFile('izquierda')) {
                // Delete old image if exists
                if ($membrete->getRawOriginal('izquierda')) {
                    Storage::disk('public')->delete('imagenes_membretados/' . $membrete->getRawOriginal('izquierda'));
                }
                $file = $request->file('izquierda');
                $filename = time() . '_izquierda_' . $file->getClientOriginalName();
                $file->storeAs('imagenes_membretados', $filename, 'public');
                $membrete->izquierda = $filename;
            }

            $membrete->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Membrete actualizado correctamente',
                'data' => $membrete
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el membrete: ' . $e->getMessage()
            ], 500);
        }
    }
}
