<?php

namespace App\Http\Controllers\Planes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Planes\Request\CrearPlanRequest;
use App\Http\Controllers\Planes\Request\ActualizarPlanRequest;
use App\Http\Controllers\Planes\Services\PlanesService;
use App\Models\Planes\Plan;

class PlanesController extends Controller
{
    public function __construct(protected PlanesService $planesService) {}

    public function crearPlan(CrearPlanRequest $request)
    {
        try {
            $planCreado = $this->planesService->crearPlan($request->validated());
            return response()->json($planCreado, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'mensaje' => 'Error al crear el plan: ' . $exception->getMessage(),
            ], 400);
        }
    }

    public function actualizarPlan(ActualizarPlanRequest $request, Plan $plan)
    {
        try {
            $planActualizado = $this->planesService->actualizarPlan($plan, $request->validated());
            return response()->json($planActualizado, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'mensaje' => 'Error al actualizar el plan: ' . $exception->getMessage(),
            ], 400);
        }
    }

    public function listarPlanes()
    {
        try {
            $listarPlanes = $this->planesService->listarPlanes();
            return response()->json($listarPlanes, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener los planes',
            ], 400);
        }
    }
}
