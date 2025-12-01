<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tesis extends Model
{
    protected $table = 'tesis';

    protected $fillable = [
        'titulo',
        'nivel_id',
        'mencion_id',
        'proceso_id',
        'descripcion'
    ];

    // Relaciones
    public function nivel()
    {
        return $this->belongsTo(Nivel::class);
    }

    public function mencion()
    {
        return $this->belongsTo(Mencion::class);
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function integrantes()
    {
        return $this->hasMany(IntegranteTesis::class);
    }

    public function tesistas()
    {
        return $this->integrantes()->where('rol', 'tesista')->with('persona');
    }

    public function asesores()
    {
        return $this->integrantes()->where('rol', 'asesor')->with('persona');
    }

    public function jurados()
    {
        return $this->integrantes()->where('rol', 'jurado')->with('persona');
    }
}
