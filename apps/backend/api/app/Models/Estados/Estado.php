<?php

namespace App\Models\Estados;

use App\Models\Caja\Caja;
use App\Models\Categorias\categoria;
use App\Models\Ciudades\ciudad;
use App\Models\Clientes\Cliente;
use App\Models\Departamentos\Departamento;
use App\Models\Impuesto\impuesto;
use App\Models\Marcas\marca;
use App\Models\Paises\Pais;
use App\Models\Planes\Plan;
use App\Models\Producto\producto;
use App\Models\Provedores\provedor;
use App\Models\Seguridad\Rol;
use App\Models\Suscripcion;
use App\Models\TiposDocumento\tipoDocumento;
use App\Models\UnidadMedida\UnidadMedida;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Estado extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'estados';

    protected $fillable = [
        'nombre'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function planes()
    {
        return $this->hasMany(Plan::class);
    }

    public function roles()
    {
        return $this->hasMany(Rol::class);
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

    public function paises()
    {
        return $this->hasMany(Pais::class);
    }

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'estado_id');
    }

    public function ciudades()
    {
        return $this->hasMany(ciudad::class, 'estado_id');
    }

    public function tiposDocumento()
    {
        return $this->hasMany(tipoDocumento::class, 'estado_id');
    }

    public function proveedores()
    {
        return $this->hasMany(provedor::class, 'estado_id');
    }

    public function impuestos()
    {
        return $this->hasMany(impuesto::class, 'estado_id');
    }

    public function productos()
    {
        return $this->hasMany(producto::class, 'estado_id');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'estado_id');
    }

    public function caja()
    {
        return $this->hasMany(Caja::class, 'estado_id');
    }
}
