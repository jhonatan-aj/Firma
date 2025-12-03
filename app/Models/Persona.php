<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $fillable = [
        'codigo',
        'dni',
        'paterno',
        'materno',
        'nombres',
        'fecha_nacimiento',
        'correo',
        'celular',
        'direccion',
    ];

    protected $casts = [
        'direccion' => 'array',
    ];

    protected $appends = ['nombre_completo', 'direccion_completa'];

    public function getNombreCompletoAttribute()
    {
        return "{$this->paterno} {$this->materno} {$this->nombres}";
    }

    public function getDireccionCompletaAttribute()
    {
        if (!$this->direccion) return null;

        return collect([
            $this->direccion['tipo'] ?? '',
            $this->direccion['descripcion'] ?? '',
            $this->direccion['numero'] ?? '',
            $this->direccion['distrito'] ?? '',
            $this->direccion['provincia'] ?? '',
            $this->direccion['departamento'] ?? '',
        ])->filter()->implode(' ');
    }

    public function usuarios()
    {
        return $this->hasOne(Usuario::class);
    }

    public function getCelularAttribute($value)
    {
        return $value ?? 'Sin registrar';
    }

    public function integrantesTesis()
    {
        return $this->hasMany(IntegranteTesis::class);
    }
}
