<?php

namespace App\Models\Empresas;

use App\Models\Caja\Caja;
use App\Models\Categorias\categoria;
use App\Models\Ciudades\ciudad;
use App\Models\Clientes\Cliente;
use App\Models\ControlCajas\ControlCaja;
use App\Models\Departamentos\Departamento;
use App\Models\Impuesto\impuesto;
use App\Models\InventarioCodigoBarras\InventarioCodigoBarra;
use App\Models\Marcas\marca;
use App\Models\Planes\Plan;
use App\Models\Producto\producto;
use App\Models\Provedores\provedor;
use App\Models\Sucursales\Sucursal;
use App\Models\Suscripcion;
use App\Models\UnidadMedida\UnidadMedida;
use App\Models\User;
use App\Models\Ventas\Venta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Empresa extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nombre_comercial',
        'razon_social',
        'nit',
        'email',
        'telefono',
        'direccion',
        'departamento_id',
        'ciudad_id',
        'ciudad',
        'departamento',
        'logo',
        'estado_id',
        'plan',
        'fecha_vencimiento',
        'plan_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_vencimiento' => 'datetime',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function ciudad()
    {
        return $this->belongsTo(ciudad::class, 'ciudad_id');
    }

    public function estado()
    {
        return $this->belongsTo(\App\Models\Estados\Estado::class);
    }

    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class);
    }

    public function categorias()
    {
        return $this->hasMany(categoria::class);
    }

    public function marcas()
    {
        return $this->hasMany(marca::class);
    }

    public function unidadesMedida()
    {
        return $this->hasMany(UnidadMedida::class);
    }

    public function proveedores()
    {
        return $this->hasMany(provedor::class);
    }

    public function impuestos()
    {
        return $this->hasMany(impuesto::class);
    }

    public function productos()
    {
        return $this->hasMany(producto::class);
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }

    public function cajas()
    {
        return $this->hasMany(Caja::class);
    }

    public function controlCajas()
    {
        return $this->hasMany(ControlCaja::class);
    }

    public function inventarioCodigosBarras()
    {
        return $this->hasMany(InventarioCodigoBarra::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

}
