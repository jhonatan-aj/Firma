<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Membrete extends Model
{
    protected $table = 'membretes';

    protected $fillable = [
        'nivel_id',
        'nivel_filtro',
        'nombre',
        'estado',
        'derecha',
        'izquierda',
        'centro',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function nivel()
    {
        return $this->belongsTo(Nivel::class, 'nivel_id');
    }

    public function formatos()
    {
        return $this->hasMany(Formato::class, 'membrete_id');
    }

    // Accessors for full image URLs
    public function getDerechaAttribute($value)
    {
        return $value ? url('storage/imagenes_membretados/' . $value) : null;
    }

    public function getIzquierdaAttribute($value)
    {
        return $value ? url('storage/imagenes_membretados/' . $value) : null;
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
