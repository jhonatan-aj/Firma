<?php

namespace App\Http\Controllers;

use App\Models\SignedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SignatureController extends Controller
{

    public function getTempDocumentPath()
    {
        $relativePath = 'temp/documento.pdf';

        if (!Storage::exists($relativePath)) {
            return response()->json(['error' => 'Documento temporal no encontrado. Asegúrate de generar uno en storage/app/temp/documento.pdf'], 404);
        }

        $absolutePath = Storage::path($relativePath);

        return response()->json([
            'path' => $absolutePath,
            'filename' => 'documento.pdf'
        ]);
    }

    public function storeSignedDocument(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        $storedPath = $file->storeAs('signed', time() . '_' . $originalName);

        $document = SignedDocument::create([
            'original_name' => $originalName,
            'file_path' => $storedPath,
            'status' => 'completed' // Ya llega firmado
        ]);

        return response()->json([
            'message' => 'Documento firmado guardado exitosamente',
            'data' => $document
        ]);
    }

    public function download($id)
    {
        $document = SignedDocument::find($id);

        if (!$document) {
            return response()->json(['error' => 'Documento no encontrado'], 404);
        }

        if (!$document->file_path || !Storage::exists($document->file_path)) {
            return response()->json(['error' => 'Archivo físico no encontrado'], 404);
        }

        return Storage::download($document->file_path, $document->original_name);
    }
}
