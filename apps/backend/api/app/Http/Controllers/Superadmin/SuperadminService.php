<?php

namespace App\Http\Controllers\Superadmin;

use App\Models\Empresas\Empresa;

class SuperadminService
{
    public function cambiarEstado(array $data)
    {
        $empresa = $data['empresa'] ?? null;

        if (!$empresa instanceof Empresa && isset($data['empresa_id'])) {
            $empresa = Empresa::find($data['empresa_id']);
        }

        if (!$empresa) {
            return response()->json([
                'mensaje' => 'Empresa no encontrada.',
            ], 404);
        }

        $estadoActivo = \App\Models\Estados\Estado::where('nombre', 'Activo')->first();
        $estadoInactivo = \App\Models\Estados\Estado::where('nombre', 'inactivo')->first();

        if (isset($data['estado_id'])) {
            $nuevoEstadoId = $data['estado_id'];
        } else {
            $nuevoEstadoId = ($empresa->estado_id === $estadoActivo?->id) ? $estadoInactivo?->id : $estadoActivo?->id;
        }

        $empresa->estado_id = $nuevoEstadoId;
        $empresa->save();

        return response()->json([
            'mensaje' => 'Estado de la empresa actualizado correctamente.',
            'empresa' => $empresa->load(['estado']),
        ], 200);
    }
}
