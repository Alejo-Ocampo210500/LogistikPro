<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Caja\Caja;
use App\Models\Categorias\categoria;
use App\Models\Ciudades\ciudad;
use App\Models\Clientes\Cliente;
use App\Models\ControlCajas\ControlCaja;
use App\Models\Departamentos\Departamento;
use App\Models\Empresas\Empresa;
use App\Models\Estados\Estado;
use App\Models\Marcas\marca;
use App\Models\Paises\Pais;
use App\Models\Provedores\provedor;
use App\Models\Seguridad\Rol;
use App\Models\TiposDocumento\tipoDocumento;
use App\Models\UnidadMedida\UnidadMedida;
use App\Models\Impuesto\impuesto;
use App\Models\InventarioCodigoBarras\InventarioCodigoBarra;
use App\Models\Producto\producto;
use App\Models\Sucursales\Sucursal;
use App\Models\Ventas\Venta;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'empresa_id',
        'nombre',
        'apellido',
        'email',
        'password',
        'telefono',
        'ultimo_acceso',
        'rol_id',
        'estado_id',
        'requiere_cambio_password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function categoriasCreadas()
    {
        return $this->hasMany(categoria::class, 'creado_por');
    }

    public function categoriasActualizadas()
    {
        return $this->hasMany(categoria::class, 'actualizado_por');
    }

    public function marcasCreadas()
    {
        return $this->hasMany(marca::class, 'creado_por');
    }

    public function marcasActualizadas()
    {
        return $this->hasMany(marca::class, 'actualizado_por');
    }

    public function unidadesMedidaCreadas()
    {
        return $this->hasMany(UnidadMedida::class, 'creado_por');
    }

    public function unidadesMedidaActualizadas()
    {
        return $this->hasMany(UnidadMedida::class, 'actualizado_por');
    }

    public function paisesCreados()
    {
        return $this->hasMany(Pais::class, 'creado_por');
    }

    public function departamentosCreados()
    {
        return $this->hasMany(Departamento::class, 'creado_por');
    }

    public function ciudadesCreadas()
    {
        return $this->hasMany(ciudad::class, 'creado_por');
    }

    public function tiposDocumentoCreados()
    {
        return $this->hasMany(tipoDocumento::class, 'creado_por');
    }

    public function tiposDocumentoActualizados()
    {
        return $this->hasMany(tipoDocumento::class, 'actualizado_por');
    }

    public function proveedoresCreados()
    {
        return $this->hasMany(provedor::class, 'creado_por');
    }

    public function proveedoresActualizados()
    {
        return $this->hasMany(provedor::class, 'actualizado_por');
    }

    public function impuestosCreados()
    {
        return $this->hasMany(impuesto::class, 'creado_por');
    }

    public function impuestosActualizados()
    {
        return $this->hasMany(impuesto::class, 'actualizado_por');
    }

    public function productosCreados()
    {
        return $this->hasMany(producto::class, 'creado_por');
    }

    public function productosActualizados()
    {
        return $this->hasMany(producto::class, 'actualizado_por');
    }

    public function clientesCreados()
    {
        return $this->hasMany(Cliente::class, 'creado_por');
    }

    public function clientesActualizados()
    {
        return $this->hasMany(Cliente::class, 'actualizado_por');
    }

    public function sucursalesCreadas()
    {
        return $this->hasMany(Sucursal::class, 'created_by');
    }

    public function sucursalesActualizadas()
    {
        return $this->hasMany(Sucursal::class, 'updated_by');
    }

    public function cajasCreadas()
    {
        return $this->hasMany(Caja::class, 'created_by');
    }

    public function cajasActualizadas()
    {
        return $this->hasMany(Caja::class, 'updated_by');
    }

    public function controlCajasApertura()
    {
        return $this->hasMany(ControlCaja::class, 'usuario_apertura_id');
    }

    public function controlCajasCierre()
    {
        return $this->hasMany(ControlCaja::class, 'usuario_cierre_id');
    }

    public function controlCajasCreadas()
    {
        return $this->hasMany(ControlCaja::class, 'created_by');
    }

    public function controlCajasActualizadas()
    {
        return $this->hasMany(ControlCaja::class, 'updated_by');
    }

    public function inventarioCodigosBarrasCreados()
    {
        return $this->hasMany(InventarioCodigoBarra::class, 'created_by');
    }

    public function inventarioCodigosBarrasActualizados()
    {
        return $this->hasMany(InventarioCodigoBarra::class, 'updated_by');
    }

    public function ventasCreadas()
    {
        return $this->hasMany(Venta::class, 'usuario_id');
    }
}
