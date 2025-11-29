<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    

    protected $fillable = ['persona_id', 'usuario', 'password', 'estado'];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function asignaciones()
    {
        return $this->hasMany(PersonaPerfilAsignacion::class);
    }
}
