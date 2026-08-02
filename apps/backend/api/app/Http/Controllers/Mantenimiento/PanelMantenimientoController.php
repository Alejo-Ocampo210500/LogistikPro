<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mantenimiento\Request\ActualizarUsuarioGlobalRequest;
use App\Http\Controllers\Mantenimiento\Services\PanelMantenimientoService;
use Illuminate\Http\Request;

class PanelMantenimientoController extends Controller
{
    public function __construct(protected PanelMantenimientoService $PanelMantenimientoService) {}

    /**
     * Funcion del controlador que recibe unos datos del frontend 
     * para enviarlos al servcio y actualizar los datos del usuario
     * del modulo de mantenimiento - Actualizar usuario global
     *
     * @param ActualizarUsuarioGlobalRequest $request
     * @author Alejandro Ocampo
     */
    public function actualizarUsuarioGlobal(ActualizarUsuarioGlobalRequest $request, $usuario)
    {
        try {
            $actualizarUsuarios = $this->PanelMantenimientoService->actualizarUsuarioGlobal($usuario, $request->validated());
            return response()->json($actualizarUsuarios, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al actualizar el usuario',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Funcion del controlador que recibe unos datos del frontend 
     * para enviarlos al servcio y obtener los estados
     * del modulo de mantenimiento - Obtener estados
     *
     * @param Request $request
     * @author Alejandro Ocampo
     */
    public function obtenerEstados(Request $request)
    {
        try {
            $obtenerEstados = $this->PanelMantenimientoService->obtenerEstados($request);
            return response()->json($obtenerEstados, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener los estados',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
