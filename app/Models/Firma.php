<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Firma extends Model
{
    protected $table = 'firmas';

    protected $fillable = [
        'usuario_id',
        'historial_proceso_id',
        'tipo_firma',
        'firma_hash',
        'certificado_path',
        'pdf_firmado_path',
        'valido',
        'fecha_firma'
    ];

    protected $casts = [
        'tipo_firma' => 'string',
        'valido' => 'boolean',
        'fecha_firma' => 'datetime',
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function historialProceso()
    {
        return $this->belongsTo(HistorialProceso::class);
    }
}
