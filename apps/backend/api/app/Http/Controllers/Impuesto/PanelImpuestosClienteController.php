<?php

namespace App\Http\Controllers\Impuesto;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Impuesto\Repositories\ImpuestosRepository;

class PanelImpuestosClienteController extends Controller
{
    public function __construct(protected ImpuestosRepository $ImpuestosRepository) {}

    public function obtenerImpuestosCliente()
    {
        try {
            $listarImpuestos = $this->ImpuestosRepository->obtenerImpuestosCliente();
            return response()->json($listarImpuestos, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener los impuestos del cliente',
            ], 400);
        }
    }

}
