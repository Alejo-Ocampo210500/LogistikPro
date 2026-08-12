<?php

namespace App\Models\Sucursales;

use App\Models\Caja\Caja;
use App\Models\Ciudades\ciudad;
use App\Models\ControlCajas\ControlCaja;
use App\Models\Departamentos\Departamento;
use App\Models\Empresas\Empresa;
use App\Models\Paises\Pais;
use App\Models\User;
use App\Models\Ventas\Venta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Sucursal extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'sucursales';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nombre',
        'nit',
        'direccion',
        'telefono',
        'email',
        'ciudad_id',
        'departamento_id',
        'pais_id',
        'responsable',
        'estado',
        'created_by',
        'updated_by'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function ciudad()
    {
        return $this->belongsTo(ciudad::class, 'ciudad_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function cajas()
    {
        return $this->hasMany(Caja::class, 'sucursal_id');
    }

    public function controlCajas()
    {
        return $this->hasMany(ControlCaja::class, 'sucursal_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'sucursal_id');
    }
}
