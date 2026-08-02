<?php

namespace App\Http\Controllers\UnidadesMedida;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UnidadesMedida\Repositories\UnidadesMedidasRepository;
use App\Http\Controllers\UnidadesMedida\Request\CrearUnidadMedidaRequest;

class PanelUnidadMedidaController extends Controller
{
    public function __construct(protected UnidadesMedidasRepository $UnidadesMedidasRepository) {}

    public function obtenerUnidadesMedidaCliente()
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $unidadesMedida = $this->UnidadesMedidasRepository->obtenerUnidadesMedidaCliente($empresaId);

            return response()->json([
                'mensaje' => 'Unidades de medida obtenidas correctamente.',
                'unidades_medida' => $unidadesMedida,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener las unidades de medida.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function crearUnidadMedidaCliente(CrearUnidadMedidaRequest $CrearUnidadMedidaRequest)
    {

        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $unidadMedida = $this->UnidadesMedidasRepository->crearUnidadMedidaCliente($empresaId, $CrearUnidadMedidaRequest->validated());

            return response()->json([
                'mensaje' => 'Unidad de medida creada correctamente.',
                'unidad_medida' => $unidadMedida,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al crear la unidad de medida.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function actualizarUnidadMedidaCliente(CrearUnidadMedidaRequest $CrearUnidadMedidaRequest, int $unidadId)
        {
            try {
                $empresaId = (int) (request()->user()?->empresa_id ?? 0);

                if ($empresaId <= 0) {
                    return response()->json([
                        'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                    ], 422);
                }

                $unidadMedida = $this->UnidadesMedidasRepository->actualizarUnidadMedidaCliente($empresaId, $unidadId, $CrearUnidadMedidaRequest->validated());

                return response()->json([
                    'mensaje' => 'Unidad de medida actualizada correctamente.',
                    'unidad_medida' => $unidadMedida,
                ], 200);
            } catch (\Throwable $th) {
                return response()->json([
                    'mensaje' => 'Error al actualizar la unidad de medida.',
                    'error' => $th->getMessage(),
                ], 400);
            }
        }
    public function cambiarEstadoUnidadMedidaCliente(int $unidadId)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $unidadMedida = $this->UnidadesMedidasRepository->cambiarEstadoUnidadMedidaCliente($empresaId, $unidadId, ['estado_id' => request()->input('estado_id')]);

            return response()->json([
                'mensaje' => 'Estado de la unidad de medida actualizado correctamente.',
                'unidad_medida' => $unidadMedida,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al actualizar el estado de la unidad de medida.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }
}
