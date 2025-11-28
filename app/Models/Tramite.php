<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tramite extends Model
{
    protected $table = 'tramites';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'estado',
        'obligatorio',
        'dirigido',
        'tipo',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'obligatorio' => 'boolean',
        'dirigido' => 'boolean',
    ];

    // requisitoss
    public function requisitos()
    {
        return $this->hasMany(Requisito::class, 'tramite_id');
    }

    // Relación con formatos 
    public function formatos()
    {
        return $this->belongsToMany(Formato::class, 'formato_tramite', 'tramite_id', 'formato_id')
            ->withTimestamps();
    }

    // otros trámites que deben completarse antes
    public function prerequisitos()
    {
        return $this->belongsToMany(Tramite::class, 'tramite_prerequisito', 'tramite_id', 'prerequisito_id')
            ->withTimestamps();
    }

    // trámites que tienen este trámite como prerequisito
    public function tramitesDependientes()
    {
        return $this->belongsToMany(Tramite::class, 'tramite_prerequisito', 'prerequisito_id', 'tramite_id')
            ->withTimestamps();
    }

    // Scope para trámites activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
