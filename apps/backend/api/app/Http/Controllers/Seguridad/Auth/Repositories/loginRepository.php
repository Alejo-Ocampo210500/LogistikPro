<?php

namespace App\Http\Controllers\Seguridad\Auth\Repositories;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class loginRepository
{
    public function __construct(protected User $userModel) {}

    public function login(array $data)
    {
        $usuario = $this->userModel
            ->with(['empresa.estado', 'rol', 'estado'])
            ->where('email', $data['email'])
            ->first();

        if (!$usuario) {
            throw new Exception('Las credenciales son incorrectas.');
        }

        if (!$usuario->estado || strtolower($usuario->estado->nombre) !== 'activo') {
            $estadoNombre = $usuario->estado ? $usuario->estado->nombre : 'inactivo';
            throw new Exception("El usuario se encuentra {$estadoNombre}.");
        }

        if (!$usuario->empresa || !$usuario->empresa->estado || strtolower($usuario->empresa->estado->nombre) !== 'activo') {
            $estadoNombre = ($usuario->empresa && $usuario->empresa->estado) ? $usuario->empresa->estado->nombre : 'inactiva';
            throw new Exception("La empresa se encuentra {$estadoNombre}.");
        }

        if (!Hash::check($data['password'], $usuario->password)) {
            throw new Exception('Las credenciales son incorrectas.');
        }

        $token = $usuario->createToken('logistikpro')->plainTextToken;

        $usuario->update([
            'ultimo_acceso' => now()
        ]);

        return [
            'token' => $token,
            'empresa_id' => $usuario->empresa_id,
            'user' => $usuario,
            'empresa' => $usuario->empresa,
            'rol' => $usuario->rol,
        ];
    }
}
