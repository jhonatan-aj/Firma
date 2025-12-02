<?php

namespace App\Services;

use App\Models\Formato;
use App\Models\Tesis;
use App\Models\Destinatario;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class FormatoService
{
    /**
     * Generar PDF desde un formato con reemplazo de variables
     */
    public function generarPDF($formatoId, $sumilla, $fundamento, $tesis, $destinatariosIds, $tipoFirma = 'manual', $requisitos = [])
    {
        // Obtener formato con membrete
        $formato = Formato::with('membrete')->find($formatoId);
        if (!$formato) {
            throw new \Exception('Formato no encontrado');
        }

        $contenidoHtml = $formato->contenido;

        // Obtener datos para reemplazar variables
        $variables = $this->obtenerVariables($sumilla, $fundamento, $tesis, $destinatariosIds, $requisitos);

        // Reemplazar variables en el HTML
        $htmlProcesado = $this->reemplazarVariables($contenidoHtml, $variables);

        // Generar PDF
        $pdfPath = $this->convertirHtmlAPdf($htmlProcesado, $tipoFirma);

        return [
            'path' => $pdfPath,
            'variables' => $variables,
            'html' => $htmlProcesado
        ];
    }

    /**
     * Obtener todas las variables para reemplazo
     */
    private function obtenerVariables($sumilla, $fundamento, $tesis, $destinatariosIds, $requisitos = [])
    {
        // Nivel y mención
        $nivel = $tesis->nivel->nivel ?? '';
        $mencion = $tesis->mencion->mencion ?? '';

        // Tesistas (nombres completos separados por coma)
        $tesistas = $tesis->tesistas()->get()
            ->map(function ($integrante) {
                return $integrante->persona->nombre_completo ?? '';
            })
            ->filter()
            ->implode(', ');

        // Asesor (primer asesor si existe)
        $asesor = $tesis->asesores()->first()?->persona->nombre_completo ?? 'Sin asesor asignado';

        // Autoridad/Destinatarios (nombres completos separados por coma)
        $autoridad = \App\Models\Usuario::with('persona')
            ->whereIn('id', $destinatariosIds)
            ->get()
            ->map(function ($usuario) {
                return $usuario->persona->nombre_completo ?? '';
            })
            ->filter()
            ->implode(', ');

        // Fecha actual en español
        $fecha = now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY');

        // Requisitos presentados (lista HTML)
        $requisitosHtml = '';
        if (!empty($requisitos)) {
            $requisitosHtml = '<ul>';
            foreach ($requisitos as $req) {
                $nombreReq = $req['nombre'] ?? 'Requisito';
                $requisitosHtml .= "<li>{$nombreReq}</li>";
            }
            $requisitosHtml .= '</ul>';
        }

        return [
            '<<< Asunto >>>' => $sumilla,
            '<<< Contenido >>>' => $fundamento,
            '<<< Autoridad >>>' => $autoridad,
            '<<< Fecha >>>' => $fecha,
            '<<< Titulo >>>' => $tesis->titulo,
            '<<< Nivel >>>' => $nivel,
            '<<< Mencion >>>' => $mencion,
            '<<< Tesistas >>>' => $tesistas,
            '<<< Asesor >>>' => $asesor,
            '<<< Requisitos >>>' => $requisitosHtml,
        ];
    }

    /**
     * Reemplazar variables en el HTML
     */
    private function reemplazarVariables($html, $variables)
    {
        return str_replace(array_keys($variables), array_values($variables), $html);
    }

    /**
     * Convertir HTML a PDF usando dompdf
     */
    private function convertirHtmlAPdf($html, $tipoFirma = 'manual')
    {
        // Determinar directorio según tipo de firma
        // Firma digital → temp (temporal para proceso de firma)
        // Firma manual → storage/documentos (permanente)

        $directorio = $tipoFirma === 'digital' ? 'temp' : 'documentos/generados';
        $nombreArchivo = uniqid('doc_') . '.pdf';

        // Generar PDF con dompdf
        $pdf = Pdf::loadHTML($html);

        // Configuración adicional (opcional)
        $pdf->setPaper('A4', 'portrait'); // Tamaño y orientación

        // Guardar el PDF
        $rutaCompleta = storage_path('app/public/' . $directorio . '/' . $nombreArchivo);

        // Crear directorio si no existe
        if (!file_exists(dirname($rutaCompleta))) {
            mkdir(dirname($rutaCompleta), 0755, true);
        }

        // Guardar el PDF
        $pdf->save($rutaCompleta);

        return $directorio . '/' . $nombreArchivo;
    }
}
