<?php

namespace App\Models\Paises;

use App\Models\Clientes\Cliente;
use App\Models\Departamentos\Departamento;
use App\Models\Estados\Estado;
use App\Models\Provedores\provedor;
use App\Models\Sucursales\Sucursal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Pais extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'paises';

    protected $fillable = [
        'nombre',
        'codigo_iso',
        'estado_id',
        'creado_por',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'pais_id');
    }

    public function proveedores()
    {
        return $this->hasMany(provedor::class, 'pais_id');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'pais_id');
    }

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class, 'pais_id');
    }
}
