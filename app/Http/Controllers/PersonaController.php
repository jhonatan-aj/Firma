<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PersonaResource;
use Illuminate\Support\Facades\Log;

class PersonaController extends Controller
{
    public function index()
    {
        try {
            $personas = Persona::with(['usuarios'])->get();
            $formatted = PersonaResource::collection($personas);

            $response = [
                'message' => 'Consulta realizada exitosamente.',
                'data' => $formatted
            ];

            return response()->json(JWT::encode($response, env('VITE_SECRET_KEY'), 'HS512'), 200);

        } catch (\Exception $e) {
            Log::error('Error al consultar puestos: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al realizar la consulta.',
                'data' => null
            ], 500);
        }
    }

     public function consultarDNI($dni)
    {
        $verificacion = $this->verificarDNI($dni);

        if($verificacion){
            return response()->json([
                'message' => 'Usuario registrado.',
                'data' => null
            ], 404);
        }

        $persona = DB::connection('consulta_db')->table('Persona')
            ->select([
                'Codigo',
                'Paterno',
                'Materno',
                'Nombres',
                'Fech_Nac',
                'E_Mail',
                'Telefono',
                'Dpto_Dir',
                'Prov_Dir',
                'Dist_Dir',
                'Id_Tip_Dir',
                'Desc_Dir',
                'Num_Dir'
            ])
            ->where('DNI', $dni)
            ->first();

        if (!$persona) {
            return response()->json([
                'message' => 'No se encontró una persona con ese DNI.',
                'data' => null
            ], 404);
        }

        // 🔹 Ubigeo: Departamento
        $departamento = DB::connection('consulta_db')->table('Dpto')
            ->select('Desc_Dpto as departamento')
            ->where('Id_Dpto', $persona->Dpto_Dir)
            ->where('Id_Dpto', '<>', '00')
            ->first();

        // 🔹 Ubigeo: Provincia
        $provincia = DB::connection('consulta_db')->table('Provincia')
            ->select('Desc_Prov as provincia')
            ->where('Id_Dpto', $persona->Dpto_Dir)
            ->where('Id_Prov', $persona->Prov_Dir)
            ->where('Id_Prov', '<>', '00')
            ->first();

        // 🔹 Ubigeo: Distrito
        $distrito = DB::connection('consulta_db')->table('Distrito')
            ->select('Desc_Dist as distrito')
            ->where('Id_Dpto', $persona->Dpto_Dir)
            ->where('Id_Prov', $persona->Prov_Dir)
            ->where('Id_Dist', $persona->Dist_Dir)
            ->where('Id_Dist', '<>', '00')
            ->first();

        // 🔹 Tipo de dirección
        $tipoDireccion = DB::connection('consulta_db')->table('Tipo_Direc')
            ->select('Desc_Tip_Dir as tipo')
            ->where('Id_Tip_Dir', $persona->Id_Tip_Dir)
            ->first();

        // 🔹 Armar respuesta
        $data = [
            'codigo' => $persona->Codigo,
            'dni' => $dni,
            'nombre_completo' => trim("{$persona->Nombres} {$persona->Paterno} {$persona->Materno}"),
            'paterno' => trim($persona->Paterno),
            'materno' => trim($persona->Materno),
            'nombres' => trim($persona->Nombres),
            'fecha_nacimiento' => $persona->Fech_Nac,
            'correo' => $persona->E_Mail,
            'celular' => $persona->Telefono,
            'direccion' => [
                'tipo' => $tipoDireccion?->tipo,
                'descripcion' => $persona->Desc_Dir,
                'numero' => $persona->Num_Dir,
                'departamento' => $departamento?->departamento,
                'provincia' => $provincia?->provincia,
                'distrito' => $distrito?->distrito,
            ]
        ];

        $response = [
            'message' => 'Consulta realizada exitosamente.',
            'data' => $data
        ];

        return response()->json(JWT::encode($response, env('VITE_SECRET_KEY'), 'HS512'), 200);
    }

    public function verificarDNI($dni)
    {
        $persona = Persona::where('dni', $dni)->first();
        if($persona) return true;
        else return false;
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|unique:personas,codigo',
            'dni' => 'required|string|size:8|unique:personas,dni',
            'paterno' => 'required|string',
            'materno' => 'required|string',
            'nombres' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'correo' => 'required|email|unique:personas,correo',
            'celular' => 'nullable|string',
            'direccion.tipo' => 'nullable|string',
            'direccion.descripcion' => 'nullable|string',
            'direccion.numero' => 'nullable|string',
            'direccion.departamento' => 'nullable|string',
            'direccion.provincia' => 'nullable|string',
            'direccion.distrito' => 'nullable|string',
        ]);

        try{
            DB::beginTransaction();

            $persona = Persona::create([
                'codigo' => $request->codigo,
                'dni' => $request->dni,
                'paterno' => $request->paterno,
                'materno' => $request->materno,
                'nombres' => $request->nombres,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'correo' => $request->correo,
                'celular' => $request->celular,
                'direccion' => $request->direccion,
            ]);

            DB::commit();
            $response = [
                'message' => 'Usuario registrado exitosamente',
                'persona' => $persona,
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
        $persona = Persona::findOrFail($request->id);

        $validated = $request->validate([
            'codigo'          => 'nullable|string|max:20',
            'dni'             => 'required|string|max:15|unique:personas,dni,' . $persona->id,
            'paterno'         => 'required|string|max:100',
            'materno'         => 'required|string|max:100',
            'nombres'         => 'required|string|max:150',
            'fecha_nacimiento'=> 'nullable|date',
            'correo'          => 'nullable|email|max:150',
            'celular'         => 'nullable|string|max:20',
            'direccion'       => 'nullable|array',
        ]);

        $persona->update($validated);

        return response()->json([
            'message' => 'Persona actualizada correctamente',
            'data'    => $persona->load('usuario')
        ]);
    }

}
