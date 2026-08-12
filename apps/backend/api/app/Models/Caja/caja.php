<?php

namespace App\Models\Caja;

use App\Models\Empresas\Empresa;
use App\Models\Estados\Estado;
use App\Models\Sucursales\Sucursal;
use App\Models\User;
use App\Models\Ventas\Venta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Caja extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'cajas';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'codigo',
        'nombre',
        'descripcion',
        'impresora',
        'estado_id',
        'created_by',
        'updated_by',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'caja_id');
    }
}
