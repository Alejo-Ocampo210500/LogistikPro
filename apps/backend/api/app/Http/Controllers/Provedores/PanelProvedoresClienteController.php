<?php

namespace App\Http\Controllers\Provedores;

use App\Http\Controllers\Provedores\Repositories\ProvedoresRepository;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Provedores\Request\CrearProvedorClienteRequest;
use App\Models\Estados\Estado;

class PanelProvedoresClienteController extends Controller
{
    public function __construct(protected ProvedoresRepository $ProvedoresRepository) {}

    public function obtenerProvedoresCliente()
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $provedores = $this->ProvedoresRepository->obtenerProvedoresCliente($empresaId);

            return response()->json([
                'mensaje' => 'Provedores obtenidos correctamente.',
                'provedores' => $provedores,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener los provedores.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function crearProvedorCliente(CrearProvedorClienteRequest $CrearProvedorClienteRequest)
    {
        try {
            $payload = $CrearProvedorClienteRequest->validated();
            $payload['empresa_id'] = (int) ($CrearProvedorClienteRequest->user()?->empresa_id ?? 0);

            if ($payload['empresa_id'] <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $estadoId = isset($payload['estado_id']) ? (int) $payload['estado_id'] : 0;
            $estadoValido = $estadoId > 0
                ? Estado::query()->where('id', $estadoId)->first()
                : null;

            if ($estadoId > 0 && !$estadoValido) {
                return response()->json([
                    'mensaje' => 'El estado seleccionado no existe.',
                ], 422);
            }

            if ($estadoValido) {
                $nombreEstado = strtolower(trim((string) $estadoValido->nombre));

                if (!in_array($nombreEstado, ['activo', 'inactivo'], true)) {
                    return response()->json([
                        'mensaje' => 'Solo se permiten estados Activo o Inactivo para proveedores.',
                    ], 422);
                }
            }

            if (!$estadoValido) {
                $estadoValido = Estado::query()
                    ->where('nombre', 'Activo')
                    ->orWhere('nombre', 'ACTIVO')
                    ->orWhere('nombre', 'activo')
                    ->first();
            }

            if (!$estadoValido) {
                return response()->json([
                    'mensaje' => 'No existe un estado Activo configurado para crear el proveedor.',
                ], 422);
            }

            $payload['estado_id'] = (int) $estadoValido->id;

            $provedorCreado = $this->ProvedoresRepository->crearProvedorCliente($payload);

            return response()->json([
                'mensaje' => 'Proveedor creado correctamente.',
                'provedor' => $provedorCreado,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al crear el proveedor.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function actualizarProvedorCliente(int $provedorId, CrearProvedorClienteRequest $CrearProvedorClienteRequest)
    {
        try {
            $payload = $CrearProvedorClienteRequest->validated();
            $payload['empresa_id'] = (int) ($CrearProvedorClienteRequest->user()?->empresa_id ?? 0);
            $payload['id'] = $provedorId;

            if ($payload['empresa_id'] <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            if ($payload['id'] <= 0) {
                return response()->json([
                    'mensaje' => 'El proveedor a actualizar no es valido.',
                ], 422);
            }

            $estadoId = isset($payload['estado_id']) ? (int) $payload['estado_id'] : 0;
            $estadoValido = $estadoId > 0
                ? Estado::query()->where('id', $estadoId)->first()
                : null;

            if ($estadoId > 0 && !$estadoValido) {
                return response()->json([
                    'mensaje' => 'El estado seleccionado no existe.',
                ], 422);
            }

            if ($estadoValido) {
                $nombreEstado = strtolower(trim((string) $estadoValido->nombre));

                if (!in_array($nombreEstado, ['activo', 'inactivo'], true)) {
                    return response()->json([
                        'mensaje' => 'Solo se permiten estados Activo o Inactivo para proveedores.',
                    ], 422);
                }
            }

            if (!$estadoValido) {
                $estadoValido = Estado::query()
                    ->where('nombre', 'Activo')
                    ->orWhere('nombre', 'ACTIVO')
                    ->orWhere('nombre', 'activo')
                    ->first();
            }

            if (!$estadoValido) {
                return response()->json([
                    'mensaje' => 'No existe un estado Activo configurado para Actualizar el proveedor.',
                ], 422);
            }

            $payload['estado_id'] = (int) $estadoValido->id;

            $provedorActualizado = $this->ProvedoresRepository->actualizarProvedorCliente($payload);

            return response()->json([
                'mensaje' => 'Proveedor actualizado correctamente.',
                'provedor' => $provedorActualizado,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al actualizar el proveedor.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function cambiarEstadoProvedorCliente(int $provedorId)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            if ($provedorId <= 0) {
                return response()->json([
                    'mensaje' => 'El proveedor a cambiar de estado no es valido.',
                ], 422);
            }

            $estadoId = isset(request()->estado_id) ? (int) request()->estado_id : 0;
            $provedorActualizado = $this->ProvedoresRepository->cambiarEstadoProvedorCliente($provedorId, $estadoId, $empresaId);

            return response()->json([
                'mensaje' => 'Proveedor actualizado correctamente.',
                'provedor' => $provedorActualizado,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al actualizar el proveedor.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }
}
