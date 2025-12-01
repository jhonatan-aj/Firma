<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destinatario extends Model
{
    protected $table = 'destinatarios';

    protected $fillable = [
        'proceso_id',
        'usuario_id',
        'historial_proceso_id',
        'estado',
        'fecha_recepcion'
    ];

    protected $casts = [
        'estado' => 'string',
        'fecha_recepcion' => 'datetime',
    ];

    // Relaciones
    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function historialProceso()
    {
        return $this->belongsTo(HistorialProceso::class);
    }
}
