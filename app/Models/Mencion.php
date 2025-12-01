<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mencion extends Model
{
    protected $table = 'menciones';

    protected $fillable = [
        'codigo',
        'facultad',
        'nivel',
        'mencion',
        'especialidad',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'nivel' => 'integer',
    ];

    public function nivelAcademico()
    {
        return $this->belongsTo(Nivel::class, 'nivel');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    public function scopePorNivel($query, $nivelId)
    {
        return $query->where('nivel', $nivelId);
    }

}
