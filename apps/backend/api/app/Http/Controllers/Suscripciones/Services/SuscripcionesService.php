<?php

namespace App\Http\Controllers\Suscripciones\Services;

use App\Models\Suscripcion;
use Illuminate\Support\Facades\DB;

class SuscripcionesService
{
    public function obtenerSuscripciones()
    {
        return DB::table('suscripciones')
            ->join('empresas', 'suscripciones.empresa_id', '=', 'empresas.id')
            ->join('planes', 'suscripciones.plan_id', '=', 'planes.id')
            ->join('estados', 'suscripciones.estado_id', '=', 'estados.id')
            ->select(
                'suscripciones.*',
                'empresas.nombre_comercial as empresa_nombre',
                'planes.nombre as plan_nombre',
                'estados.nombre as estado_nombre'
            )
            ->orderBy('id', 'desc')
            ->get();
    }
}
