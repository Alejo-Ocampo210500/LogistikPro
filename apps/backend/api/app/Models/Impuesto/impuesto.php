<?php

namespace App\Models\Impuesto;

use App\Models\Empresas\Empresa;
use App\Models\Estados\Estado;
use App\Models\Producto\producto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\User;

class impuesto extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'impuestos';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'porcentaje',
        'codigo',
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
        return $this->hasMany(producto::class, 'impuesto_id');
    }
}
