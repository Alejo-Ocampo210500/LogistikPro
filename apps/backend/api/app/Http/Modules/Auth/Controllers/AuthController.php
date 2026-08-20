<?php

namespace App\Http\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Auth\Request\LoginRequest;
use App\Http\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;

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
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'No fue posible iniciar sesión. Inténtalo de nuevo.',
            ], 400);
        }
    }
}
