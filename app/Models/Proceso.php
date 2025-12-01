<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proceso extends Model
{
    protected $table = 'procesos';

    protected $fillable = [
        'tramite_id',
        'numero_tramite',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    // Relaciones
    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    public function tesis()
    {
        return $this->hasOne(Tesis::class);
    }

    public function historiales()
    {
        return $this->hasMany(HistorialProceso::class)->orderBy('created_at', 'desc');
    }

    public function destinatarios()
    {
        return $this->hasMany(Destinatario::class);
    }

    public function requisitosProceso()
    {
        return $this->hasMany(RequisitoProceso::class);
    }

    public function formatosProceso()
    {
        return $this->hasMany(FormatoProceso::class);
    }
}
