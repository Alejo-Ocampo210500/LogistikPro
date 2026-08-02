<?php

namespace App\Http\Controllers\Departamentos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Departamentos\Repositories\DepartamentosRepository;

class PanelDepartamentosClienteController extends Controller
{
    public function __construct(protected DepartamentosRepository $DepartamentosRepository) {}

    public function obtenerDepartamentosCliente()
    {
        try {
            $listarDepartamentos = $this->DepartamentosRepository->obtenerDepartamentosCliente();
            return response()->json([$listarDepartamentos], 200);
        } catch (\Throwable $th) {
            return response([
                'mensaje' => 'Error al obtener los departamentos  '
            ], 400);
        }
    }
}
