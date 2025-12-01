<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UsuarioController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string|unique:usuarios,usuario|min:4',
            'clave' => 'required|string|min:8|confirmed',
        ]);

        try{
            DB::beginTransaction();

            $usuario = Usuario::create([
                'persona_id' => $request->persona,
                'usuario' => $request->usuario,
                'password' => Hash::make($request->password),
                'estado' => true,
                'foto' => null
            ]);

            DB::commit();
            $response = [
                'message' => 'Usuario registrado exitosamente',
                'persona' => $usuario,
            ];

            return response()->json(JWT::encode($response, env('VITE_SECRET_KEY'), 'HS512'), 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al guardar el registro.',
                'data' => null
            ], 500);
        }
    }

    public function update(Request $request)
    {
        // Validación: no incluimos 'usuario' porque no se actualiza
        $request->validate([
            'clave' => 'nullable|string|min:8|confirmed',
            'estado' => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            $usuario = Usuario::findOrFail($request->id);

            // Actualizar campos básicos
            $usuario->persona_id = $request->persona ?? $usuario->persona_id;
            $usuario->estado = $request->estado;

            // Si viene clave, actualizarla
            if ($request->filled('clave')) {
                $usuario->password = Hash::make($request->clave);
            }

            $usuario->save();
            DB::commit();

            $response = [
                'message' => 'Usuario actualizado exitosamente',
                'persona' => $usuario,
            ];

            return response()->json(JWT::encode($response, env('VITE_SECRET_KEY'), 'HS512'), 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el registro.',
                'data' => null
            ], 500);
        }
    }

    public function updateClaveFoto(Request $request, $id)
    {
        $request->validate([
            'clave' => 'nullable|string|min:8|confirmed',
            'foto'  => 'nullable|image|max:2048', // máximo 2MB
        ]);

        try {
            DB::beginTransaction();

            $usuario = Usuario::findOrFail($id);

            // Actualizar clave si viene
            if ($request->filled('clave')) {
                $usuario->password = Hash::make($request->clave);
            }

            // Actualizar foto si viene
            if ($request->hasFile('foto')) {
                // Eliminar foto anterior si existe
                if ($usuario->getRawOriginal('foto')) {
                    Storage::disk('local')->delete('usuarios_fotos/' . $usuario->getRawOriginal('foto'));
                }

                $file = $request->file('foto');
                $filename = time() . '_usuario_' . $file->getClientOriginalName();
                $file->storeAs('usuarios_fotos', $filename, 'local'); // ⚠️ disk privado
                $usuario->foto = $filename;
            } else if ($usuario->foto) {
                // Si se quiere eliminar la foto
                Storage::disk('local')->delete('usuarios_fotos/' . $usuario->getRawOriginal('foto'));
                $usuario->foto = null;
            }

            $usuario->save();

            DB::commit();

            return response()->json([
                'message' => 'Clave y/o foto actualizadas correctamente',
                'usuario' => $usuario,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Ocurrió un error al actualizar clave/foto.',
                'data' => null
            ], 500);
        }
    }

}
