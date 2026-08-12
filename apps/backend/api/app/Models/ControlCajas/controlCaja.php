<?php

namespace App\Models\ControlCajas;

use App\Models\Caja\Caja;
use App\Models\Empresas\Empresa;
use App\Models\Sucursales\Sucursal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ControlCaja extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'control_cajas';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'caja_id',
        'usuario_apertura_id',
        'usuario_cierre_id',
        'fecha_apertura',
        'fecha_cierre',
        'hora_apertura',
        'hora_cierre',
        'monto_apertura',
        'monto_cierre',
        'efectivo_sistema',
        'efectivo_contado',
        'diferencia',
        'observaciones_apertura',
        'observaciones_cierre',
        'estado',
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

    public function usuarioApertura()
    {
        return $this->belongsTo(User::class, 'usuario_apertura_id');
    }

    public function usuarioCierre()
    {
        return $this->belongsTo(User::class, 'usuario_cierre_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
