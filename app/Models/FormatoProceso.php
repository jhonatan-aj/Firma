<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormatoProceso extends Model
{
    protected $table = 'formato_proceso';

    protected $fillable = [
        'formato_id',
        'proceso_id',
        'historial_proceso_id',
        'sumilla',
        'fundamento',
        'pdf_generado_path',
        'tipo_firma'
    ];

    protected $casts = [
        'tipo_firma' => 'string',
    ];

    // Relaciones
    public function formato()
    {
        return $this->belongsTo(Formato::class);
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function historialProceso()
    {
        return $this->belongsTo(HistorialProceso::class);
    }
}
