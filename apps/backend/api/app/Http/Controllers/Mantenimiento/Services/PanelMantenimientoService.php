<?php

namespace App\Http\Controllers\Mantenimiento\Services;

use App\Http\Controllers\Notificaciones\Services\NotificacionesSistemaService;
use App\Models\Estados\Estado;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PanelMantenimientoService
{
    public function __construct(protected NotificacionesSistemaService $notificacionesService) {}

    /**
     * Funcion del servicio que actualiza los datos del usuario
     * del modulo de mantenimiento - Actualizar usuario global
     *
     * @param $usuarioId
     * @author Alejandro Ocampo
     */
    public function actualizarUsuarioGlobal($usuarioId, $data)
    {
        $usuario = User::findOrFail($usuarioId);
        $payload = [

            'nombre' => $data['nombre'],

            'apellido' => $data['apellido'],

            'telefono' => $data['telefono'],

            'email' => $data['email'],

            'estado_id' => $data['estado_id'],

        ];

        if (!empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $usuario->update($payload);

        $this->notificacionesService->registrarEvento('accion_administrativa', [
            'empresa_id' => $usuario->empresa_id,
            'empresa_nombre' => $usuario->empresa?->nombre_comercial ?? 'Empresa',
            'usuario_actor_id' => Auth::id(),
            'mensaje' => 'Se actualizo el usuario global ' . trim($usuario->nombre . ' ' . $usuario->apellido) . '.',
            'destino_modulo' => 'usuarios-globales',
            'destino_id' => (string) $usuario->id,
        ]);

        return [
            'mensaje' => 'Usuario actualizado correctamente',
            'usuario' => $usuario
        ];
    }

    /**
     * Funcion del servicio que obtiene los estados
     * del modulo de mantenimiento - Obtener estados
     *
     * @author Alejandro Ocampo
     */
    public function obtenerEstados()
    {
        return Estado::all();
    }
}
