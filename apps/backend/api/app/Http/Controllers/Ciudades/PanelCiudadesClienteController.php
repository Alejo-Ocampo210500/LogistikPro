<?php

namespace App\Http\Controllers\Ciudades;

use App\Http\Controllers\Ciudades\Repositories\CiudadesRepository;
use App\Http\Controllers\Controller;

class PanelCiudadesClienteController extends Controller
{
    public function __construct(protected CiudadesRepository $CiudadesRepository) {}

    public function obtenerCiudadesCliente()
    {
        try {
            $listarCiudades = $this->CiudadesRepository->obtenerCiudadesCliente();
            return response()->json([$listarCiudades], 200);
        } catch (\Throwable $th) {
            return response([
                'mensaje' => 'Error al obtener las ciudades',
            ], 400);
        }
    }
}
