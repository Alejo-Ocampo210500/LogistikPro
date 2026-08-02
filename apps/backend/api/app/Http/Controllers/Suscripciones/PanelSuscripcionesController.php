<?php

namespace App\Http\Controllers\Suscripciones;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Suscripciones\Services\SuscripcionesService;
use Illuminate\Http\JsonResponse;

class PanelSuscripcionesController extends Controller
{
    public function __construct(protected SuscripcionesService $SuscripcionesService) {}

    public function obtenerSuscripciones(): JsonResponse
    {
        try {
            $obtenerSucripciones = $this->SuscripcionesService->obtenerSuscripciones();
            return response()->json($obtenerSucripciones, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obetner las suscripciones',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
