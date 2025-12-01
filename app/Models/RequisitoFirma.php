<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitoFirma extends Model
{
    protected $table = 'requisito_firma';

    protected $fillable = [
        'documento_requisito_id',
        'usuario_id',
        'tipo_firma',
        'firmado',
        'fecha_firma',
        'pdf_firmado_path'
    ];

    protected $casts = [
        'tipo_firma' => 'string',
        'firmado' => 'boolean',
        'fecha_firma' => 'datetime',
    ];

    // Relaciones
    public function documentoRequisito()
    {
        return $this->belongsTo(DocumentoRequisito::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
