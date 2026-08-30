<?php

namespace App\Http\Modules\Empresas\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Empresas\Repositories\EmpresaRepository;
use App\Http\Modules\Empresas\Request\CrearEmpresaRequest;
use App\Http\Modules\Empresas\Services\EmpresaService;

class EmpresasController extends Controller
{
    public function __construct(
        protected EmpresaService $EmpresaService,
        protected EmpresaRepository $EmpresaRepository
    ) {}


    /**
     * Funcion del controlador que recibe una peticion y llama al servicio para listar las
     *  empresas y retorna la informacion de las empresas
     *
     * @author Alejandro Ocampos
     */
    public function listarEmpresasPorId()
    {
        try {
            $listarEmpresasId = $this->EmpresaRepository->listarEmpresasPorId();
            return response()->json($listarEmpresasId, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al listar las empresas por ID',
            ], 400);
        }
    }

    /**
     * Funcion del controlador que recibe lapeticion para crear una
     * empresa recibiendo los datos de la empresa y validando los datos con el request y asi
     * llamando al servicio para crear la empresa
     *
     * @param CrearEmpresaRequest $request
     *
     * @author Alejandro Ocampos
     */
    public function crearEmpresa(CrearEmpresaRequest $request)
    {
        try {
            $crearEmpresa = $this->EmpresaService->crearEmpresa($request->validated());
            return response()->json($crearEmpresa, 201);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al crear la empresa',
                'error' => $th->getMessage(),
            ], 400);
        }
    }
}
