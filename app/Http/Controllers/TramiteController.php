<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use App\Models\Requisito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TramiteController extends Controller
{

    public function index()
    {
        $tramites = Tramite::with(['prerequisitos', 'requisitos', 'formatos.membrete'])->get();

        $data = $tramites->map(function ($tramite) {
            return [
                'id' => $tramite->id,
                'codigo' => $tramite->codigo,
                'nombre' => $tramite->nombre,
                'descripcion' => $tramite->descripcion,
                'estado' => $tramite->estado,
                'obligatorio' => $tramite->obligatorio,
                'dirigido' => $tramite->dirigido,
                'tipo' => $tramite->tipo,

                // IDs simples para edición
                'prerequisito' => $tramite->prerequisitos->pluck('id')->toArray(),
                'requisito' => $tramite->requisitos->pluck('id')->toArray(),
                'formato' => $tramite->formatos->pluck('id')->toArray(),
                'prerequisitos' => $tramite->prerequisitos->map(function ($prereq) {
                    return [
                        'id' => $prereq->id,
                        'codigo' => $prereq->codigo,
                        'nombre' => $prereq->nombre,
                        'descripcion' => $prereq->descripcion,
                        'estado' => $prereq->estado,
                        'obligatorio' => $prereq->obligatorio,
                        'dirigido' => $prereq->dirigido,
                        'tipo' => $prereq->tipo,
                    ];
                })->toArray(),

                'requisitos' => $tramite->requisitos->map(function ($req) {
                    return [
                        'id' => $req->id,
                        'nombre' => $req->nombre,
                        'descripcion' => $req->descripcion,
                        'estado' => $req->estado,
                        'tipo' => $req->tipo,
                        'tramite_id' => $req->tramite_id,
                    ];
                })->toArray(),

                'formatos' => $tramite->formatos->map(function ($formato) {
                    return [
                        'id' => $formato->id,
                        'estado' => $formato->estado,
                        'nombre' => $formato->nombre,
                        'membrete' => $formato->membrete_id,
                        'utilizado' => $formato->utilizado,
                        'tipo' => $formato->tipo,
                        'contenido' => $formato->contenido,
                    ];
                })->toArray(),
            ];
        });

        return response()->json($data);
    }

    /**
     * Listar solo trámites activos
     */
    public function active()
    {
        $tramites = Tramite::activos()->with(['prerequisitos', 'requisitos', 'formatos.membrete'])->get();

        $data = $tramites->map(function ($tramite) {
            return [
                'id' => $tramite->id,
                'codigo' => $tramite->codigo,
                'nombre' => $tramite->nombre,
                'descripcion' => $tramite->descripcion,
                'estado' => $tramite->estado,
                'obligatorio' => $tramite->obligatorio,
                'dirigido' => $tramite->dirigido,
                'tipo' => $tramite->tipo,
                'prerequisito' => $tramite->prerequisitos->pluck('id')->toArray(),
                'requisito' => $tramite->requisitos->pluck('id')->toArray(),
                'formato' => $tramite->formatos->pluck('id')->toArray(),
                'prerequisitos' => $tramite->prerequisitos,
                'requisitos' => $tramite->requisitos,
                'formatos' => $tramite->formatos,
            ];
        });

        return response()->json($data);
    }

    /**
     * Obtener un trámite específico por ID
     */
    public function show($id)
    {
        $tramite = Tramite::with(['prerequisitos', 'requisitos', 'formatos.membrete'])->find($id);

        if (!$tramite) {
            return response()->json([
                'status' => false,
                'message' => 'Trámite no encontrado'
            ], 404);
        }

        $data = [
            'id' => $tramite->id,
            'codigo' => $tramite->codigo,
            'nombre' => $tramite->nombre,
            'descripcion' => $tramite->descripcion,
            'estado' => $tramite->estado,
            'obligatorio' => $tramite->obligatorio,
            'dirigido' => $tramite->dirigido,
            'tipo' => $tramite->tipo,
            'prerequisito' => $tramite->prerequisitos->pluck('id')->toArray(),
            'requisito' => $tramite->requisitos->pluck('id')->toArray(),
            'formato' => $tramite->formatos->pluck('id')->toArray(),
            'prerequisitos' => $tramite->prerequisitos,
            'requisitos' => $tramite->requisitos,
            'formatos' => $tramite->formatos,
        ];

        return response()->json($data);
    }

    /**
     * Crear un nuevo trámite
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|unique:tramites,codigo',
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean',
            'obligatorio' => 'required|boolean',
            'dirigido' => 'required|boolean',
            'tipo' => 'required|in:tesis,interno,otro',
            'prerequisito' => 'nullable|array',
            'prerequisito.*' => 'exists:tramites,id',
            'requisito' => 'nullable|array',
            'requisito.*.nombre' => 'required|string',
            'requisito.*.descripcion' => 'nullable|string',
            'requisito.*.estado' => 'required|boolean',
            'requisito.*.tipo' => 'required|in:obligatorio,opcional',
            'formato' => 'nullable|array',
            'formato.*' => 'exists:formatos,id',
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
            // Crear el trámite
            $tramite = Tramite::create([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado,
                'obligatorio' => $request->obligatorio,
                'dirigido' => $request->dirigido,
                'tipo' => $request->tipo,
            ]);

            // Asociar prerequisitos (otros trámites)
            if ($request->has('prerequisito') && is_array($request->prerequisito)) {
                $tramite->prerequisitos()->sync($request->prerequisito);
            }

            // Crear requisitos asociados
            if ($request->has('requisito') && is_array($request->requisito)) {
                foreach ($request->requisito as $reqData) {
                    Requisito::create([
                        'nombre' => $reqData['nombre'],
                        'descripcion' => $reqData['descripcion'] ?? null,
                        'estado' => $reqData['estado'],
                        'tipo' => $reqData['tipo'],
                        'tramite_id' => $tramite->id,
                    ]);
                }
            }

            // Asociar formatos
            if ($request->has('formato') && is_array($request->formato)) {
                $tramite->formatos()->sync($request->formato);
            }

            DB::commit();

            // Recargar con relaciones
            $tramite->load(['prerequisitos', 'requisitos', 'formatos']);

            return response()->json([
                'status' => true,
                'message' => 'Trámite creado correctamente',
                'data' => [
                    'id' => $tramite->id,
                    'codigo' => $tramite->codigo,
                    'nombre' => $tramite->nombre,
                    'descripcion' => $tramite->descripcion,
                    'estado' => $tramite->estado,
                    'obligatorio' => $tramite->obligatorio,
                    'dirigido' => $tramite->dirigido,
                    'tipo' => $tramite->tipo,
                    'prerequisito' => $tramite->prerequisitos->pluck('id')->toArray(),
                    'requisito' => $tramite->requisitos->pluck('id')->toArray(),
                    'formato' => $tramite->formatos->pluck('id')->toArray(),
                    'prerequisitos' => $tramite->prerequisitos,
                    'requisitos' => $tramite->requisitos,
                    'formatos' => $tramite->formatos,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error al crear el trámite: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un trámite existente
     */
    public function update(Request $request, $id)
    {
        $tramite = Tramite::find($id);

        if (!$tramite) {
            return response()->json([
                'status' => false,
                'message' => 'Trámite no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|unique:tramites,codigo,' . $id,
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean',
            'obligatorio' => 'required|boolean',
            'dirigido' => 'required|boolean',
            'tipo' => 'required|in:tesis,interno,otro',
            'prerequisito' => 'nullable|array',
            'prerequisito.*' => 'exists:tramites,id',
            'requisito' => 'nullable|array',
            'requisito.*.id' => 'nullable|exists:requisitos,id',
            'requisito.*.nombre' => 'required|string',
            'requisito.*.descripcion' => 'nullable|string',
            'requisito.*.estado' => 'required|boolean',
            'requisito.*.tipo' => 'required|in:obligatorio,opcional',
            'formato' => 'nullable|array',
            'formato.*' => 'exists:formatos,id',
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
            // Actualizar datos básicos del trámite
            $tramite->update([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado,
                'obligatorio' => $request->obligatorio,
                'dirigido' => $request->dirigido,
                'tipo' => $request->tipo,
            ]);

            if ($request->has('prerequisito')) {
                $tramite->prerequisitos()->sync($request->prerequisito ?? []);
            }

            if ($request->has('requisito')) {
                if (is_array($request->requisito)) {
                    $requisitosActualizados = collect($request->requisito);

                    $idsEnPeticion = $requisitosActualizados->pluck('id')->filter();

                    // Eliminar requisitos que ya no están en la petición
                    $tramite->requisitos()->whereNotIn('id', $idsEnPeticion)->delete();

                    foreach ($requisitosActualizados as $reqData) {
                        if (isset($reqData['id']) && $reqData['id']) {
                            $tramite->requisitos()->where('id', $reqData['id'])->update([
                                'nombre' => $reqData['nombre'],
                                'descripcion' => $reqData['descripcion'] ?? null,
                                'estado' => $reqData['estado'],
                                'tipo' => $reqData['tipo'],
                            ]);
                        } else {
                            Requisito::create([
                                'nombre' => $reqData['nombre'],
                                'descripcion' => $reqData['descripcion'] ?? null,
                                'estado' => $reqData['estado'],
                                'tipo' => $reqData['tipo'],
                                'tramite_id' => $tramite->id,
                            ]);
                        }
                    }
                }
            }

            if ($request->has('formato')) {
                $tramite->formatos()->sync($request->formato ?? []);
            }

            DB::commit();

            $tramite->load(['prerequisitos', 'requisitos', 'formatos']);

            return response()->json([
                'status' => true,
                'message' => 'Trámite actualizado correctamente',
                'data' => [
                    'id' => $tramite->id,
                    'codigo' => $tramite->codigo,
                    'nombre' => $tramite->nombre,
                    'descripcion' => $tramite->descripcion,
                    'estado' => $tramite->estado,
                    'obligatorio' => $tramite->obligatorio,
                    'dirigido' => $tramite->dirigido,
                    'tipo' => $tramite->tipo,
                    'prerequisito' => $tramite->prerequisitos->pluck('id')->toArray(),
                    'requisito' => $tramite->requisitos->pluck('id')->toArray(),
                    'formato' => $tramite->formatos->pluck('id')->toArray(),
                    'prerequisitos' => $tramite->prerequisitos,
                    'requisitos' => $tramite->requisitos,
                    'formatos' => $tramite->formatos,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el trámite: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un trámite
     */
    public function destroy($id)
    {
        $tramite = Tramite::find($id);

        if (!$tramite) {
            return response()->json([
                'status' => false,
                'message' => 'Trámite no encontrado'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Las relaciones se eliminarán automáticamente por cascade
            $tramite->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Trámite eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el trámite: ' . $e->getMessage()
            ], 500);
        }
    }
}
