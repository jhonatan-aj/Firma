<?php

namespace App\Http\Controllers;

use App\Mail\SendVerificationCode;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    public function enviarCodigo(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string',
            'purpose' => 'required|in:register,reset',
        ]);

        $codigo = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $registro = VerificationCode::updateOrCreate(
            ['email' => $request->email, 'purpose' => $request->purpose],
            [
                'name' => $request->name,
                'code' => $codigo,
                'expires_at' => now()->addHour(),
            ]
        );

        Mail::to($request->email)->send(
            new SendVerificationCode($request->name, $codigo, $request->purpose)
        );

        $mensaje = $registro->wasRecentlyCreated
            ? "Código enviado exitosamente a {$request->email}"
            : "Código reenviado con éxito a {$request->email}";

        return response()->json(['message' => $mensaje], 200);
    }

    public function validarCodigo(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'purpose' => 'required|in:register,reset',
            'code' => 'required|digits:4',
        ]);

        $registro = VerificationCode::where('email', $request->email)
            ->where('purpose', $request->purpose)
            ->where('code', $request->code)
            ->orderByDesc('created_at')
            ->first();

        if (!$registro || $registro->isExpired()) {
            return response()->json(['valid' => false, 'message' => 'Código inválido o expirado, Reenviar'], 422);
        }

        return response()->json(['valid' => true, 'message' => 'Código válido'], 200);
    }
}
