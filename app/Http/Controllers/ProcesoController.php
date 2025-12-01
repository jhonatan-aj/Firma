<?php

namespace App\Http\Controllers;

use App\Models\Proceso;
use App\Models\Tesis;
use App\Models\IntegranteTesis;
use App\Models\Destinatario;
use App\Models\RequisitoProceso;
use App\Models\DocumentoRequisito;
use App\Models\RequisitoFirma;
use App\Models\FormatoProceso;
use App\Models\HistorialProceso;
use App\Services\FormatoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProcesoController extends Controller
{
    protected $formatoService;

    public function __construct(FormatoService $formatoService)
    {
        $this->formatoService = $formatoService;
    }


    /**
     * POST /api/procesos/previsualizar
     * Paso 1: Generar PDF temporal para previsualización
     */
    public function previsualizar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'general.titulo' => 'required|string|max:500',
            'general.nivel' => 'required|exists:niveles,id',
            'general.mencion' => 'required|exists:menciones,id',
            'general.tesistas' => 'required|array|min:1',
            'general.tesistas.*' => 'exists:personas,id',

            'documento.formato' => 'required|exists:formatos,id',
            'documento.destinatario' => 'required|array|min:1',
            'documento.destinatario.*' => 'exists:usuarios,id',
            'documento.sumilla' => 'required|string',
            'documento.fundamento' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // Crear instancias temporales (no guardadas en DB) para el servicio
            $tesis = new Tesis([
                'titulo' => $request->general['titulo'],
                'nivel_id' => $request->general['nivel'],
                'mencion_id' => $request->general['mencion'],
            ]);

            // Cargar relaciones manualmente para el servicio
            $tesis->setRelation('nivel', \App\Models\Nivel::find($request->general['nivel']));
            $tesis->setRelation('mencion', \App\Models\Mencion::find($request->general['mencion']));

            // Simular relación de tesistas
            $tesistas = \App\Models\IntegranteTesis::hydrate(
                collect($request->general['tesistas'])->map(function ($id) {
                    return ['persona_id' => $id, 'rol' => 'tesista'];
                })->toArray()
            );
            $tesistas->each(function ($integrante) {
                $integrante->setRelation('persona', \App\Models\Persona::find($integrante->persona_id));
            });
            $tesis->setRelation('integrantes', $tesistas);

            // Generar PDF temporal
            $resultadoPdf = $this->formatoService->generarPDF(
                $request->documento['formato'],
                $request->documento['sumilla'],
                $request->documento['fundamento'],
                $tesis,
                $request->documento['destinatario'],
                'digital' // Siempre generar en temp para previsualizar
            );

            return response()->json([
                'success' => true,
                'message' => 'Previsualización generada',
                'data' => [
                    'pdf_url' => asset('storage/' . $resultadoPdf['path']),
                    'pdf_path' => $resultadoPdf['path'] // Para enviar de vuelta al confirmar
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/procesos/registrar
     * Paso 2: Confirmar y registrar el proceso (Firma Manual o Digital)
     */
    public function registrar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tramite_id' => 'required|exists:tramites,id',
            'tipo_firma' => 'required|in:digital,manual',

            // Datos necesarios para recrear el proceso
            'general.titulo' => 'required|string',
            'general.nivel' => 'required|exists:niveles,id',
            'general.mencion' => 'required|exists:menciones,id',
            'general.tesistas' => 'required|array',

            'documento.formato' => 'required|exists:formatos,id',
            'documento.destinatario' => 'required|array',
            'documento.sumilla' => 'required|string',
            'documento.fundamento' => 'required|string',

            // Validación condicional según tipo de firma
            'archivo_firmado' => 'required_if:tipo_firma,manual|file|mimes:pdf|max:10240',
            'pdf_path_temp' => 'required_if:tipo_firma,digital|string', // Path del PDF generado en previsualización
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Lógica de Firma
            $pdfFinalPath = null;
            $firmaValida = false;

            if ($request->tipo_firma === 'manual') {
                // Guardar archivo subido
                $file = $request->file('archivo_firmado');
                $nombreArchivo = uniqid('firmado_manual_') . '.pdf';
                $pdfFinalPath = $file->storeAs('documentos/firmados', $nombreArchivo);
                $firmaValida = true;
            } else {
                // Firma Digital: Ejecutar Lanzador Python
                $inputPath = storage_path('app/' . $request->pdf_path_temp);
                $outputPath = storage_path('app/documentos/firmados/' . uniqid('firmado_digital_') . '.pdf');

                // Asegurar directorio destino
                if (!file_exists(dirname($outputPath))) {
                    mkdir(dirname($outputPath), 0755, true);
                }

                // Ejecutar lanzador (Simulado por ahora)
                $resultadoFirma = $this->ejecutarLanzadorPython($inputPath, $outputPath);

                if (!$resultadoFirma['success']) {
                    throw new \Exception('Error en firma digital: ' . $resultadoFirma['message']);
                }

                // Convertir path absoluto a relativo para storage
                $pdfFinalPath = 'documentos/firmados/' . basename($outputPath);
                $firmaValida = true;
            }

            // 2. Crear Proceso y Relaciones (Igual que antes)
            $proceso = Proceso::create([
                'tramite_id' => $request->tramite_id,
                'numero_tramite' => $this->generarNumeroTramite(),
                'estado' => 'en_proceso', // Ya nace firmado/en proceso
            ]);

            $tesis = Tesis::create([
                'titulo' => $request->general['titulo'],
                'nivel_id' => $request->general['nivel'],
                'mencion_id' => $request->general['mencion'],
                'proceso_id' => $proceso->id,
            ]);

            foreach ($request->general['tesistas'] as $personaId) {
                IntegranteTesis::create(['tesis_id' => $tesis->id, 'persona_id' => $personaId, 'rol' => 'tesista']);
            }

            // 4. Registrar Formato Generado y Firma
            FormatoProceso::create([
                'formato_id' => $request->documento['formato'],
                'proceso_id' => $proceso->id,
                'sumilla' => $request->documento['sumilla'],
                'fundamento' => $request->documento['fundamento'],
                'pdf_generado_path' => $pdfFinalPath, // Guardamos el PDF final firmado
                'tipo_firma' => $request->tipo_firma,
            ]);

            $historial = HistorialProceso::create([
                'proceso_id' => $proceso->id,
                'usuario_id' => auth()->id() ?? 1,
                'accion' => 'registro_firma',
                'comentario' => 'Proceso registrado con firma ' . $request->tipo_firma,
            ]);

            \App\Models\Firma::create([
                'usuario_id' => auth()->id() ?? 1,
                'historial_proceso_id' => $historial->id,
                'tipo_firma' => $request->tipo_firma,
                'pdf_firmado_path' => $pdfFinalPath,
                'valido' => $firmaValida,
                'fecha_firma' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proceso registrado exitosamente',
                'data' => [
                    'proceso_id' => $proceso->id,
                    'numero_tramite' => $proceso->numero_tramite,
                    'pdf_url' => asset('storage/' . $pdfFinalPath)
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Simulación del lanzador Python
     */
    private function ejecutarLanzadorPython($inputPath, $outputPath)
    {
        // TODO: Implementar llamada real: exec("python firmador.py '$inputPath' '$outputPath'");

        // Simulación: Copiar el archivo input a output (como si se hubiera firmado)
        try {
            copy($inputPath, $outputPath);
            return ['success' => true, 'message' => 'Firma digital simulada exitosa'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * GET /api/procesos/{id}
     * Obtener detalles completos de un proceso
     */
    public function show($id)
    {
        $proceso = Proceso::with([
            'tramite',
            'tesis.nivel',
            'tesis.mencion',
            'tesis.integrantes.persona',
            'destinatarios.usuario.persona',
            'requisitosProceso.requisito',
            'requisitosProceso.documentos.requisitoFirma',
            'formatosProceso.formato',
            'historiales.usuario.persona',
        ])->find($id);

        if (!$proceso) {
            return response()->json([
                'success' => false,
                'message' => 'Proceso no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $proceso
        ]);
    }

    /**
     * GET /api/procesos
     * Listar todos los procesos con filtros
     */
    public function index(Request $request)
    {
        $query = Proceso::with([
            'tramite',
            'tesis',
            'destinatarios.usuario.persona',
        ]);

        // Filtro por estado
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por trámite
        if ($request->has('tramite_id')) {
            $query->where('tramite_id', $request->tramite_id);
        }

        // Filtro por fecha
        if ($request->has('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $procesos = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $procesos
        ]);
    }

    /**
     * POST /api/procesos/{id}/firmar-manual
     * Recibir PDF firmado manualmente
     */
    public function firmarManual($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pdf_firmado' => 'required|file|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $proceso = Proceso::find($id);
        if (!$proceso) {
            return response()->json([
                'success' => false,
                'message' => 'Proceso no encontrado'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Guardar PDF firmado
            $file = $request->file('pdf_firmado');
            $nombreArchivo = uniqid('firmado_') . '.pdf';
            $path = $file->storeAs('documentos/firmados', $nombreArchivo);

            // Crear historial
            $historial = HistorialProceso::create([
                'proceso_id' => $proceso->id,
                'usuario_id' => auth()->id() ?? 1,
                'accion' => 'firma',
                'comentario' => 'Documento firmado manualmente',
            ]);

            // Crear registro de firma
            \App\Models\Firma::create([
                'usuario_id' => auth()->id() ?? 1,
                'historial_proceso_id' => $historial->id,
                'tipo_firma' => 'manual',
                'pdf_firmado_path' => $path,
                'valido' => true,
                'fecha_firma' => now(),
            ]);

            // Actualizar estado del proceso
            $proceso->update(['estado' => 'en_proceso']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Firma manual registrada correctamente',
                'data' => [
                    'pdf_firmado_url' => asset('storage/' . $path)
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar firma: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/procesos/{id}/firmar-digital
     * Activar firma digital (lanzador Python - TODO)
     */
    public function firmarDigital($id, Request $request)
    {
        $proceso = Proceso::find($id);
        if (!$proceso) {
            return response()->json([
                'success' => false,
                'message' => 'Proceso no encontrado'
            ], 404);
        }

        // TODO: Implementar activación del lanzador Python
        // Por ahora retornar mensaje informativo

        return response()->json([
            'success' => true,
            'message' => 'Firma digital - Lanzador Python (pendiente de implementación)',
            'data' => [
                'proceso_id' => $proceso->id,
                'instrucciones' => 'Activar lanzador Python para firma digital'
            ]
        ]);
    }

    /**
     * Generar número de trámite único
     */
    private function generarNumeroTramite()
    {
        $year = date('Y');
        $ultimo = Proceso::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimo && $ultimo->numero_tramite) {
            // Extraer el número del formato TR-2025-00001
            $partes = explode('-', $ultimo->numero_tramite);
            $numero = isset($partes[2]) ? intval($partes[2]) + 1 : 1;
        } else {
            $numero = 1;
        }

        return 'TR-' . $year . '-' . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }
}
