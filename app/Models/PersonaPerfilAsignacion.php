<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonaPerfilAsignacion extends Model
{
    use HasFactory;

    protected $table = 'persona_perfil_asignacion';

    protected $fillable = [
        'usuario_id',
        'perfil_id',
        'nivel_id',
        'menciones',
        'oficina_id',
        'puesto_id',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'menciones' => 'array',
        'usuario_id' => 'integer',
        'nivel_id' => 'integer',
        'perfil_id' => 'integer',
        'oficina_id' => 'integer',
        'puesto_id' => 'integer',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function perfil()
    {
        return $this->belongsTo(Perfil::class);
    }

    public function nivel()
    {
        return $this->belongsTo(Nivel::class);
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class);
    }
}
