<?php

namespace App\Http\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Auth\Request\LoginRequest;
use App\Http\Modules\Auth\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
    ) {}


    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $inicioSesion = $this->authService->login($request->validated());

            return response()->json($inicioSesion, 200);
        } catch (AuthenticationException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 401);
        }
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->token()?->revoke();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}
