<?php

namespace App\Http\Controllers\Cajas\Repositories;

use App\Models\Caja\Caja;
use Illuminate\Support\Facades\Auth;

class CajasRepository
{
    public function obtenerCajasCliente(int $empresaId)
    {
        return Caja::query()
            ->where('empresa_id', $empresaId)
            ->orderByDesc('id')
            ->get();
    }

    public function crearCajaCliente(array $payload): Caja
    {
        return Caja::create([
            'empresa_id' => $payload['empresa_id'],
            'sucursal_id' => $payload['sucursal_id'],
            'codigo' => $payload['codigo'] ?? null,
            'nombre' => $payload['nombre'],
            'descripcion' => $payload['descripcion'],
            'impresora' => $payload['impresora'] ?? null,
            'estado_id' => $payload['estado_id'] ?? 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    public function actualizarCajaCliente(array $payload): Caja
    {
        $caja = Caja::query()
            ->where('empresa_id', $payload['empresa_id'])
            ->where('id', $payload['id'])
            ->firstOrFail();

        $caja->update([
            'sucursal_id' => $payload['sucursal_id'],
            'codigo' => $payload['codigo'] ?? null,
            'nombre' => $payload['nombre'],
            'descripcion' => $payload['descripcion'],
            'impresora' => $payload['impresora'] ?? null,
            'estado_id' => $payload['estado_id'] ?? $caja->estado_id,
            'updated_by' => Auth::id(),
        ]);

        return $caja;
    }

    public function cambiarEstadoCajaCliente(int $cajaId, int $estadoId, int $empresaId): Caja
    {
        $caja = Caja::query()
            ->where('empresa_id', $empresaId)
            ->where('id', $cajaId)
            ->firstOrFail();

        $caja->estado_id = $estadoId;
        $caja->updated_by = Auth::id();
        $caja->save();

        return $caja;
    }
}
