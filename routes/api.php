<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\SignatureController;

Route::get('/signature/temp-path', [SignatureController::class, 'getTempDocumentPath']);
Route::post('/signature/store', [SignatureController::class, 'storeSignedDocument']);
Route::get('/signature/download/{id}', [SignatureController::class, 'download']);


Route::post('/firma/upload-callback', function (Request $request) {

    $request->validate([
        'documento_firmado' => 'required|file|mimes:pdf|max:10240',
    ]);

    $file = $request->file('documento_firmado');
    $nombre_final = time() . '_' . $file->getClientOriginalName();

    $file->storeAs('documentos_firmados', $nombre_final);


    return response()->json([
        'message' => 'Archivo firmado recibido y guardado con éxito.',
        'filename' => $nombre_final
    ], 200);
})->name('firma.upload');


// Perfiles
Route::prefix('perfiles')->group(function () {
    Route::get('/', [PerfilController::class, 'index']);
    Route::get('/activos', [PerfilController::class, 'activos']);
    Route::post('/', [PerfilController::class, 'store']);
    Route::post('/update', [PerfilController::class, 'update']);
});

//Niveles
Route::get('/obtener-niveles-academicos-unheval', [UnhevalController::class, 'obtenerNiveles']);
Route::get('/niveles-academicos-activos', [NivelController::class, 'index']);
Route::post('/actualizar-niveles-academicos', [NivelController::class, 'store']);

use App\Http\Controllers\MembreteController;
use App\Http\Controllers\FormatoController;

// Membretes
Route::prefix('membretes')->group(function () {
    Route::get('/', [MembreteController::class, 'index']);
    Route::get('/activos', [MembreteController::class, 'active']);
    Route::post('/', [MembreteController::class, 'store']);
    Route::post('/{id}', [MembreteController::class, 'update']);
});

// Formatos
Route::prefix('formatos')->group(function () {
    Route::get('/', [FormatoController::class, 'index']);
    Route::get('/activos', [FormatoController::class, 'active']);
    Route::post('/', [FormatoController::class, 'store']);
    Route::post('/{id}', [FormatoController::class, 'update']);
});

use App\Http\Controllers\TramiteController;

// Trámites
Route::prefix('tramites')->group(function () {
    Route::get('/', [TramiteController::class, 'index']);
    Route::get('/activos', [TramiteController::class, 'active']);
    Route::get('/{id}', [TramiteController::class, 'show']);
    Route::post('/', [TramiteController::class, 'store']);
    Route::post('/{id}', [TramiteController::class, 'update']);
    Route::delete('/{id}', [TramiteController::class, 'destroy']);
});
