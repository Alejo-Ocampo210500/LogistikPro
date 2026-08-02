<?php

namespace App\Http\Controllers\Marcas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Marcas\Repositories\MarcasClientesRepository;
use App\Http\Controllers\Marcas\Request\CrearMarcasClienteRequest;

class PanelMarcasClienteController extends Controller
{
    public function __construct(protected MarcasClientesRepository $marcasClientesRepository) {}

    /**
     * Funcion del controlador que recibe la solicitud para obtener las marcas del cliente autenticado
     * y devuelve una respuesta JSON con las marcas obtenidas o un mensaje de error en caso de que ocurra algún problema.
     *
     * @author Alejandro Ocampo
     */
    public function obtenerMarcasCliente()
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $marcas = $this->marcasClientesRepository->obtenerMarcasCliente($empresaId);

            return response()->json([
                'mensaje' => 'Marcas obtenidas correctamente.',
                'marcas' => $marcas,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener las marcas.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

        /**
         * Funcion del controlador que recibe la solicitud para crear marcas del cliente autenticado y
         * devuelve una respuesta JSON con las marcas creadas o un mensaje de error en caso de que ocurra algún problema.
         *
         * @param CrearMarcasClienteRequest $CrearMarcasClienteRequest
         *
         * @author Alejandro Ocampo
         */
        public function crearMarcasCliente(CrearMarcasClienteRequest $CrearMarcasClienteRequest)
        {
                try {
                    $empresaId = (int) (request()->user()?->empresa_id ?? 0);

                    if ($empresaId <= 0) {
                        return response()->json([
                            'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                        ], 422);
                    }

                    $marcas = $this->marcasClientesRepository->crearMarcasCliente($empresaId, $CrearMarcasClienteRequest->validated());

                    return response()->json([
                        'mensaje' => 'Marcas creadas correctamente.',
                        'marcas' => $marcas,
                    ], 200);
                } catch (\Throwable $th) {
                    return response()->json([
                        'mensaje' => 'Error al crear las marcas.',
                        'error' => $th->getMessage(),
                    ], 400);
                }
        }

        /**
         * Funcion del controlador que recibe la solicitud para actualizar una marca del cliente autenticado
         * y devuelve una respuesta JSON con la marca actualizada o un mensaje de error en caso de que ocurra algún problema.
         *
         * @param int $marcasId
         * @param CrearMarcasClienteRequest $request
         *
         * @author Alejandro Ocampo
         */
        public function actualizarMarcaCliente(int $marcasId, CrearMarcasClienteRequest $request)
        {
            try {
                $empresaId = (int) ($request->user()?->empresa_id ?? 0);

                if ($empresaId <= 0) {
                    return response()->json([
                        'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                    ], 422);
                }

                $marcaActualizada = $this->marcasClientesRepository->actualizarMarcaCliente($empresaId, $marcasId, $request->validated());

                if (!$marcaActualizada) {
                    return response()->json([
                        'mensaje' => 'No se pudo actualizar la marca. Verifique que exista y pertenezca a la empresa.',
                    ], 404);
                }

                return response()->json([
                    'mensaje' => 'Marca actualizada correctamente.',
                    'marca' => $marcaActualizada,
                ], 200);
            } catch (\Throwable $th) {
                return response()->json([
                    'mensaje' => 'Error al actualizar la marca.',
                    'error' => $th->getMessage(),
                ], 400);
            }
        }

        public function cambiarEstadoMarcaCliente(int $marcasId)
        {
            try {
                $empresaId = (int) (request()->user()?->empresa_id ?? 0);

                if ($empresaId <= 0) {
                    return response()->json([
                        'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                    ], 422);
                }

                $marca = $this->marcasClientesRepository->obtenerMarcaClientePorId($marcasId, $empresaId);

                if (!$marca) {
                    return response()->json([
                        'mensaje' => 'Marca no encontrada para la empresa autenticada.',
                    ], 404);
                }

                $nuevoEstadoId = $marca->estado_id === 1 ? 2 : 1;
                $marcaActualizada = $this->marcasClientesRepository->cambiarEstadoMarcaCliente($empresaId, $marcasId, $nuevoEstadoId);

                return response()->json([
                    'mensaje' => 'Estado de la marca actualizado correctamente.',
                    'marca' => $marcaActualizada,
                ], 200);
            } catch (\Throwable $th) {
                return response()->json([
                    'mensaje' => 'Error al cambiar el estado de la marca.',
                    'error' => $th->getMessage(),
                ], 400);
            }
        }
}
