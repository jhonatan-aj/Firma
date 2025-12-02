<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisito extends Model
{
    protected $table = 'requisitos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'tipo',
        'tramite_id',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    // Relación con trámite (un requisito pertenece a un trámite)
    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'tramite_id');
    }

    // Relación con plantillas (un requisito puede tener múltiples plantillas)
    public function plantillas()
    {
        return $this->hasMany(PlantillaRequisito::class);
    }

    // Scope para requisitos activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
