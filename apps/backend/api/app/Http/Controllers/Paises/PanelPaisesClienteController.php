<?php

namespace App\Http\Controllers\Paises;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Paises\Repositories\PaisesRepository;

class PanelPaisesClienteController extends Controller
{
    public function __construct(protected PaisesRepository $PaisesRepository) {}

    public function obtenerPaisesCliente()
    {
        try {
            $listarPaises = $this->PaisesRepository->obtenerPaisesCliente();
            return response()->json([$listarPaises], 200);
        } catch (\Throwable $th) {
            return response([
                'mensaje' => 'Error al obtener los paises ',
            ], 400);
        }
    }
}
