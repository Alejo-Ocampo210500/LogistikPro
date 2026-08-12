<?php

namespace App\Http\Controllers\Cajas;
use App\Http\Controllers\Cajas\Request\CrearCajaRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Cajas\Repositories\CajasRepository;

class PanelCajasClienteController extends Controller
{
    public function __construct(protected CajasRepository $CajasRepository) {}

    public function obtenerCajasCliente()
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $cajas = $this->CajasRepository->obtenerCajasCliente($empresaId);

            return response()->json([
                'mensaje' => 'Cajas obtenidas correctamente.',
                'cajas' => $cajas,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener las cajas.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function crearCajaCliente(CrearCajaRequest $crearCajaRequest)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $payload = $crearCajaRequest->validated();
            $payload['empresa_id'] = $empresaId;

            $caja = $this->CajasRepository->crearCajaCliente($payload);

            if (!$caja) {
                return response()->json([
                    'mensaje' => 'No se pudo crear la caja.',
                ], 422);
            }

            return response()->json([
                'mensaje' => 'Caja creada correctamente.',
                'caja' => $caja,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al crear la caja.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function actualizarCajaCliente(int $cajaId, CrearCajaRequest $crearCajaRequest)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            if ($cajaId <= 0) {
                return response()->json([
                    'mensaje' => 'La caja a actualizar no es valida.',
                ], 422);
            }

            $payload = $crearCajaRequest->validated();
            $payload['empresa_id'] = $empresaId;
            $payload['id'] = $cajaId;

            $caja = $this->CajasRepository->actualizarCajaCliente($payload);

            return response()->json([
                'mensaje' => 'Caja actualizada correctamente.',
                'caja' => $caja,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al actualizar la caja.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function cambiarEstadoCajaCliente(int $cajaId)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            if ($cajaId <= 0) {
                return response()->json([
                    'mensaje' => 'La caja a cambiar de estado no es valida.',
                ], 422);
            }

            $estadoId = (int) (request()->estado_id ?? 0);

            if ($estadoId <= 0) {
                return response()->json([
                    'mensaje' => 'Debes enviar un estado valido para la caja.',
                ], 422);
            }

            $caja = $this->CajasRepository->cambiarEstadoCajaCliente($cajaId, $estadoId, $empresaId);

            return response()->json([
                'mensaje' => 'Estado de caja actualizado correctamente.',
                'caja' => $caja,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al cambiar el estado de la caja.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }
}
