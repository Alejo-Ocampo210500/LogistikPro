<?php

namespace App\Http\Controllers\UnidadesMedida\Repositories;

use App\Models\UnidadMedida\UnidadMedida;
use Illuminate\Support\Facades\Auth;

class UnidadesMedidasRepository
{
    public function obtenerUnidadesMedidaCliente(int $empresaId)
    {
        return UnidadMedida::query()
            ->where('empresa_id', $empresaId)
            ->orderByDesc('id')
            ->get();
    }

    public function crearUnidadMedidaCliente(int $empresaId, array $data)
    {
        $unidadMedida = new UnidadMedida();
        $unidadMedida->empresa_id = $empresaId;
        $unidadMedida->nombre = $data['nombre'];
        $unidadMedida->descripcion = $data['descripcion'] ?? null;
        $unidadMedida->abreviatura = $data['abreviatura'] ?? null;
        $unidadMedida->estado_id = (int) ($data['estado_id'] ?? 1);
        $unidadMedida->creado_por = Auth::id();
        $unidadMedida->actualizado_por = Auth::id();

        $unidadMedida->save();

        return $unidadMedida;
    }

    public function actualizarUnidadMedidaCliente(int $empresaId, int $unidadId, array $data)
    {
        $unidadMedida = UnidadMedida::query()
            ->where('id', $unidadId)
            ->where('empresa_id', $empresaId)
            ->first();

        if (!$unidadMedida) {
            return null;
        }

        $unidadMedida->nombre = $data['nombre'];
        $unidadMedida->descripcion = $data['descripcion'] ?? null;
        $unidadMedida->abreviatura = $data['abreviatura'] ?? null;
        $unidadMedida->estado_id = (int) ($data['estado_id'] ?? $unidadMedida->estado_id ?? 1);
        $unidadMedida->actualizado_por = Auth::id();

        $unidadMedida->save();

        return $unidadMedida;
    }

    public function cambiarEstadoUnidadMedidaCliente(int $empresaId, int $unidadId, array $data)
    {
        $unidadMedida = UnidadMedida::query()
            ->where('id', $unidadId)
            ->where('empresa_id', $empresaId)
            ->first();

        if (!$unidadMedida) {
            return null;
        }

        $estadoSolicitado = isset($data['estado_id']) ? (int) $data['estado_id'] : 0;
        $unidadMedida->estado_id = $estadoSolicitado > 0
            ? $estadoSolicitado
            : ((int) $unidadMedida->estado_id === 1 ? 2 : 1);
        $unidadMedida->actualizado_por = Auth::id();
        $unidadMedida->save();

        return $unidadMedida;
    }
}
