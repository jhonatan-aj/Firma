<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formato extends Model
{
    protected $table = 'formatos';

    protected $fillable = [
        'nombre',
        'estado',
        'membrete_id',
        'utilizado',
        'tipo',
        'contenido',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'utilizado' => 'boolean',
    ];

    public function membrete()
    {
        return $this->belongsTo(Membrete::class, 'membrete_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    // Relación con trámites (many-to-many)
    public function tramites()
    {
        return $this->belongsToMany(Tramite::class, 'formato_tramite', 'formato_id', 'tramite_id')
            ->withTimestamps();
    }
}
