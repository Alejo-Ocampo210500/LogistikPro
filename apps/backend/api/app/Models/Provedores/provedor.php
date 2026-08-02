<?php

namespace App\Models\Provedores;

use App\Models\Ciudades\ciudad;
use App\Models\Departamentos\Departamento;
use App\Models\Empresas\Empresa;
use App\Models\Estados\Estado;
use App\Models\Paises\Pais;
use App\Models\TiposDocumento\tipoDocumento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class provedor extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'proveedores';

    protected $fillable = [
        'empresa_id',
        'tipo_documento_id',
        'numero_documento',
        'codigo_verificacion',
        'razon_social',
        'nombre_comercial',
        'direccion',
        'telefono',
        'celular',
        'email',
        'sitio_web',
        'pais_id',
        'departamento_id',
        'ciudad_id',
        'codigo_postal',
        'cupo_credito',
        'estado_id',
        'dias_credito',
        'observaciones',
        'creado_por',
        'actualizado_por',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(tipoDocumento::class, 'tipo_documento_id');
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
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
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }
}
