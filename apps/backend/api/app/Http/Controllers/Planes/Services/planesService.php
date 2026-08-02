<?php

namespace App\Http\Controllers\Planes\Services;

use App\Models\Planes\Plan;

class PlanesService
{
    public function crearPlan(array $data)
    {
        $plan = new Plan();
        $plan->nombre = $data['nombre'];
        $plan->descripcion = $data['descripcion'] ?? null;
        $plan->precio = $data['precio'];
        $plan->duracion_meses = $data['duracion_meses'];
        $plan->descuento = $data['descuento'] ?? 0.00;
        $plan->estado_id = $data['estado_id'];
        $plan->save();

        return $plan;
    }

    public function actualizarPlan(Plan $plan, array $data)
    {
        $plan->nombre = $data['nombre'];
        $plan->descripcion = $data['descripcion'] ?? null;
        $plan->precio = $data['precio'];
        $plan->duracion_meses = $data['duracion_meses'];
        $plan->descuento = $data['descuento'] ?? 0.00;
        $plan->estado_id = $data['estado_id'];
        $plan->save();

        return $plan;
    }

    public function listarPlanes()
    {
        $planes = Plan::all();
        return $planes;
    }
}
