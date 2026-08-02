<?php

namespace App\Models\Departamentos;

use App\Models\Ciudades\ciudad;
use App\Models\Estados\Estado;
use App\Models\Paises\Pais;
use App\Models\Provedores\provedor;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos';

    protected $fillable = [
        'nombre',
        'codigo',
        'pais_id',
        'estado_id',
        'creado_por',
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function ciudades()
    {
        return $this->hasMany(ciudad::class, 'departamento_id');
    }

    public function proveedores()
    {
        return $this->hasMany(provedor::class, 'departamento_id');
    }
}
