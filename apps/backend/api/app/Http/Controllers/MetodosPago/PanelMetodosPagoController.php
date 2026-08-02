<?php

namespace App\Http\Controllers\MetodosPago;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MetodosPago\Repositories\MetodosPagoRepository;
use Illuminate\Http\JsonResponse;

class PanelMetodosPagoController extends Controller
{
    public function __construct(protected MetodosPagoRepository $MetodosPagoRepository) {}

    public function listarMetodosPago()
    {
        try {
            $listarMetodosPago = $this->MetodosPagoRepository->listarMetodosPago();
            return response()->json($listarMetodosPago, 200);
        } catch (\Throwable $th) {
            return response([
                'mensaje' => 'Error al listar los metodos de pago',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
