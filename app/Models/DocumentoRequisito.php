<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoRequisito extends Model
{
    protected $table = 'documento_requisito';

    protected $fillable = [
        'requisito_proceso_id',
        'nombre_original',
        'nombre_almacenado',
        'path_archivo',
        'tipo_archivo',
        'mime_type',
        'tamano_bytes'
    ];

    protected $casts = [
        'tipo_archivo' => 'string',
        'tamano_bytes' => 'integer',
    ];

    // Relaciones
    public function requisitoProceso()
    {
        return $this->belongsTo(RequisitoProceso::class);
    }

    public function requisitoFirma()
    {
        return $this->hasOne(RequisitoFirma::class);
    }
}
