<?php

namespace App\Models\Ventas;

use App\Models\Caja\Caja;
use App\Models\Empresas\Empresa;
use App\Models\Pagos\MetodoPago;
use App\Models\Sucursales\Sucursal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Venta extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'ventas';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'caja_id',
        'usuario_id',
        'cliente_id',
        'metodo_pago_id',
        'numero_venta',
        'fecha_venta',
        'subtotal',
        'descuento',
        'impuesto',
        'total',
        'estado',
        'observaciones',
        'codigo_barra',
        'created_by',
        'updated_by'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }
}
