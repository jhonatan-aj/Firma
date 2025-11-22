<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerfilController extends Controller
{
    public function index()
    {
        try {
            $perfiles = Perfil::with('permisos:id')->get()->map(function ($perfil) {
                return [
                    'id' => $perfil->id,
                    'nombre' => $perfil->nombre,
                    'estado' => $perfil->estado,
                    'permiso' => $perfil->permisos->pluck('id')->toArray(),
                ];
            });

            $response = [
                'message' => 'Consulta realizada exitosamente.',
                'data' => $perfiles
            ];

            return response()->json(JWT::encode($response, env('VITE_SECRET_KEY'), 'HS512'), 200);
        } catch (\Exception $e) {
            Log::error('Error al consultar perfiles: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al realizar la consulta.',
                'data' => null
            ], 500);
        }
    }

    public function activos()
    {
        try {
            $perfiles = Perfil::activos()->with('permisos:id')->get()->map(function ($perfil) {
                return [
                    'id' => $perfil->id,
                    'nombre' => $perfil->nombre,
                    'estado' => $perfil->estado,
                    'permiso' => $perfil->permisos->pluck('id')->toArray(),
                ];
            });

            $response = [
                'message' => 'Consulta realizada exitosamente.',
                'data' => $perfiles
            ];

            return response()->json(JWT::encode($response, env('VITE_SECRET_KEY'), 'HS512'), 200);
        } catch (\Exception $e) {
            Log::error('Error al consultar perfiles activos: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al realizar la consulta.',
                'data' => null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:perfiles,nombre',
            'estado' => 'boolean',
            'permiso' => 'array',
            'permiso.*' => 'exists:permisos,id',
        ]);

        try {
            DB::beginTransaction();

            $perfil = Perfil::create([
                'nombre' => $validated['nombre'],
                'estado' => $validated['estado'] ?? true,
            ]);

            if (!empty($validated['permiso'])) {
                $perfil->permisos()->sync($validated['permiso']);
            }

            $response = [
                'message' => 'Registro creado exitosamente.',
                'data' => $perfil->load('permisos')
            ];

            DB::commit();
            return response()->json(JWT::encode($response, env('VITE_SECRET_KEY'), 'HS512'), 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Ocurrió un error al guardar el registro.',
                'data' => null
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:perfiles,id',
            'nombre' => 'required|string|unique:perfiles,nombre,' . $request->id,
            'estado' => 'boolean',
            'permiso' => 'array',
            'permiso.*' => 'exists:permisos,id',
        ]);

        try {
            DB::beginTransaction();

            $perfil = Perfil::findOrFail($validated['id']);
            $perfil->update([
                'nombre' => $validated['nombre'],
                'estado' => $validated['estado'] ?? true,
            ]);

            $perfil->permisos()->sync($validated['permiso'] ?? []);

            $response = [
                'message' => 'Registro actualizado exitosamente.',
                'data' => $perfil->load('permisos')
            ];

            DB::commit();
            return response()->json(JWT::encode($response, env('VITE_SECRET_KEY'), 'HS512'), 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Ocurrió un error al actualizar el registro.',
                'data' => null
            ], 500);
        }
    }

}
