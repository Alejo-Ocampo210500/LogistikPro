<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Categorias\categoria;
use App\Models\Ciudades\ciudad;
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
use App\Models\Producto\producto;
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
}
