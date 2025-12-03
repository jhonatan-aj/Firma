<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialProceso extends Model
{
    protected $table = 'historial_procesos';

    protected $fillable = [
        'proceso_id',
        'usuario_id',
        'accion',
        'comentario',
        'ultimo',
        'recepcionado'
    ];

    protected $casts = [
        'accion' => 'string',
        'ultimo' => 'boolean',
        'recepcionado' => 'datetime',
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

    public function firmas()
    {
        return $this->hasMany(Firma::class);
    }

    public function destinatario()
    {
        return $this->hasOne(Destinatario::class);
    }

    public function requisitosProceso()
    {
        return $this->belongsToMany(
            RequisitoProceso::class,
            'requisito_proceso_historial',
            'historial_proceso_id',
            'requisito_proceso_id'
        )->withTimestamps()->withPivot('observaciones');
    }

    public function formatosProceso()
    {
        return $this->hasMany(FormatoProceso::class);
    }
}
