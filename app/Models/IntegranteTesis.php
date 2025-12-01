<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegranteTesis extends Model
{
    protected $table = 'integrante_tesis';

    protected $fillable = [
        'tesis_id',
        'persona_id',
        'rol'
    ];

    protected $casts = [
        'rol' => 'string',
    ];

    // Relaciones
    public function tesis()
    {
        return $this->belongsTo(Tesis::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
}
