<?php

namespace App\Http\Controllers\Pagos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pagos\Request\confirmarPagoPlanesRequest;
use App\Http\Controllers\Pagos\Request\RegistrarPagoManualRequest;
use App\Http\Controllers\Pagos\Services\PagosService;
use Illuminate\Http\JsonResponse;

class PanelPagosEmpresaController extends Controller
{
    public function __construct(protected PagosService $PagosService) {}

    public function listarPagosEmpresa(): JsonResponse
    {
        try {
            $obtenerPagos = $this->PagosService->obtenerPagos();
            return response()->json($obtenerPagos, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener los pagos',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function listarPagosPlanEmpresa($empresa_id): JsonResponse
    {
        try {
            $suscripcion = $this->PagosService->listarPagosPlanEmpresa($empresa_id);
            return response()->json($suscripcion, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al listar los pagos de la empresa'
            ], 400);
        }
    }

    public function confirmarPago(confirmarPagoPlanesRequest $confirmarPagoPlanesRequest): JsonResponse
    {
        try {
            $confirmarPago = $this->PagosService->confirmarPagoPlanes($confirmarPagoPlanesRequest->validated());
            return response()->json($confirmarPago, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al confirmar el pago',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function registrarPagoManual(RegistrarPagoManualRequest $request): JsonResponse
    {
        try {
            $registrarPago = $this->PagosService->registrarPagoManual($request->validated());
            return response()->json($registrarPago, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al registrar el pago manual',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
