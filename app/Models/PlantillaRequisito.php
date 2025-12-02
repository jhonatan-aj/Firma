<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaRequisito extends Model
{
    protected $table = 'documentos_requisito';

    protected $fillable = [
        'requisito_id',
        'nombre',
        'ruta_archivo',
        'tipo_archivo',
        'descripcion',
        'activo'
    ];

    protected $casts = [
        'tipo_archivo' => 'string',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function requisito()
    {
        return $this->belongsTo(Requisito::class);
    }
}
