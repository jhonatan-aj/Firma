<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitoProcesoHistorial extends Model
{
    protected $table = 'requisito_proceso_historial';

    protected $fillable = [
        'requisito_proceso_id',
        'historial_proceso_id',
        'observaciones'
    ];

    // Relaciones
    public function requisitoProceso()
    {
        return $this->belongsTo(RequisitoProceso::class);
    }

    public function historialProceso()
    {
        return $this->belongsTo(HistorialProceso::class);
    }
}
