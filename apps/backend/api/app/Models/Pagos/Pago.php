<?php

namespace App\Models\Pagos;

use App\Models\Suscripcion;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';
    protected $fillable = [
        'suscripcion_id',
        'plan_id',
        'metodo_pago_id',
        'estado_pago_id',
        'valor',
        'tipo_pago',
        'fecha_pago',
        'referencia',
        'comprobante',
        'observaciones',
    ];

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }

    public function estadoPago()
    {
        return $this->belongsTo(EstadoPago::class);
    }

    public function suscripcion()
    {
        return $this->belongsTo(Suscripcion::class);
    }
}
