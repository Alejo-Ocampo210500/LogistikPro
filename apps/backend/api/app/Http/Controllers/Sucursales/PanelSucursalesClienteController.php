<?php

namespace App\Http\Controllers\Sucursales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Sucursales\Repositories\SucursalesRepository;
use App\Http\Controllers\Sucursales\Request\CrearSucursalRequest;

class PanelSucursalesClienteController extends Controller
{
    public function __construct(protected SucursalesRepository $SucursalesRepository) {}

    public function obtenerSucursalesCliente()
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $sucursales = $this->SucursalesRepository->obtenerSucursalesCliente($empresaId);

            return response()->json([
                'mensaje' => 'Sucursales obtenidas correctamente.',
                'sucursales' => $sucursales,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener las sucursales.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function crearSucursalCliente(CrearSucursalRequest $crearSucursalRequest)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $payload = $crearSucursalRequest->validated();
            $payload['empresa_id'] = $empresaId;

            $sucursal = $this->SucursalesRepository->crearSucursalCliente($payload);

            if (!$sucursal) {
                return response()->json([
                    'mensaje' => 'No se pudo crear la sucursal.',
                ], 422);
            }

            return response()->json([
                'mensaje' => 'Sucursal creada correctamente.',
                'sucursal' => $sucursal,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al crear la sucursal.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function actualizarSucursalCliente(int $sucursalId, CrearSucursalRequest $crearSucursalRequest)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            if ($sucursalId <= 0) {
                return response()->json([
                    'mensaje' => 'La sucursal a actualizar no es valida.',
                ], 422);
            }

            $payload = $crearSucursalRequest->validated();
            $payload['empresa_id'] = $empresaId;
            $payload['id'] = $sucursalId;

            $sucursal = $this->SucursalesRepository->actualizarSucursalCliente($payload);

            return response()->json([
                'mensaje' => 'Sucursal actualizada correctamente.',
                'sucursal' => $sucursal,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al actualizar la sucursal.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function cambiarEstadoSucursalCliente(int $sucursalId)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            if ($sucursalId <= 0) {
                return response()->json([
                    'mensaje' => 'La sucursal a cambiar de estado no es valida.',
                ], 422);
            }

            $estadoRequest = strtolower(trim((string) request()->estado));
            $estadoId = (int) (request()->estado_id ?? 0);

            if ($estadoRequest === '') {
                if ($estadoId === 1) {
                    $estadoRequest = 'activo';
                } elseif ($estadoId === 2) {
                    $estadoRequest = 'inactivo';
                }
            }

            if (!in_array($estadoRequest, ['activo', 'inactivo'], true)) {
                return response()->json([
                    'mensaje' => 'El estado debe ser Activo o Inactivo.',
                ], 422);
            }

            $sucursal = $this->SucursalesRepository->cambiarEstadoSucursalCliente($sucursalId, $estadoRequest, $empresaId);

            return response()->json([
                'mensaje' => 'Estado de sucursal actualizado correctamente.',
                'sucursal' => $sucursal,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al cambiar el estado de la sucursal.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }
}
