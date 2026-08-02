<?php

namespace App\Models\UnidadMedida;

use App\Models\Empresas\Empresa;
use App\Models\Estados\Estado;
use App\Models\Producto\producto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'unidades_medida';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'abreviatura',
        'descripcion',
        'estado_id',
        'creado_por',
        'actualizado_por',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
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

    public function productos()
    {
        return $this->hasMany(producto::class, 'unidad_medida_id');
    }
}
