<?php

namespace App\Http\Controllers\Sucursales\Repositories;

use App\Models\Sucursales\Sucursal;
use Illuminate\Support\Facades\Auth;

class SucursalesRepository
{
    public function obtenerSucursalesCliente(int $empresaId)
    {
        return Sucursal::query()
            ->where('empresa_id', $empresaId)
            ->orderByDesc('id')
            ->get();
    }

    public function crearSucursalCliente(array $payload): Sucursal
    {
        return Sucursal::create([
            'empresa_id' => $payload['empresa_id'],
            'codigo' => $payload['codigo'],
            'nombre' => $payload['nombre'],
            'nit' => $payload['nit'],
            'direccion' => $payload['direccion'],
            'telefono' => $payload['telefono'] ?? null,
            'email' => $payload['email'],
            'ciudad_id' => $payload['ciudad_id'],
            'departamento_id' => $payload['departamento_id'],
            'pais_id' => $payload['pais_id'],
            'responsable' => $payload['responsable'],
            'estado' => $payload['estado'] ?? 'activo',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    public function actualizarSucursalCliente(array $payload): Sucursal
    {
        $sucursal = Sucursal::query()
            ->where('empresa_id', $payload['empresa_id'])
            ->where('id', $payload['id'])
            ->firstOrFail();

        $sucursal->update([
            'codigo' => $payload['codigo'],
            'nombre' => $payload['nombre'],
            'nit' => $payload['nit'],
            'direccion' => $payload['direccion'],
            'telefono' => $payload['telefono'] ?? null,
            'email' => $payload['email'],
            'ciudad_id' => $payload['ciudad_id'],
            'departamento_id' => $payload['departamento_id'],
            'pais_id' => $payload['pais_id'],
            'responsable' => $payload['responsable'],
            'estado' => $payload['estado'] ?? $sucursal->estado,
            'updated_by' => Auth::id(),
        ]);

        return $sucursal;
    }

    public function cambiarEstadoSucursalCliente(int $sucursalId, string $estado, int $empresaId): Sucursal
    {
        $sucursal = Sucursal::query()
            ->where('empresa_id', $empresaId)
            ->where('id', $sucursalId)
            ->firstOrFail();

        $sucursal->estado = $estado;
        $sucursal->updated_by = Auth::id();
        $sucursal->save();

        return $sucursal;
    }
}
