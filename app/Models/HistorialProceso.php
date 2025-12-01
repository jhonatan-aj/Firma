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
        'comentario'
    ];

    protected $casts = [
        'accion' => 'string',
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
}
