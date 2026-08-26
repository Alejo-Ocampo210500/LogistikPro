<?php

namespace App\Http\Modules\Auth\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(array $data): array
    {
        $user = User::query()
            ->where('email', $data['email'])
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new AuthenticationException('Correo o contraseña incorrectos.');
        }

        $user->tokens()->update(['revoked' => true]);

        $tokenResult = $user->createToken('logistikpro-web');
        $tokenResult->token->expires_at = now()->addDays(30);
        $tokenResult->token->save();

        return [
            'success' => true,
            'message' => 'Inicio de sesión exitoso.',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token->expires_at,
            'user' => $user,
        ];
    }
}
