<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitoProceso extends Model
{
    protected $table = 'requisito_proceso';

    protected $fillable = [
        'requisito_id',
        'proceso_id',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    // Relaciones
    public function requisito()
    {
        return $this->belongsTo(Requisito::class);
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoRequisito::class);
    }
}
