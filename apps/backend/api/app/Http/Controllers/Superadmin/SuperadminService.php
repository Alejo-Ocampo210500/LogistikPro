<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Notificaciones\Services\NotificacionesSistemaService;
use App\Models\Empresas\Empresa;
use Illuminate\Support\Facades\Auth;

class SuperadminService
{
    public function __construct(protected NotificacionesSistemaService $notificacionesService) {}

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

        $estadoFinal = strtolower((string) optional($empresa->load('estado')->estado)->nombre);

        if ($estadoFinal === 'inactivo') {
            $this->notificacionesService->registrarEvento('empresa_bloqueada', [
                'empresa_id' => $empresa->id,
                'empresa_nombre' => $empresa->nombre_comercial,
                'usuario_actor_id' => Auth::id(),
                'destino_modulo' => 'empresas-listado',
                'destino_id' => (string) $empresa->id,
            ]);
        } else {
            $this->notificacionesService->registrarEvento('accion_administrativa', [
                'empresa_id' => $empresa->id,
                'empresa_nombre' => $empresa->nombre_comercial,
                'usuario_actor_id' => Auth::id(),
                'mensaje' => 'Se actualizo el estado de la empresa ' . $empresa->nombre_comercial . ' a ' . $estadoFinal . '.',
                'destino_modulo' => 'empresas-listado',
                'destino_id' => (string) $empresa->id,
            ]);
        }

        return response()->json([
            'mensaje' => 'Estado de la empresa actualizado correctamente.',
            'empresa' => $empresa->load(['estado']),
        ], 200);
    }
}
