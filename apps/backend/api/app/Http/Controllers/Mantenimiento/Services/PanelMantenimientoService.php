<?php

namespace App\Http\Controllers\Mantenimiento\Services;

use App\Models\Estados\Estado;
use App\Models\User;

class PanelMantenimientoService
{

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
