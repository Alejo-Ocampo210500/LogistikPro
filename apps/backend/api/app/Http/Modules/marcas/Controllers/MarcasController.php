<?php

namespace App\Http\Modules\Marcas\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Marcas\Repositories\MarcasRepository;
use App\Http\Modules\Marcas\Services\MarcasService;

class MarcasController extends Controller
{
    public function __construct(
        protected MarcasRepository $MarcasRepository,
        protected MarcasService $MarcasService
    ) {}

    public function listarMarcas()
    {
        try {
            $listarMarcas = $this->MarcasRepository->listarMarcas();
            return response()->json($listarMarcas, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al listar las marcas',
            ], 400);
        }
    }

    public function crearMarca(crearMarcaRequest $request)
    {
        try {
            $crearMarca = $this->MarcasService->crearMarca($request->validated());
            return response()->json($crearMarca, 201);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al crear la marca',
            ], 400);
        }
    }
}
