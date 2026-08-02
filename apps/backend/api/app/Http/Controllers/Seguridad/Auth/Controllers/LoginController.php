<?php

namespace App\Http\Controllers\Seguridad\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Seguridad\Auth\Repositories\loginRepository;
use App\Http\Controllers\Seguridad\Auth\Request\LoginRequest;
use Exception;

class LoginController extends Controller
{
    public function __construct(protected loginRepository $loginRepository)
    {}

    public function login(LoginRequest $loginRequest)
    {
        try {
            $inicioSesion = $this->loginRepository->login($loginRequest->validated());

            return response()->json($inicioSesion, 200);
        } catch (Exception $exception) {
            return response()->json([
                'mensaje' => $exception->getMessage(),
            ], 401);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'No fue posible iniciar sesión. Inténtalo de nuevo.',
            ], 400);
        }
    }
}
