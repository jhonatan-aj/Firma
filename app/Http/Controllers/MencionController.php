<?php

namespace App\Http\Controllers;

use App\Models\Mencion;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class MencionController extends Controller
{
    public function indexAll()
    {
        $mencion = Mencion::all();

        $response = [
            'message' => 'Consulta realizada exitosamente.',
            'data' => $mencion
        ];
        $response = JWT::encode($response, env('VITE_SECRET_KEY'), 'HS512');

        return response()->json($response, 200);
    }

    public function index($nivel)
    {
        $mencion = Mencion::activas()
            ->porNivel($nivel)
            ->get();

        $response = [
            'message' => 'Consulta realizada exitosamente.',
            'data' => $mencion
        ];
        $response = JWT::encode($response, env('VITE_SECRET_KEY'), 'HS512');

        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $mencionesRecibidas = collect($request->all());

        // Extraer códigos únicos recibidos
        $codigosRecibidos = $mencionesRecibidas->pluck('codigo')->toArray();

        // 1. Insertar o actualizar menciones recibidas
        foreach ($mencionesRecibidas as $mencion) {
            Mencion::updateOrCreate(
                ['codigo' => $mencion['codigo']],
                [
                    'facultad' => $mencion['facultad'] ?? '09',
                    'nivel' => $mencion['nivel'],
                    'mencion' => $mencion['mencion'] ?? '',
                    'especialidad' => $mencion['especialidad'] ?? null,
                    'estado' => true,
                ]
            );
        }

        // 2. Desactivar menciones que no están en el array recibido
        Mencion::whereNotIn('codigo', $codigosRecibidos)
            ->update(['estado' => false]);

        return response()->json(['message' => 'Menciones sincronizadas correctamente'], 200);
    }
}
