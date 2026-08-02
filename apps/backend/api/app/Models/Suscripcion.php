<?php

namespace App\Models;

use App\Models\Empresas\Empresa;
use App\Models\Estados\Estado;
use App\Models\Pagos\Pago;
use App\Models\Planes\Plan;
use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    protected $table = 'suscripciones';

    protected $fillable = [
        'empresa_id',
        'plan_id',
        'estado_id',
        'fecha_inicio',
        'fecha_final',
        'fecha_vencimiento',
        'usuarios_contratados',
        'valor_pagado',
        'renovacion',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
