<?php

namespace App\Http\Controllers\Estados;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Estados\Services\EstadosService;
use Illuminate\Http\JsonResponse;

class EstadoController extends Controller
{
    public function __construct(protected EstadosService $estadosService) {}

    public function index(): JsonResponse
    {
        try {
            $estados = $this->estadosService->obtenerEstados();
            return response()->json($estados, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'mensaje' => 'Error al obtener estados: ' . $exception->getMessage(),
            ], 400);
        }
    }
}
