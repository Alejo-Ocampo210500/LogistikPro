<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Clientes\Repositories\ClientesRepository;
use App\Http\Controllers\Clientes\Request\CrearClienteRequest;
use Illuminate\Http\Request;

class PanelClientesClienteController extends Controller
{
    public function __construct(protected ClientesRepository $ClientesRepository) {}

    public function obtenerClientesCliente()
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $clientes = $this->ClientesRepository->obtenerClientesCliente($empresaId);

            return response()->json([
                'mensaje' => 'Clientes obtenidos correctamente.',
                'clientes' => $clientes,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener los clientes.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function crearClienteCliente(CrearClienteRequest $crearClienteRequest)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $payload = $crearClienteRequest->validated();
            $payload['empresa_id'] = $empresaId;
            $payload['creado_por'] = (int) ($crearClienteRequest->user()?->id ?? 0);
            $payload['actualizado_por'] = (int) ($crearClienteRequest->user()?->id ?? 0);

            $cliente = $this->ClientesRepository->crearClienteCliente($payload);

            if (!$cliente) {
                return response()->json([
                    'mensaje' => 'No se pudo crear el cliente.',
                ], 422);
            }

            return response()->json([
                'mensaje' => 'Cliente creado correctamente.',
                'cliente' => $cliente,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al crear el cliente.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function actualizarClienteCliente(CrearClienteRequest $crearClienteRequest, int $clienteId)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $payload = $crearClienteRequest->validated();
            $payload['empresa_id'] = $empresaId;
            $payload['actualizado_por'] = (int) ($crearClienteRequest->user()?->id ?? 0);

            $cliente = $this->ClientesRepository->actualizarClienteCliente($clienteId, $empresaId, $payload);

            if (!$cliente) {
                return response()->json([
                    'mensaje' => 'No se encontro el cliente solicitado para actualizar.',
                ], 404);
            }

            return response()->json([
                'mensaje' => 'Cliente actualizado correctamente.',
                'cliente' => $cliente,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al actualizar el cliente.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function cambiarEstadoClienteCliente(Request $request, int $clienteId)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $validated = $request->validate([
                'estado_id' => ['required', 'integer', 'exists:estados,id'],
            ]);

            $cliente = $this->ClientesRepository->cambiarEstadoClienteCliente(
                $clienteId,
                $empresaId,
                (int) $validated['estado_id'],
                (int) ($request->user()?->id ?? 0)
            );

            if (!$cliente) {
                return response()->json([
                    'mensaje' => 'No se encontro el cliente solicitado para cambiar estado.',
                ], 404);
            }

            return response()->json([
                'mensaje' => 'Estado del cliente actualizado correctamente.',
                'cliente' => $cliente,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al cambiar estado del cliente.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }
}
