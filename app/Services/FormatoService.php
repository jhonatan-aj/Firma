<?php

namespace App\Services;

use App\Models\Formato;
use App\Models\Tesis;
use App\Models\Destinatario;
use Illuminate\Support\Facades\Storage;

class FormatoService
{
    /**
     * Generar PDF desde un formato con reemplazo de variables
     */
    public function generarPDF($formatoId, $sumilla, $fundamento, $tesis, $destinatariosIds, $tipoFirma = 'manual')
    {
        // Obtener formato con membrete
        $formato = Formato::with('membrete')->find($formatoId);
        if (!$formato) {
            throw new \Exception('Formato no encontrado');
        }

        $contenidoHtml = $formato->contenido;

        // Obtener datos para reemplazar variables
        $variables = $this->obtenerVariables($sumilla, $fundamento, $tesis, $destinatariosIds);

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
    private function obtenerVariables($sumilla, $fundamento, $tesis, $destinatariosIds)
    {
        // Nivel y mención
        $nivel = $tesis->nivel->nombre ?? '';
        $mencion = $tesis->mencion->nombre ?? '';

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
     * Convertir HTML a PDF
     */
    private function convertirHtmlAPdf($html, $tipoFirma = 'manual')
    {
        // Determinar directorio según tipo de firma
        // Firma digital → temp (temporal para proceso de firma)
        // Firma manual → storage/documentos (permanente)

        $directorio = $tipoFirma === 'digital' ? 'temp' : 'documentos/generados';
        $nombreArchivo = uniqid('doc_') . '.pdf';

        // TODO: Implementar conversión HTML a PDF
        // Opciones:
        // 1. Usar dompdf: $pdf = PDF::loadHTML($html);
        // 2. Usar wkhtmltopdf: exec("wkhtmltopdf ...")
        // 3. Usar Python: exec("python python/generar_pdf.py ...")

        // Por ahora, guardar el HTML como referencia
        $htmlPath = $directorio . '/' . str_replace('.pdf', '.html', $nombreArchivo);
        Storage::disk('public')->put($htmlPath, $html);

        // Crear un PDF dummy para que el link funcione en pruebas
        // En producción esto será reemplazado por el PDF real
        Storage::disk('public')->put($directorio . '/' . $nombreArchivo, $html); // Guardamos el HTML como "PDF" por ahora para que descargue algo

        return $directorio . '/' . $nombreArchivo;
    }

    /**
     * Generar PDF real (implementar según librería elegida)
     */
    private function generarPdfReal($html)
    {
        // TODO: Implementar con dompdf, wkhtmltopdf o Python
        return '';
    }
}
