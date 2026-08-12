<?php

namespace App\Http\Controllers\Productos\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Producto\producto as ProductoModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductosRepository
{
    public function obtenerProductosCliente(int $empresaId): Collection
    {
        return ProductoModel::query()
            ->where('empresa_id', $empresaId)
            ->orderByDesc('id')
            ->get();
    }

    public function crearProductoCliente(array $payload): ProductoModel
    {
        $imagenPath = $this->persistirImagen($payload['imagen'] ?? null);

        return ProductoModel::query()->create([
            'empresa_id' => $payload['empresa_id'],
            'codigo' => $payload['codigo'],
            'codigo_barras' => $payload['codigo_barras'] ?? null,
            'nombre' => $payload['nombre'],
            'descripcion' => $payload['descripcion'] ?? null,
            'imagen' => $imagenPath,
            'categoria_id' => $payload['categoria_id'],
            'marca_id' => $payload['marca_id'] ?? null,
            'unidad_medida_id' => $payload['unidad_medida_id'],
            'impuesto_id' => $payload['impuesto_id'] ?? null,
            'costo' => $payload['costo'] ?? 0,
            'precio_venta' => $payload['precio_venta'] ?? 0,
            'stock' => $payload['stock'] ?? 0,
            'stock_minimo' => $payload['stock_minimo'] ?? 0,
            'stock_maximo' => $payload['stock_maximo'] ?? null,
            'maneja_inventario' => $payload['maneja_inventario'] ?? true,
            'permite_descuento' => $payload['permite_descuento'] ?? true,
            'es_servicio' => $payload['es_servicio'] ?? false,
            'venta_libre' => $payload['venta_libre'] ?? true,
            'estado_id' => $payload['estado_id'],
            'creado_por' => $payload['creado_por'],
            'actualizado_por' => $payload['actualizado_por'] ?? null,
        ]);
    }

    public function actualizarProductoCliente(int $productoId, int $empresaId, array $payload): ?ProductoModel
    {
        $producto = ProductoModel::query()
            ->where('id', $productoId)
            ->where('empresa_id', $empresaId)
            ->first();

        if (!$producto) {
            return null;
        }

        $imagenPath = $producto->imagen;
        if (isset($payload['imagen']) && $payload['imagen'] instanceof UploadedFile) {
            $imagenPath = $this->persistirImagen($payload['imagen'], $producto->imagen);
        }

        $producto->update([
            'codigo' => $payload['codigo'],
            'codigo_barras' => $payload['codigo_barras'] ?? null,
            'nombre' => $payload['nombre'],
            'descripcion' => $payload['descripcion'] ?? null,
            'imagen' => $imagenPath,
            'categoria_id' => $payload['categoria_id'],
            'marca_id' => $payload['marca_id'] ?? null,
            'unidad_medida_id' => $payload['unidad_medida_id'],
            'impuesto_id' => $payload['impuesto_id'] ?? null,
            'costo' => $payload['costo'] ?? 0,
            'precio_venta' => $payload['precio_venta'] ?? 0,
            'stock' => $payload['stock'] ?? 0,
            'stock_minimo' => $payload['stock_minimo'] ?? 0,
            'stock_maximo' => $payload['stock_maximo'] ?? null,
            'maneja_inventario' => $payload['maneja_inventario'] ?? true,
            'permite_descuento' => $payload['permite_descuento'] ?? true,
            'es_servicio' => $payload['es_servicio'] ?? false,
            'venta_libre' => $payload['venta_libre'] ?? true,
            'estado_id' => $payload['estado_id'],
            'actualizado_por' => $payload['actualizado_por'] ?? null,
        ]);

        return $producto->fresh();
    }

    public function cambiarEstadoProductoCliente(int $productoId, int $empresaId): bool
    {
        $producto = ProductoModel::query()
            ->where('id', $productoId)
            ->where('empresa_id', $empresaId)
            ->first();

        if (!$producto) {
            return false;
        }

        $nuevoEstadoId = (int) $producto->estado_id === 1 ? 2 : 1;

        return $producto->update([
            'estado_id' => $nuevoEstadoId,
        ]);
    }

    protected function persistirImagen(?UploadedFile $imagen, ?string $rutaActual = null): ?string
    {
        if (!$imagen) {
            return $rutaActual;
        }

        if ($rutaActual && Storage::disk('public')->exists($rutaActual)) {
            Storage::disk('public')->delete($rutaActual);
        }

        return $imagen->store('productos', 'public');
    }
}
