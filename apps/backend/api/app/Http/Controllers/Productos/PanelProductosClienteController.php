<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Productos\Request\CrearProductoRequest;
use App\Http\Controllers\Productos\Repositories\ProductosRepository;

class PanelProductosClienteController extends Controller
{
    public function __construct(protected ProductosRepository $ProductosRepository) {}

    public function obtenerProductosCliente()
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $productos = $this->ProductosRepository->obtenerProductosCliente($empresaId);

            return response()->json([
                'mensaje' => 'Productos obtenidos correctamente.',
                'productos' => $productos,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener los productos.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function crearProductoCliente(CrearProductoRequest $crearProductoRequest)
    {
        try {
            $empresaId = (int) ($crearProductoRequest->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $payload = $crearProductoRequest->validated();
            $payload['empresa_id'] = $empresaId;
            $payload['creado_por'] = (int) ($crearProductoRequest->user()?->id ?? 0);
            $payload['actualizado_por'] = (int) ($crearProductoRequest->user()?->id ?? 0);

            $producto = $this->ProductosRepository->crearProductoCliente($payload);

            if (!$producto) {
                return response()->json([
                    'mensaje' => 'No se pudo crear el producto.',
                ], 422);
            }

            return response()->json([
                'mensaje' => 'Producto creado correctamente.',
                'producto' => $producto,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al crear el producto.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function actualizarProductoCliente(CrearProductoRequest $actualizarProductoRequest, int $productoId)
    {
        try {
            $empresaId = (int) ($actualizarProductoRequest->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $payload = $actualizarProductoRequest->validated();
            $payload['empresa_id'] = $empresaId;
            $payload['actualizado_por'] = (int) ($actualizarProductoRequest->user()?->id ?? 0);

            $producto = $this->ProductosRepository->actualizarProductoCliente($productoId, $payload);

            if (!$producto) {
                return response()->json([
                    'mensaje' => 'No se pudo actualizar el producto.',
                ], 422);
            }

            return response()->json([
                'mensaje' => 'Producto actualizado correctamente.',
                'producto' => $producto,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al actualizar el producto.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function cambiarEstadoProductoCliente(int $productoId)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $producto = $this->ProductosRepository->cambiarEstadoProductoCliente($productoId, $empresaId);

            if (!$producto) {
                return response()->json([
                    'mensaje' => 'No se pudo cambiar el estado del producto.',
                ], 422);
            }

            return response()->json([
                'mensaje' => 'Estado del producto cambiado correctamente.',
                'producto' => $producto,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al cambiar el estado del producto.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }
}
