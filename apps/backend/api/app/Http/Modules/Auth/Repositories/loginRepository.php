<?php

namespace App\Http\Modules\Auth\Repositories;

use Illuminate\Support\Facades\Auth;

class loginRepository
{
    public function login(array $data)
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        if (!auth()->attempt($credentials)) {
            throw new \Exception('Credenciales inválidas');
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ];
    }

}
